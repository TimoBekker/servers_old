<?php

namespace app\models\relation;

use Yii;
use app\models\registry\RSoftwareInstalled;
use app\models\registry\RInformationSystem;

/**
 * This is the model class for table "nn_is_softinstall".
 *
 * @property string $id
 * @property string $information_system
 * @property string $software_installed
 *
 * @property RSoftwareInstalled $softwareInstalled
 * @property RInformationSystem $informationSystem
 */
class NnIsSoftinstall extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_is_softinstall';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['information_system', 'software_installed'], 'required'],
            [['information_system', 'software_installed'], 'integer'],
            [['information_system', 'software_installed'], 'unique', 'targetAttribute' => ['information_system', 'software_installed'], 'message' => 'The combination of Information System and Software Installed has already been taken.']
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
            'software_installed' => 'Software Installed',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSoftwareInstalled()
    {
        return $this->hasOne(RSoftwareInstalled::className(), ['id' => 'software_installed']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInformationSystem()
    {
        return $this->hasOne(RInformationSystem::className(), ['id' => 'information_system']);
    }
}
