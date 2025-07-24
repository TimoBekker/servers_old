<?php

namespace app\models\registry;

use Yii;
use app\models\reference\CTypeEvent;
use app\models\relation\NnEventEquipment;
use app\models\relation\NnEventIs;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "r_event".
 *
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $date_begin
 * @property string $date_end
 *
 * @property NnEventEquipment[] $nnEventEquipments
 * @property NnEventIs[] $nnEventIs
 * @property CTypeEvent $type0
 */
class REvent extends \yii\db\ActiveRecord
{
    public function __construct($config = []){
        // подписываемся на события перед добавлением и редактированием записи
        $this->on(parent::EVENT_AFTER_INSERT, [$this, 'afterInsert']);
        $this->on(parent::EVENT_AFTER_UPDATE, [$this, 'afterUpdate']);
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'type', 'date_begin', 'date_end'], 'required'],
            [['type'], 'integer'],
            [['date_begin', 'date_end'], 'safe'],
            [['name'], 'string', 'max' => 512],
            [['description'], 'string', 'max' => 1024],
        ];
    }

    // преобразуем дату перед сохранением. обработчик события EVENT_AFTER_VALIDATE
    public function afterValidate(){
        parent::afterValidate();
        if($this->date_begin)
            $this->date_begin = (new \Datetime($this->date_begin))->format('Y-m-d H:i:s');
        if($this->date_end)
            $this->date_end = (new \Datetime($this->date_end))->format('Y-m-d H:i:s');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'type' => 'Тип',
            'description' => 'Описание',
            'date_begin' => 'Начало',
            'date_end' => 'Окончание',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNnEventEquipments()
    {
        return $this->hasMany(NnEventEquipment::className(), ['event' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNnEventIs()
    {
        return $this->hasMany(NnEventIs::className(), ['event' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(CTypeEvent::className(), ['id' => 'type']);
    }

    // обработчики стандартных событий после сохранения у нас будет доступен id
    public function afterInsert(){
        // var_dump(Yii::$app->request->post());exit;
        // уникалим полученный список айдишников по моделям + добавляем id-ки
        // + здесь еще надо изабав. от пустых,потому что могут быть НЕТ значения. Исп. array_filter
        // используем array_values, поскольку массовая валидация
        // validateMultiple работает, только если у нас массив 0,1,2,3,...
        $uniquesEquipes = array_values(array_unique(array_filter(ArrayHelper::getColumn(
            Yii::$app->request->post('NnEventEquipment'), 'equipment'
        ), function($el){ return !empty($el); })));
        $arrParams['NnEventEquipment'] = array_map(function($val) {
           return ['equipment' => (int)$val, 'event' => (int)$this->id];
        }, $uniquesEquipes);
        $arrNEEModels = [];
        foreach ($arrParams['NnEventEquipment'] as $val) {
           $arrNEEModels[] = new NnEventEquipment();
        }
        // var_dump($arrNEEModels);
        // var_dump($arrParams['NnEventEquipment']);
        // var_dump(NnEventEquipment::loadMultiple($arrNEEModels, $arrParams));
        // var_dump(NnEventEquipment::validateMultiple($arrNEEModels));
        // exit;
        if(NnEventEquipment::loadMultiple($arrNEEModels, $arrParams)
                && NnEventEquipment::validateMultiple($arrNEEModels)){
            foreach ($arrNEEModels as $NEEMModel) {
                $NEEMModel->save(false);
            }
        }

        $uniquesInfosysts = array_values(array_unique(array_filter(ArrayHelper::getColumn(
            Yii::$app->request->post('NnEventIs'), 'information_system'
        ), function($el){ return !empty($el); })));
        $arrParams['NnEventIs'] = array_map(function($val) {
           return ['information_system' => (int)$val, 'event' => (int)$this->id];
        }, $uniquesInfosysts);
        $arrNEIModels = [];
        foreach ($arrParams['NnEventIs'] as $val) {
           $arrNEIModels[] = new NnEventIs();
        }
        if(NnEventIs::loadMultiple($arrNEIModels, $arrParams)
                && NnEventIs::validateMultiple($arrNEIModels)){
            foreach ($arrNEIModels as $NEIMModel) {
                $NEIMModel->save(false);
            }
        }
    }

    public function afterUpdate(){
        // вначале удалим все данные из таблиц связей
        $deletedidEquip = [];
        foreach ($this->nnEventEquipments as $v) {
            $deletedidEquip[] = $v->id; // айдишники в таблице связи c оборудованием
        }
        NnEventEquipment::deleteAll(['id' => $deletedidEquip]);
        $deletedidInfosys = [];
        foreach ($this->nnEventIs as $v) {
            $deletedidInfosys[] = $v->id; // айдишники в таблице связи с ифосистемами
        }
        NnEventIs::deleteAll(['id' => $deletedidInfosys]);
        // echo $this->id;
        // Потом вызыаем метод afterInsert()
        $this->afterInsert();
    }

    // для логирования
    private $tempDelListEquip = '';
    private $tempDelListIs = '';
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $arrNamesEquip = [];
            foreach ($this->nnEventEquipments as $val) {
                $arrNamesEquip[] = $val->equipment0->name;
            }
            $this->tempDelListEquip = $arrNamesEquip ? join($arrNamesEquip, ', ') : '(не заданы)';
            $arrNamesIs = [];
            foreach ($this->nnEventIs as $val) {
                $arrNamesIs[] = $val->informationSystem->name_short;
            }
            $this->tempDelListIs = $arrNamesIs ? join($arrNamesIs, ', ') : '(не заданы)';
            return true;
        } else {
            return false;
        }

    }
    public function afterDelete()
    {
        parent::afterDelete();
        // логируем удаление
        $forLogUserModel = \app\models\User::findOne(Yii::$app->user->id);
        $forLogUserName = $forLogUserModel->last_name.' '.$forLogUserModel->first_name;
        Yii::$app->systemlog->dolog(Yii::$app->user->id, 701,
            "Пользователь {$forLogUserName} удалил событие обслуживания: {$this->name}
            для оборудования: {$this->tempDelListEquip} и информационных систем: {$this->tempDelListIs}");
    }


    // возвратить список событий для главной страницы
    public function listEventsForIndex(){
        date_default_timezone_set('europe/samara');
        $now = date("Y-m-d H:i:s");
        // var_dump($now);exit;
        // 3 последних прошедших события
        $res['prevent'] = $this->find()
            ->where(['<', 'date_end', $now])
            ->orderBy('date_end DESC')
            ->limit(3)
            // ->asArray()
            ->all();
        $res['prevent'] = array_reverse($res['prevent']);

        // все текущие события
        $res['current'] = $this->find()
            ->Where(['<', 'date_begin', $now])
            ->andWhere(['>', 'date_end', $now])
            ->orderBy('date_begin')
            // ->asArray()
            ->all();

        // 2 будущих события
        $res['next'] = $this->find()
            ->where(['>', 'date_begin', $now])
            ->orderBy('date_begin ASC')
            ->limit(2)
            // ->asArray()
            ->all();

        return $res;
    }
}
