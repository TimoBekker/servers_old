<?php

namespace app\models\relation;

use Yii;
use app\models\registry\RContract;
use app\models\registry\RInformationSystem;

/**
 * This is the model class for table "nn_is_contract".
 *
 * @property string $id
 * @property string $information_system
 * @property string $contract
 *
 * @property RInformationSystem $informationSystem
 * @property RContract $contract0
 */
class NnIsContract extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_is_contract';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['information_system', 'contract'], 'required'],
            [['information_system', 'contract'], 'integer'],
            [['information_system', 'contract'], 'unique', 'targetAttribute' => ['information_system', 'contract'],
                         'message' => 'Комбинация информационной сиситемы и контракта не может повоторяться']
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
            'contract' => 'Contract',
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
    public function getContract0()
    {
        return $this->hasOne(RContract::className(), ['id' => 'contract']);
    }
}
