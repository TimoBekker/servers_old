<?php

namespace app\models\relation;

use Yii;
use app\models\registry\RLegalDoc;
use app\models\registry\RInformationSystem;

/**
 * This is the model class for table "nn_ldoc_is".
 *
 * @property string $id
 * @property string $legal_doc
 * @property string $information_system
 *
 * @property RLegalDoc $legalDoc
 * @property RInformationSystem $informationSystem
 */
class NnLdocIs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_ldoc_is';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['legal_doc', 'information_system'], 'required'],
            [['legal_doc', 'information_system'], 'integer'],
            [['legal_doc', 'information_system'], 'unique', 'targetAttribute' => ['legal_doc', 'information_system'], 'message' => 'The combination of Legal Doc and Information System has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'legal_doc' => 'Legal Doc',
            'information_system' => 'Information System',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLegalDoc()
    {
        return $this->hasOne(RLegalDoc::className(), ['id' => 'legal_doc']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInformationSystem()
    {
        return $this->hasOne(RInformationSystem::className(), ['id' => 'information_system']);
    }
}
