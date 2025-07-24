<?php

namespace app\models\relation;

use Yii;
use app\models\registry\RSoftwareInstalled;

/**
 * This is the model class for table "nn_softinstall_softinstall".
 *
 * @property string $id
 * @property string $software_installed1
 * @property string $software_installed2
 *
 * @property RSoftwareInstalled $softwareInstalled1
 * @property RSoftwareInstalled $softwareInstalled2
 */
class NnSoftinstallSoftinstall extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_softinstall_softinstall';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['software_installed1', 'software_installed2'], 'required'],
            [['software_installed1', 'software_installed2'], 'integer'],
            [['software_installed1', 'software_installed2'], 'unique', 'targetAttribute' => ['software_installed1', 'software_installed2'], 'message' => 'The combination of Software Installed1 and Software Installed2 has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'software_installed1' => 'Software Installed1',
            'software_installed2' => 'Software Installed2',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSoftwareInstalled1()
    {
        return $this->hasOne(RSoftwareInstalled::className(), ['id' => 'software_installed1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSoftwareInstalled2()
    {
        return $this->hasOne(RSoftwareInstalled::className(), ['id' => 'software_installed2']);
    }
}
