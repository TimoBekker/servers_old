<?php

namespace app\models\relation;

use Yii;
use app\models\registry\RLegalDoc;
use app\models\registry\RContract;

/**
 * This is the model class for table "nn_ldoc_contract".
 *
 * @property string $id
 * @property string $legal_doc
 * @property string $contract
 *
 * @property RLegalDoc $legalDoc
 * @property RContract $contract0
 */
class NnLdocContract extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_ldoc_contract';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['legal_doc', 'contract'], 'required'],
            [['legal_doc', 'contract'], 'integer'],
            [['legal_doc', 'contract'], 'unique', 'targetAttribute' => ['legal_doc', 'contract'], 'message' => 'The combination of Legal Doc and Contract has already been taken.']
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
            'contract' => 'Contract',
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
    public function getContract0()
    {
        return $this->hasOne(RContract::className(), ['id' => 'contract']);
    }
}
