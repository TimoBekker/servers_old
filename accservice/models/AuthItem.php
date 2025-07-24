<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use app\components\utils\Utils;
use app\models\AuthItemChild;
use app\models\relation\NnAuthitemEquipment;
use app\models\relation\NnAuthitemIs;
use app\models\relation\NnAuthitemSoftware;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $alias
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemParents
 * @property NnAuthitemEquipment[] $nnAuthitemEquipments
 * @property NnAuthitemIs[] $nnAuthitemIs
 * @property NnAuthitemSoftware[] $nnAuthitemSoftwares
 */

/*
    Группы в базе имеют типы в зависимости от предназначения
    1,4 - хранители правил, где
        1 - Доступные для присвоения из интерфейса
        4 - Задаваемые молча. Такие как default,
    2 - простые группы
    3 - группы-пользователи
*/
class AuthItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['type', 'default', 'value' => 2],
            [[/*'name', 'type',*/ 'alias'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name', 'alias'], 'string', 'max' => 64]
        ];
    }

    /*
        Функция вернет массив имен (auth_item.name) записей таблицы auth_item
        с auth_item.type = 1, доступных для присвоения простым группам
        в качестве правил в интерфейсе системы.
    */
    /*public function getAvailableUserRulesNames(){
        return ...
    }*/

    /*
        Перед сохранением
    */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Делаем необходимые преобразования при добавлении группы
            // При пересохранении в данном случае эти данные уже меняться не будут
            if($insert){
                $this->type = 2; // Из интерфейса системы мы можем создать группы только с типом 2 - Простые группы.
                $this->name = Utils::transliteration($this->alias);
            }
            return true;
        } else {
            return false;
        }
    }

    /*
        После сохранения
    */
    public $needRollback = false; // для отмены сохранения транзакции

    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

        // header('Content-Type: text/html; charset=utf-8');
        // echo '<pre style = "margin-top:100px">';

        // var_dump(Yii::$app->request->post('ItemChildren')); // либо null, либо array

        // Объекты, входящие в группу
        if ($this->type == 2)
            $this->_asItemParents($insert);

        // echo '</pre>';
        // return $this->needRollback = true;
    }

    // _Объекты, входящие в группу
    private function _asItemParents($insert){
        // очищение
        if(!$insert){
            // Удаляем все связи с зависимыми группами
            // По схеме с чекбоксами - они либо приходят в массиве, либо придет Null
            // Поэтому проверку на наличие поста смысла делать проверку тут нет.
            // т.о. если пришел Null по какой-либо причине, то считаем, что мы просто
            // не выбрали ни одной галочки
            AuthItemChild::deleteAll(['child' => $this->name]);
            // todo: возможно надо будет доп. type = 2 .Но пока архитектура такая, что таких зависи
            // пересечений не должно быть.
            // В child будут только группы с type 2 и type 1. Но группу с type 1 мы из формы редактирования гр.
            // никогда не будем (не должны по замыслу архитектуры) модифицировать.
            // целесообразно просто добавит 2 для защиты от попытки удаления ненужной записи через передачю ложного
            // name группы в форме.
            // Эту проверку я сделал выше при вызове данной функции. В противном случае мы просто не обработаем запрос
        }
        // добавление
        if($arrAuthItemChild = Yii::$app->request->post('ItemParents')){
            // array_walk - немного по другой схеме
            array_walk($arrAuthItemChild, function(&$value, $key){
                $temp = ['child' => $this->name, 'parent' => $value];
                $value = $temp;
            });
            $arrAuthItemChildModels = [];
            foreach ($arrAuthItemChild as $val) {
               $arrAuthItemChildModels[] = new AuthItemChild();
            }
            if( AuthItemChild::loadMultiple($arrAuthItemChildModels, ['AuthItemChild' => array_values($arrAuthItemChild)])
                    && AuthItemChild::validateMultiple($arrAuthItemChildModels)){
                foreach ($arrAuthItemChildModels as $AuthItemChildModel) {
                    $AuthItemChildModel->save(false);
                }
            }
        }
        // var_dump($arrAuthItemChild);
    }

    /*
        Сохранение доступных прав
        Логика сохранения.
        0. Если по каким либо причинам приходит пустой пост запрос через ($data['ItemRights']) то считаем, что
            просто сброшены все записи и будем удалять все записи.
        1. Удалить все связи parent-child, где parent = $this->name и child.type = 1.
        2. Добавить связи parent-child, где parent = $this->name и child = $data['ItemRights']
    */
    public function saveAvailableUserRules($data)
    {
        // удаление - очистка
        $delNames = $this->find()
            ->select('name')
            ->where(['type' => 1])
            ->asArray('name')
            ->All();
        // var_dump($delNames);
        AuthItemChild::deleteAll(['parent' => $this->name, 'child' => ArrayHelper::getColumn($delNames, 'name')]);

        // добавление
        if (!isset($data['ItemRights'])) return;

        $arrAuthItemChild = $data['ItemRights'];
        array_walk($arrAuthItemChild, function(&$value, $key){
            $temp = ['parent' => $this->name, 'child' => $value];
            $value = $temp;
        });
        $arrAuthItemChildModels = [];
        foreach ($arrAuthItemChild as $val) {
           $arrAuthItemChildModels[] = new AuthItemChild();
        }
        // var_dump($arrAuthItemChild);exit;
        if( AuthItemChild::loadMultiple($arrAuthItemChildModels, ['AuthItemChild' => array_values($arrAuthItemChild)])
                && AuthItemChild::validateMultiple($arrAuthItemChildModels)){
            foreach ($arrAuthItemChildModels as $AuthItemChildModel) {
                $AuthItemChildModel->save(false);
            }
        }
    }

    /*
        Сохранение связей групп с объектами - Сервера, ИС, ПО
        Логика сохранения:
        1. Если по каким либо причинам не приходят в посте нужные данные, то просто считаем, что они все сброшены
        2. Удаляем все связи auth_item-объект
        3. Сохраняем новые связи
    */
    public function saveEntytiesRelation($data)
    {
        // echo "<pre style='margin-top:60px'>";
        // var_dump($data);
        // echo "</pre>";
        // Оборудование - удаление
        NnAuthitemEquipment::deleteAll(['auth_item' => $this->name]);
        // Оборудование - сохранение
        if (!empty($data['ItemEquipments'])) {
            $arrNnAuthitemEquipment = $data['ItemEquipments'];
            array_walk($arrNnAuthitemEquipment, function(&$value, $key){
                $temp = ['auth_item' => $this->name, 'equipment' => $value];
                $value = $temp;
            });
            $arrNnAuthitemEquipmentModels = [];
            foreach ($arrNnAuthitemEquipment as $val) {
               $arrNnAuthitemEquipmentModels[] = new NnAuthitemEquipment();
            }
            if( NnAuthitemEquipment::loadMultiple($arrNnAuthitemEquipmentModels, ['NnAuthitemEquipment' => array_values($arrNnAuthitemEquipment)])
                    && NnAuthitemEquipment::validateMultiple($arrNnAuthitemEquipmentModels)){
                foreach ($arrNnAuthitemEquipmentModels as $NnAuthitemEquipmentModel) {
                    $NnAuthitemEquipmentModel->save(false);
                }
            }
        }
        // Информационные системы - удаление
        NnAuthitemIs::deleteAll(['auth_item' => $this->name]);
        // Информационные системы - сохранение
        if (!empty($data['ItemInfosys'])) {
            $arrNnAuthitemIs = $data['ItemInfosys'];
            array_walk($arrNnAuthitemIs, function(&$value, $key){
                $temp = ['auth_item' => $this->name, 'information_system' => $value];
                $value = $temp;
            });
            $arrNnAuthitemIsModels = [];
            foreach ($arrNnAuthitemIs as $val) {
               $arrNnAuthitemIsModels[] = new NnAuthitemIs();
            }
            if( NnAuthitemIs::loadMultiple($arrNnAuthitemIsModels, ['NnAuthitemIs' => array_values($arrNnAuthitemIs)])
                    && NnAuthitemIs::validateMultiple($arrNnAuthitemIsModels)){
                foreach ($arrNnAuthitemIsModels as $NnAuthitemIsModel) {
                    $NnAuthitemIsModel->save(false);
                }
            }
        }
        // Программное обеспечение - удаление
        NnAuthitemSoftware::deleteAll(['auth_item' => $this->name]);
        // Программное обеспечение - сохранение
        if (!empty($data['ItemSoft'])) {
            $arrNnAuthitemSoftware = $data['ItemSoft'];
            array_walk($arrNnAuthitemSoftware, function(&$value, $key){
                $temp = ['auth_item' => $this->name, 'software' => $value];
                $value = $temp;
            });
            $arrNnAuthitemSoftwareModels = [];
            foreach ($arrNnAuthitemSoftware as $val) {
               $arrNnAuthitemSoftwareModels[] = new NnAuthitemSoftware();
            }
            if( NnAuthitemSoftware::loadMultiple($arrNnAuthitemSoftwareModels, ['NnAuthitemSoftware' => array_values($arrNnAuthitemSoftware)])
                    && NnAuthitemSoftware::validateMultiple($arrNnAuthitemSoftwareModels)){
                foreach ($arrNnAuthitemSoftwareModels as $NnAuthitemSoftwareModel) {
                    $NnAuthitemSoftwareModel->save(false);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Наименование', // При добавлении будет заполняться трансилтерацией из алиаса
            'type' => 'Type',
            'description' => 'Описание',
            'rule_name' => 'Rule Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'alias' => 'Наименование', // При создании будет заполняться только эта штука
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemParents()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNnAuthitemEquipments()
    {
        return $this->hasMany(NnAuthitemEquipment::className(), ['auth_item' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNnAuthitemIs()
    {
        return $this->hasMany(NnAuthitemIs::className(), ['auth_item' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNnAuthitemSoftwares()
    {
        return $this->hasMany(NnAuthitemSoftware::className(), ['auth_item' => 'name']);
    }

    /*
        Проверка, принадлежит ли оборудование с указанным id группе
        параметр int $id
    */
    public function isMyEquip($id)
    {
        if ( !is_numeric($id) || $id <= 0 || !$this->nnAuthitemEquipments) return false;
        return in_array($id, ArrayHelper::getColumn($this->nnAuthitemEquipments, 'equipment'));
    }
    /*
        Проверка, принадлежит ли информационная система с указанным id группе
        параметр int $id
    */
    public function isMyInfoSystem($id)
    {
        if ( !is_numeric($id) || $id <= 0 || !$this->nnAuthitemIs) return false;
        return in_array($id, ArrayHelper::getColumn($this->nnAuthitemIs, 'information_system'));
    }
    /*
        Проверка, принадлежит ли дистрибутив с указанным id группе
        параметр int $id
    */
    public function isMySoftware($id)
    {
        if ( !is_numeric($id) || $id <= 0 || !$this->nnAuthitemSoftwares) return false;
        return in_array($id, ArrayHelper::getColumn($this->nnAuthitemSoftwares, 'software'));
    }
    /*
        Проверка, связано ли оборудование с какой-либо из данного набора групп. Если да, то вернет true
    */
    public static function isEquipInGroups($id, array $item_names)
    {
        foreach ($item_names as $itemname) {
            $item = self::findOne($itemname);
            if( $item->isMyEquip($id) )
                return true;
        }
        return false;
    }
    /*
        Проверка, связана ли информационная система с какой-либо из данного набора групп. Если да, то вернет true
    */
    public static function isInfosysInGroups($id, array $item_names)
    {
        foreach ($item_names as $itemname) {
            $item = self::findOne($itemname);
            if( $item->isMyInfoSystem($id) )
                return true;
        }
        return false;
    }
    /*
        Проверка, связан ли дистрибутив ПО с какой-либо из данного набора групп. Если да, то вернет true
    */
    public static function isSoftInGroups($id, array $item_names)
    {
        foreach ($item_names as $itemname) {
            $item = self::findOne($itemname);
            if( $item->isMySoftware($id) )
                return true;
        }
        return false;
    }

    /*
        Функции, возвращающие массивы статистический данных о Группе
    */

    /*
        Возвращает список родительских групп (рекурсивно связанных).Прим.помним об инвер-сти назв. столбцов в таб. связи
    */
    public function getRecursiveParents()
    {
        return \Yii::$app->db->createCommand(
            'SELECT aic2.child
               FROM auth_item_child aic1, auth_item_child aic2
              WHERE aic1.parent = :name
                    AND
                    aic1.child = aic2.parent
              UNION
             SELECT child
               FROM auth_item_child
              WHERE parent = :name'
        )
        ->bindValue(':name', $this->name)
        ->queryColumn();
    }
    /*
        Возвращает список дочерних групп (рекурсивно связанных).Прим.помним об инвер-сти назв. столбцов в таб. связи
    */
    public function getRecursiveChildren()
    {
        return \Yii::$app->db->createCommand(
            'SELECT aic2.parent
               FROM auth_item_child aic1, auth_item_child aic2
              WHERE aic1.child = :name
                    AND
                    aic1.parent = aic2.child
              UNION
             SELECT parent
               FROM auth_item_child
              WHERE child = :name'
        )
        ->bindValue(':name', $this->name)
        ->queryColumn();
    }
    /*
        Вернуть список правил для данной группы
    */
    public function getAviableRules()
    {
        /*return self::find()
            ->select('aic.child')
            ->join('RIGHT JOIN', 'auth_item_child aic', 'aic.child = auth_item.name')
            ->where(['auth_item.type' => 1, 'aic.parent' => $this->name])
            ->asArray('aic.child')
            ->All();*/
        $arrAIC = $this->authItemChildren;
        foreach ($arrAIC as $key => $aic) {
            if ($aic->child0->type != 1) unset($arrAIC[$key]);
        }
        if (!empty($arrAIC))
            return ArrayHelper::getColumn($arrAIC, 'child');
        else
            return null;
    }
    /*
        Вернуть список правил для данной группы + рекурсивно наследуемые от родительский групп
    */
    public function getAviableRulesRecursive()
    {
        $parentGroupNames = array_merge([$this->name], $this->recursiveParents);
        $arrRes = [];
        // var_dump($arrRes, $parentGroupNames);exit;
        foreach ($parentGroupNames as $grName) {
            $grModel = self::findOne($grName);
            $arrAIC = $grModel->authItemChildren;
            foreach ($arrAIC as $key => $aic) {
                if ($aic->child0->type != 1) unset($arrAIC[$key]);
            }
            if (!empty($arrAIC))
                $arrRes = array_merge($arrRes, ArrayHelper::getColumn($arrAIC, 'child'));
        }

        if (!empty($arrRes))
            return $arrRes;
        else
            return null;
    }
    /*
        Рекурсивный список наследуемого оборудования
    */
    public function getNnAuthitemEquipmentsRecursive()
    {
        $withParentGroupNames = array_merge([$this->name], $this->recursiveParents);
        return NnAuthitemEquipment::find()
            ->select(new Expression("
                equipment,
                ANY_VALUE(id) id, 
                ANY_VALUE(auth_item) auth_item
            "))
            ->where(['auth_item' => $withParentGroupNames])->groupBy('equipment')->All();
    }
    /*
        Рекурсивный список наследуемых информационных систем
    */
    public function getNnAuthitemIsRecursive()
    {
        $withParentGroupNames = array_merge([$this->name], $this->recursiveParents);
        return NnAuthitemIs::find()
            ->select(new Expression("
                information_system,
                ANY_VALUE(id) id, 
                ANY_VALUE(auth_item) auth_item
            "))
            ->where(['auth_item' => $withParentGroupNames])
            ->groupBy('information_system')
            ->All();
    }
    /*
        Рекурсивный список наследуемого софта
    */
    public function getNnAuthitemSoftwaresRecursive()
    {
        $withParentGroupNames = array_merge([$this->name], $this->recursiveParents);
        return NnAuthitemSoftware::find()
            ->select(new Expression("
                software,
                ANY_VALUE(id) id, 
                ANY_VALUE(auth_item) auth_item
            "))
            ->where(['auth_item' => $withParentGroupNames])
            ->groupBy('software')->All();
    }
}
