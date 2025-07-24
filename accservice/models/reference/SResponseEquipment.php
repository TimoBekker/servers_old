<?php

namespace app\models\reference;

use Yii;
use app\models\reference\CVariantResponsibility;
use app\models\User;
use app\models\registry\RLegalDoc;
use app\models\registry\REquipment;

/**
 * This is the model class for table "s_response_equipment".
 *
 * @property string $id
 * @property string $equipment
 * @property string $responsibility
 * @property string $response_person
 * @property string $legal_doc
 * @property integer $removed
 *
 * @property REquipment $equipment0
 * @property CVariantResponsibility $responsibility0
 * @property User $responsePerson
 * @property RLegalDoc $legalDoc
 */
class SResponseEquipment extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Ответственные за оборудование";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_response_equipment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment', 'responsibility', 'response_person'], 'required'],
            [['equipment', 'responsibility', 'response_person', 'legal_doc', 'removed'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment' => 'Оборудование',
            'responsibility' => 'Вид ответственности',
            'response_person' => 'Пользователь',
            'legal_doc' => 'Нормативно-правовой акт',
            'removed' => 'Удален из системы',
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
    public function getResponsibility0()
    {
        return $this->hasOne(CVariantResponsibility::className(), ['id' => 'responsibility']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResponsePerson()
    {
        return $this->hasOne(User::className(), ['id' => 'response_person']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLegalDoc()
    {
        return $this->hasOne(RLegalDoc::className(), ['id' => 'legal_doc']);
    }

    /*
     * Для АПИ
     * */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['legal_doc'],$fields['removed']);
        return $fields;
    }
}
