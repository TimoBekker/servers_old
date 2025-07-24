<?php

namespace app\models\registry;

use Yii;
use app\models\registry\RSoftware;
use app\models\registry\REquipment;
use app\models\relation\NnIsSoftinstall;
use app\models\relation\NnSoftinstallSoftinstall;
use yii\helpers\ArrayHelper;
use app\components\utils\Utils;
use yii\web\UploadedFile;

/**
 * This is the model class for table "r_software_installed".
 *
 * @property string $id
 * @property string $software
 * @property string $equipment
 * @property string $description
 * @property string $date_commission
 * @property string $bitrate
 *
 * @property NnIsSoftinstall[] $nnIsSoftinstalls
 * @property NnSoftinstallSoftinstall[] $nnSoftinstallSoftinstalls
 * @property RSoftware $software0
 * @property REquipment $equipment0
 */
class RSoftwareInstalled extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_software_installed';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['software', 'equipment'/*, 'description', 'date_commission'*/], 'required'],
            [['date_commission'], 'safe'],
            [['date_commission'], 'string', 'max' => 10],
            [['date_commission'], 'date', 'format'=>'dd.MM.yyyy'],
            // [['date_commission'], 'match', 'pattern'=>'/(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}/'],
            // [['date_commission'], 'date', 'format'=>'yyyy-MM-dd'],
            // [['date_commission'], 'match', 'pattern'=>'/[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])/'],
            [['software', 'equipment'], 'integer'],
            [['bitrate'], 'string'],
            [['bitrate'], 'default', 'value' => null],
            [['description'], 'string', 'max' => 2048]
        ];
    }

    // преобразуем дату перед сохранением. обработчик события EVENT_AFTER_VALIDATE
    public function afterValidate(){
        parent::afterValidate();
        if($this->date_commission)
            $this->date_commission = (new \Datetime($this->date_commission))->format('Y-m-d');
    }

    /*
        После сохранения
    */
    public $needRollback = false; // для отмены сохранения транзакции

    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

        // header('Content-Type: text/html; charset=utf-8');
        // echo '<pre style = "margin-top:100px">';

        // Связать с информационной системой
        $this->_asIsSoftinstall($insert);

        // Cвязь с другим установленным ПО
        $this->_asSoftinstallSoftinstall($insert);

        // echo '</pre>';
        // return $this->needRollback = true;
    }

    // _Связать с информационной системой
    private function _asIsSoftinstall($insert){
        // очищение
        if(!$insert){
            // Удаляем все связи с ИС
            if(Yii::$app->request->post('NnIsSoftinstall'))
                NnIsSoftinstall::deleteAll(['software_installed' => $this->id]);
        }
        // добавление
        if(Yii::$app->request->post('NnIsSoftinstall')){
            $arrNnIsSoftinstall = Utils::array_unique_callback(
                Yii::$app->request->post('NnIsSoftinstall'),
                function ($items) {
                     return $items['information_system'];
                },
                true
            );
            array_walk($arrNnIsSoftinstall, function(&$value, $key){$value['software_installed'] =  $this->id;});
            $arrNnIsSoftinstallModels = [];
            foreach ($arrNnIsSoftinstall as $val) {
               $arrNnIsSoftinstallModels[] = new NnIsSoftinstall();
            }
            if( NnIsSoftinstall::loadMultiple($arrNnIsSoftinstallModels, ['NnIsSoftinstall' => array_values($arrNnIsSoftinstall)])
                    && NnIsSoftinstall::validateMultiple($arrNnIsSoftinstallModels)){
                foreach ($arrNnIsSoftinstallModels as $NnIsSoftinstallModel) {
                    $NnIsSoftinstallModel->save(false);
                }
            }
        }
        // var_dump(Yii::$app->request->post('NnIsSoftinstall'));
        // var_dump($arrNnIsSoftinstall);
        // var_dump( NnIsSoftinstall::loadMultiple($arrNnIsSoftinstallModels, ['NnIsSoftinstall' => array_values($arrNnIsSoftinstall)])
        //          && NnIsSoftinstall::validateMultiple($arrNnIsSoftinstallModels));
    }

    // _Cвязь с другим установленным ПО
    private function _asSoftinstallSoftinstall($insert){
        // очищение
        if(!$insert){
            // Удаляем все связи с ИС
            if(Yii::$app->request->post('NnSoftinstallSoftinstall'))
                NnSoftinstallSoftinstall::deleteAll(['software_installed1' => $this->id]);
        }
        // добавление
        if(Yii::$app->request->post('NnSoftinstallSoftinstall')){
            $arrNnSoftinstallSoftinstall = Utils::array_unique_callback(
                Yii::$app->request->post('NnSoftinstallSoftinstall'),
                function ($items) {
                     return $items['software_installed2'];
                },
                true
            );
            array_walk($arrNnSoftinstallSoftinstall, function(&$value, $key){$value['software_installed1'] =  $this->id;});
            $arrNnSoftinstallSoftinstallModels = [];
            foreach ($arrNnSoftinstallSoftinstall as $val) {
               $arrNnSoftinstallSoftinstallModels[] = new NnSoftinstallSoftinstall();
            }
            if( NnSoftinstallSoftinstall::loadMultiple($arrNnSoftinstallSoftinstallModels, ['NnSoftinstallSoftinstall' => array_values($arrNnSoftinstallSoftinstall)])
                    && NnSoftinstallSoftinstall::validateMultiple($arrNnSoftinstallSoftinstallModels)){
                foreach ($arrNnSoftinstallSoftinstallModels as $NnSoftinstallSoftinstallModel) {
                    $NnSoftinstallSoftinstallModel->save(false);
                }
            }
        }
        // var_dump(Yii::$app->request->post('NnSoftinstallSoftinstall'));
        // var_dump($arrNnSoftinstallSoftinstall);
        // var_dump( NnSoftinstallSoftinstall::loadMultiple($arrNnSoftinstallSoftinstallModels, ['NnSoftinstallSoftinstall' => array_values($arrNnSoftinstallSoftinstall)])
        //          && NnSoftinstallSoftinstall::validateMultiple($arrNnSoftinstallSoftinstallModels));
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'software' => 'Программное обеспечение',
            'equipment' => 'Оборудование',
            'description' => 'Описание',
            'date_commission' => 'Дата ввода в эксплуатацию',
            'bitrate' => 'Разрядность',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNnIsSoftinstalls()
    {
        return $this->hasMany(NnIsSoftinstall::className(), ['software_installed' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNnSoftinstallSoftinstalls()
    {
        return $this->hasMany(NnSoftinstallSoftinstall::className(), ['software_installed1' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSoftware0()
    {
        return $this->hasOne(RSoftware::className(), ['id' => 'software']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment0()
    {
        return $this->hasOne(REquipment::className(), ['id' => 'equipment']);
    }
}
