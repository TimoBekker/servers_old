<?php

namespace app\models\reference;

use Yii;
use app\models\User;
use app\models\registry\RInformationSystem;
use app\models\registry\RLegalDoc;

/**
 * This is the model class for table "s_response_information_system".
 *
 * @property string $id
 * @property string $information_system
 * @property string $responsibility
 * @property string $response_person
 * @property string $legal_doc
 * @property integer $removed
 *
 * @property RInformationSystem $informationSystem
 * @property CVariantResponsibility $responsibility0
 * @property User $responsePerson
 * @property RLegalDoc $legalDoc
 */
class SResponseInformationSystem extends \yii\db\ActiveRecord
{
    // переменные для правильного вытаскивания отчетов
    public $responsePersonName;
    public $infosystemShortName;
    public $responsibilityName;


    public static $tableLabelName = "Ответственные за информационные системы";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_response_information_system';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['information_system', 'responsibility', 'response_person'], 'required'],
            [['information_system', 'responsibility', 'response_person', 'legal_doc', 'removed'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'information_system' => 'Информационная система',
            'responsibility' => 'Вид ответственности',
            'response_person' => 'Пользователь',
            'legal_doc' => 'Нормативно-правовой акт',
            'removed' => 'Удален из системы',
        ];
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
}
