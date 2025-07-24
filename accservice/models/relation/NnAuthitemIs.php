<?php

namespace app\models\relation;

use Yii;
use app\models\AuthItem;
use app\models\registry\RInformationSystem;

/**
 * This is the model class for table "nn_authitem_is".
 *
 * @property string $id
 * @property string $auth_item
 * @property string $information_system
 *
 * @property AuthItem $authItem
 * @property RInformationSystem $informationSystem
 */
class NnAuthitemIs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_authitem_is';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['auth_item', 'information_system'], 'required'],
            [['information_system'], 'integer'],
            [['auth_item'], 'string', 'max' => 64],
            [['auth_item', 'information_system'], 'unique', 'targetAttribute' => ['auth_item', 'information_system'], 'message' => 'The combination of Auth Item and Information System has already been taken.']
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
            'information_system' => 'Information System',
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
    public function getInformationSystem()
    {
        return $this->hasOne(RInformationSystem::className(), ['id' => 'information_system']);
    }
}
