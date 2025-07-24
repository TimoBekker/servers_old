<?php

namespace app\models\relation;

use Yii;
use app\models\registry\REquipment;

/**
 * This is the model class for table "nn_event_equipment".
 *
 * @property string $id
 * @property string $event
 * @property string $equipment
 *
 * @property REvent $event0
 * @property REquipment $equipment0
 */
class NnEventEquipment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_event_equipment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event', 'equipment'], 'required'],
            [['event', 'equipment'], 'integer'],
            [['event', 'equipment'], 'unique', 'targetAttribute' => ['event', 'equipment'], 'message' => 'The combination of Event and Equipment has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event' => 'Event',
            'equipment' => 'Equipment',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent0()
    {
        return $this->hasOne(REvent::className(), ['id' => 'event']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment0()
    {
        return $this->hasOne(REquipment::className(), ['id' => 'equipment']);
    }
}
