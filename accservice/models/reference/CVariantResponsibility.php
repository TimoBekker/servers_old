<?php

namespace app\models\reference;

use Yii;

/**
 * This is the model class for table "c_variant_responsibility".
 *
 * @property string $id
 * @property string $name
 *
 * @property SResponseEquipment[] $sResponseEquipments
 * @property SResponseInformationSystem[] $sResponseInformationSystems
 */
class CVariantResponsibility extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Виды ответственности";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_variant_responsibility';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique']
        ];
    }

    /**
     * @property array $_attributeRenders
     */
    private $_attributeRenders = [
        'name' => [
            'textInput',
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
            'name' => 'Наименование вида',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSResponseEquipments()
    {
        return $this->hasMany(SResponseEquipment::className(), ['responsibility' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSResponseInformationSystems()
    {
        return $this->hasMany(SResponseInformationSystem::className(), ['responsibility' => 'id']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->sResponseEquipments || $this->sResponseInformationSystems )
            return false;
        $this->delete();
        return true;
    }
}
