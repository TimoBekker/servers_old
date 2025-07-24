<?php

namespace app\models\reference;

use Yii;

/**
 * This is the model class for table "c_protocol".
 *
 * @property string $id
 * @property string $name
 *
 * @property SOpenPort[] $sOpenPorts
 */
class CProtocol extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Сетевые протоколы";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_protocol';
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
            'name' => 'Наименование протокола',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSOpenPorts()
    {
        return $this->hasMany(SOpenPort::className(), ['protocol' => 'id']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->sOpenPorts )
            return false;
        $this->delete();
        return true;
    }
}
