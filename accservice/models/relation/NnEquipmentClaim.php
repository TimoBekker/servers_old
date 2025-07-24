<?php

namespace app\models\relation;

use Yii;
use app\models\reference\SClaim;
use app\models\registry\REquipment;

/**
 * This is the model class for table "nn_equipment_claim".
 *
 * @property string $id
 * @property string $equipment
 * @property string $claim
 *
 * @property REquipment $equipment0
 * @property SClaim $claim0
 */
class NnEquipmentClaim extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_equipment_claim';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment', 'claim'], 'required'],
            [['equipment', 'claim'], 'integer'],
            [['equipment', 'claim'], 'unique', 'targetAttribute' => ['equipment', 'claim'], 'message' =>
                                                            'Комбинация Оборудования и Заявки должна быть Уникальна']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment' => 'Equipment',
            'claim' => 'Наименование',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment0()
    {
        return $this->hasOne(REquipment::className(), ['id' => 'equipment']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClaim0()
    {
        return $this->hasOne(SClaim::className(), ['id' => 'claim']);
    }
}
