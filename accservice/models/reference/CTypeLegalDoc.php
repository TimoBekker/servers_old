<?php

namespace app\models\reference;

use Yii;
use app\models\registry\RLegalDoc;

/**
 * This is the model class for table "c_type_legal_doc".
 *
 * @property string $id
 * @property string $name
 *
 * @property RLegalDoc[] $rLegalDocs
 */
class CTypeLegalDoc extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Типы нормативно-правовых актов";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_type_legal_doc';
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
    public function getRLegalDocs()
    {
        return $this->hasMany(RLegalDoc::className(), ['type' => 'id']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->rLegalDocs )
            return false;
        $this->delete();
        return true;
    }
}
