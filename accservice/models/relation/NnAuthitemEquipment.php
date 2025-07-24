<?php

namespace app\models\relation;

use Yii;
use app\models\AuthItem;
use app\models\registry\REquipment;

/**
 * This is the model class for table "nn_authitem_equipment".
 *
 * @property string $id
 * @property string $auth_item
 * @property string $equipment
 *
 * @property AuthItem $authItem
 * @property REquipment $equipment0
 */
class NnAuthitemEquipment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_authitem_equipment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['auth_item', 'equipment'], 'required'],
            [['equipment'], 'integer'],
            [['auth_item'], 'string', 'max' => 64],
            [['auth_item', 'equipment'], 'unique', 'targetAttribute' => ['auth_item', 'equipment'],
                                    'message' => 'Комбинация Группы и Оборудования должна быть уникальной']
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
            'equipment' => 'Equipment',
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
    public function getEquipment0()
    {
        return $this->hasOne(REquipment::className(), ['id' => 'equipment']);
    }
}
