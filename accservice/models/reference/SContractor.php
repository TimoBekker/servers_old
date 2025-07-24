<?php

namespace app\models\reference;

use Yii;
use app\models\reference\SOrganization;
use app\models\registry\RContract;

/**
 * This is the model class for table "s_contractor".
 *
 * @property string $id
 * @property string $contract
 * @property string $organization
 * @property string $prime
 *
 * @property SOrganization $organization0
 * @property RContract $contract0
 */
class SContractor extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Исполнители по контрактам";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_contractor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contract', 'organization'], 'required'],
            [['contract', 'organization', 'prime'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contract' => 'Контракт',
            'organization' => 'Организация',
            'prime' => 'Генподрядчик',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization0()
    {
        return $this->hasOne(SOrganization::className(), ['id' => 'organization']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract0()
    {
        return $this->hasOne(RContract::className(), ['id' => 'contract']);
    }
}
