<?php

namespace app\models\reference;

use Yii;
use app\models\registry\REvent;

/**
 * This is the model class for table "c_type_event".
 *
 * @property string $id
 * @property string $name
 *
 * @property REvent[] $rEvents
 */
class CTypeEvent extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Типы событий";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_type_event';
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
    public function getREvents()
    {
        return $this->hasMany(REvent::className(), ['type' => 'id']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->rEvents )
            return false;
        $this->delete();
        return true;
    }
}
