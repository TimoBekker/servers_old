<?php

namespace app\models\reference;

use Yii;
use app\models\registry\RSoftware;

/**
 * This is the model class for table "c_type_license".
 *
 * @property string $id
 * @property string $name
 *
 * @property RSoftware[] $rSoftwares
 */
class CTypeLicense extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Типы лицензий на ПО";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_type_license';
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
            'name' => 'Наименование типа',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRSoftwares()
    {
        return $this->hasMany(RSoftware::className(), ['type_license' => 'id']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->rSoftwares )
            return false;
        $this->delete();
        return true;
    }
}
