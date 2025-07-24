<?php

namespace app\models\reference;

use Yii;
use app\models\registry\REquipment;

/**
 * This is the model class for table "s_placement".
 *
 * @property string $id
 * @property string $region
 * @property string $city
 * @property string $street
 * @property string $house
 * @property string $office
 * @property string $locker
 * @property string $shelf
 *
 * @property REquipment[] $rEquipments
 */
class SPlacement extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Места размещения";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_placement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region', 'city', 'street', 'house'/*, 'office', 'locker', 'shelf'*/], 'required'],
            [['region', 'street'], 'string', 'max' => 255],
            [['city', 'house', 'office', 'locker', 'shelf'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region' => 'Регион/Область',
            'city' => 'Город',
            'street' => 'Улица',
            'house' => 'Дом',
            'office' => 'Офис/Кабинет',
            'locker' => 'Шкаф',
            'shelf' => 'Unit (снизу)',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getREquipments()
    {
        return $this->hasMany(REquipment::className(), ['placement' => 'id']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->rEquipments )
            return false;
        $this->delete();
        return true;
    }

    public function getDisplay()
    {
        $office = $this->office ? ", $this->office" : '';
        $locker = $this->locker ? ", $this->locker" : '';
        $shelf = $this->shelf ? ", $this->shelf" : '';
        $template = "{$this->region} {$this->city}, {$this->street}, {$this->house}$office$locker$shelf";
        return $template;
    }
}
