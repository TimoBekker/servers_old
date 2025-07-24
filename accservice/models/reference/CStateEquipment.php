<?php

namespace app\models\reference;

use Yii;

/**
 * This is the model class for table "c_state_equipment".
 *
 * @property integer $id
 * @property string $name
 *
 * @property REquipment[] $rEquipments
 */
class CStateEquipment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_state_equipment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getREquipments()
    {
        return $this->hasMany(REquipment::className(), ['state' => 'id']);
    }
}
