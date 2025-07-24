<?php

namespace app\models\relation;

use app\models\reference\SContourInformationSystem;
use app\models\registry\REquipment;
use app\models\registry\RInformationSystem;

/**
 * This is the model class for table "nn_equipment_infosys_contour".
 *
 * @property string $id
 * @property integer $equipment
 * @property integer $information_system
 * @property integer $contour
 *
 * @property REquipment $equipmentModel
 * @property RInformationSystem $informationSystemModel
 * @property SContourInformationSystem $contourModel
*/

class NnEquipmentInfosysContour extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'nn_equipment_infosys_contour';
    }

    public function rules()
    {
        return [
            [['equipment', 'information_system', 'contour'], 'required'],
            [['equipment', 'information_system', 'contour'], 'integer'],
            [['equipment', 'information_system', 'contour'], 'unique', 'targetAttribute' => ['equipment', 'information_system', 'contour'], 'message' =>
                'Комбинация Оборудования, Информ.системы и Контура д.б. Уникальна']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment' => 'Оборудование',
            'information_system' => 'Информационная система',
            'contour' => 'Контур',
        ];
    }

    public function getEquipmentModel()
    {
        return $this->hasOne(REquipment::class, ['id' => 'equipment']);
    }

    public function getInformationSystemModel()
    {
        return $this->hasOne(RInformationSystem::class, ['id' => 'information_system']);
    }

    public function getContourModel()
    {
        return $this->hasOne(SContourInformationSystem::class, ['id' => 'contour']);
    }
}