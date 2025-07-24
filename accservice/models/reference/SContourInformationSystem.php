<?php
namespace app\models\reference;

use app\models\relation\NnEquipmentInfosysContour;

/**
 * This is the model class for table "s_contractor".
 *
 * @property integer $id
 * @property string $name
 *
 * @property NnEquipmentInfosysContour $nnEquipmentInfosysContours
 *
 */

class SContourInformationSystem extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Контур";

    public static function tableName()
    {
        return 's_contour_information_system';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 256],
            [['name'], 'unique']
        ];
    }

    private $_attributeRenders = [
        'name' => [
            'textInput',
            '256',
        ]
    ];

    public function getAttributeRender($attrName){
        return isset($this->_attributeRenders[$attrName]) ? $this->_attributeRenders[$attrName] : false;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
        ];
    }

    public function getNnEquipmentInfosysContours()
    {
        return $this->hasMany(NnEquipmentInfosysContour::class, ['contour' => 'id']);
    }

    public function deleteWithCheckConstraint(){
        if ( $this->nnEquipmentInfosysContours )
            return false;
        $this->delete();
        return true;
    }
}