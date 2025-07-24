<?php

namespace app\models\relation;

use Yii;
use app\models\AuthItem;
use app\models\registry\RSoftware;

/**
 * This is the model class for table "nn_authitem_software".
 *
 * @property string $id
 * @property string $auth_item
 * @property string $software
 *
 * @property AuthItem $authItem
 * @property RSoftware $software0
 */
class NnAuthitemSoftware extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_authitem_software';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['auth_item', 'software'], 'required'],
            [['software'], 'integer'],
            [['auth_item'], 'string', 'max' => 64],
            [['auth_item', 'software'], 'unique', 'targetAttribute' => ['auth_item', 'software'], 'message' => 'The combination of Auth Item and Software has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_item' => 'Auth Item',
            'software' => 'Software',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItem()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'auth_item']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSoftware0()
    {
        return $this->hasOne(RSoftware::className(), ['id' => 'software']);
    }
}
