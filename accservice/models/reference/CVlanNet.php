<?php

namespace app\models\reference;

use Yii;
use app\models\relation\NnEquipmentVlan;

/**
 * This is the model class for table "c_vlan_net".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 *
 * @property NnEquipmentVlan[] $nnEquipmentVlans
 */
class CVlanNet extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "VLAN - сети";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_vlan_net';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['name'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 255],
            [['name'], 'unique']
        ];
    }

    /**
     * @property array $_attributeRenders
     */
    private $_attributeRenders = [
        'name' => [
            'textInput',
            '128',
        ],
        'description' => [
            'textarea',
            '255',
        ]
    ];

    public function getAttributeRender($attrName){
        return isset($this->_attributeRenders[$attrName]) ? $this->_attributeRenders[$attrName] : false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование сети',
            'description' => 'Описание',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNnEquipmentVlans()
    {
        return $this->hasMany(NnEquipmentVlan::className(), ['vlan_net' => 'id']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->nnEquipmentVlans )
            return false;
        $this->delete();
        return true;
    }
}
