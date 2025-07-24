<?php

namespace app\models\reference;

use Yii;

/**
 * This is the model class for table "c_type_access_remote".
 *
 * @property string $id
 * @property string $name
 *
 * @property SVariantAccessRemote[] $sVariantAccessRemotes
 */
class CTypeAccessRemote extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Типы удаленного доступа";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_type_access_remote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 128],
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
    public function getSVariantAccessRemotes()
    {
        return $this->hasMany(SVariantAccessRemote::className(), ['type' => 'id']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->sVariantAccessRemotes )
            return false;
        $this->delete();
        return true;
    }
}
