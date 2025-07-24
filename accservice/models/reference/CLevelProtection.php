<?php

namespace app\models\reference;

use Yii;
use app\models\registry\RInformationSystem;

/**
 * This is the model class for table "c_level_protection".
 *
 * @property string $id
 * @property string $name
 *
 * @property RInformationSystem[] $rInformationSystems
 */
class CLevelProtection extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Уровни защищенности";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_level_protection';
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
            'name' => 'Наименования уровня',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRInformationSystems()
    {
        return $this->hasMany(RInformationSystem::className(), ['protection' => 'id']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->rInformationSystems )
            return false;
        $this->delete();
        return true;
    }
}
