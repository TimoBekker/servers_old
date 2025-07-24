<?php

namespace app\models\relation;

use Yii;
use app\models\registry\RInformationSystem;
use app\models\registry\RSoftware;

/**
 * This is the model class for table "nn_is_software".
 *
 * @property string $id
 * @property string $information_system
 * @property string $software
 *
 * @property RInformationSystem $informationSystem
 * @property RSoftware $software0
 */
class NnIsSoftware extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_is_software';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['information_system', 'software'], 'required'],
            [['information_system', 'software'], 'integer'],
            [['information_system', 'software'], 'unique', 'targetAttribute' => ['information_system', 'software'], 'message' => 'The combination of Information System and Software has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'information_system' => 'Information System',
            'software' => 'Software',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInformationSystem()
    {
        return $this->hasOne(RInformationSystem::className(), ['id' => 'information_system']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSoftware0()
    {
        return $this->hasOne(RSoftware::className(), ['id' => 'software']);
    }
}
