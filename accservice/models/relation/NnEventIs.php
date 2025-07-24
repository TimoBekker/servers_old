<?php

namespace app\models\relation;

use Yii;
use app\models\registry\RInformationSystem;

/**
 * This is the model class for table "nn_event_is".
 *
 * @property string $id
 * @property string $event
 * @property string $information_system
 *
 * @property REvent $event0
 * @property RInformationSystem $informationSystem
 */
class NnEventIs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_event_is';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event', 'information_system'], 'required'],
            [['event', 'information_system'], 'integer'],
            [['event', 'information_system'], 'unique', 'targetAttribute' => ['event', 'information_system'], 'message' => 'The combination of Event and Information System has already been taken.']
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
            'information_system' => 'Information System',
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
    public function getInformationSystem()
    {
        return $this->hasOne(RInformationSystem::className(), ['id' => 'information_system']);
    }
}
