<?php

namespace app\models\registry;

use Yii;
use app\components\utils\Utils;

/**
 * This is the model class for table "r_documentation".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $url
 * @property string $equipment
 * @property string $information_system
 * @property string $software
 * @property string $claim
 * @property string $agreement
 *
 * @property REquipment $equipment0
 * @property RInformationSystem $informationSystem
 * @property RSoftware $software0
 * @property SClaim $claim0
 * @property CAgreement $agreement0
 */
class RDocumentation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_documentation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url'], 'required'],
            [['equipment', 'information_system', 'software', 'claim', 'agreement'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['description', 'url'], 'string', 'max' => 2048]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'description' => 'Описание',
            'url' => 'Url',
            'equipment' => 'Equipment',
            'information_system' => 'Information System',
            'software' => 'Software',
            'claim' => 'Claim',
            'agreement' => 'Agreement',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment0()
    {
        return $this->hasOne(REquipment::className(), ['id' => 'equipment']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInformationSystem()
    {
        return $this->hasOne(RInformationSystem::className(), ['id' => 'information_system']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSoftware0()
    {
        return $this->hasOne(RSoftware::className(), ['id' => 'software']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClaim0()
    {
        return $this->hasOne(SClaim::className(), ['id' => 'claim']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreement0()
    {
        return $this->hasOne(CAgreement::className(), ['id' => 'agreement']);
    }

    /* Удаление записи вместе с физическим файлом
    */
    public function deleteWithFile()
    {
        $root = Yii::getAlias('@app/web/');
        if ( Utils::deleteFile( $root . $this->url ) ) {
            $this->delete();
        } else {
            throw new \Exception("Не удалось удалить файл с документацией");
        }
    }
}
