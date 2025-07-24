<?php

namespace app\models\reference;

use Yii;
use app\models\User;
use app\models\registry\RContract;

/**
 * This is the model class for table "s_response_warranty_support".
 *
 * @property string $id
 * @property string $contract
 * @property string $response_person
 * @property integer $removed
 *
 * @property RContract $contract0
 * @property User $responsePerson
 */
class SResponseWarrantySupport extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Ответственные за гарантийную поддержку";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_response_warranty_support';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contract', 'response_person'], 'required'],
            [['contract', 'response_person', 'removed'], 'integer']
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
            'response_person' => 'Пользователь',
            'removed' => 'Удален из системы',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract0()
    {
        return $this->hasOne(RContract::className(), ['id' => 'contract']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResponsePerson()
    {
        return $this->hasOne(User::className(), ['id' => 'response_person']);
    }
}
