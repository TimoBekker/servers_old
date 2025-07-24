<?php

namespace app\models\relation;

use Yii;
use app\models\registry\RContract;
use app\models\registry\RSoftware;

/**
 * This is the model class for table "nn_software_contract".
 *
 * @property string $id
 * @property string $software
 * @property string $contract
 *
 * @property RSoftware $software0
 * @property RContract $contract0
 */
class NnSoftwareContract extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_software_contract';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['software', 'contract'], 'required'],
            [['software', 'contract'], 'integer'],
            [['software', 'contract'], 'unique', 'targetAttribute' => ['software', 'contract'], 'message' => 'The combination of Software and Contract has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'software' => 'Software',
            'contract' => 'Contract',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSoftware0()
    {
        return $this->hasOne(RSoftware::className(), ['id' => 'software']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract0()
    {
        return $this->hasOne(RContract::className(), ['id' => 'contract']);
    }
}
