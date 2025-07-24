<?php

namespace app\models\reference;

use Yii;
use app\models\registry\REquipment;

/**
 * This is the model class for table "s_variant_access_remote".
 *
 * @property string $id
 * @property string $variant
 * @property string $type
 *
 * @property REquipment[] $rEquipments
 * @property CTypeAccessRemote $type0
 */
class SVariantAccessRemote extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Виды удаленного доступа";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_variant_access_remote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variant', 'type'], 'required'],
            [['type'], 'integer'],
            [['variant'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'variant' => 'Вид доступа',
            'type' => 'Тип удаленного доступа',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getREquipments()
    {
        return $this->hasMany(REquipment::className(), ['access_remote' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(CTypeAccessRemote::className(), ['id' => 'type']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->rEquipments )
            return false;
        $this->delete();
        return true;
    }
}
