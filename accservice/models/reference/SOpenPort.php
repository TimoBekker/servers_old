<?php

namespace app\models\reference;

use Yii;
use app\models\reference\CProtocol;
use app\models\registry\REquipment;

/**
 * This is the model class for table "s_open_port".
 *
 * @property string $id
 * @property integer $equipment
 * @property integer $port_number
 * @property integer $protocol
 * @property string $description
 *
 * @property REquipment $equipment0
 * @property CProtocol $protocol0
 */
class SOpenPort extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Открытые порты оборудования";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_open_port';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment', 'port_number', 'protocol'], 'required'],
            [['equipment', 'port_number', 'protocol'], 'integer'],
            [['port_number'], 'integer', 'max' => 65535],
            [['description'], 'string', 'max' => 255],
            [['equipment', 'port_number'], 'unique', 'targetAttribute' => ['equipment', 'port_number'],
                                            'message' => 'Комбинация оборудования и номера порта должна быть уникальна']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment' => 'Оборудование',
            'port_number' => 'Номер порта',
            'protocol' => 'Протокол',
            'description' => 'Описание',
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
    public function getProtocol0()
    {
        return $this->hasOne(CProtocol::className(), ['id' => 'protocol']);
    }
}
