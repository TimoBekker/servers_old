<?php

namespace app\models\relation;

use Yii;
use app\models\registry\RSoftware;

/**
 * This is the model class for table "nn_software_software".
 *
 * @property string $id
 * @property string $software1
 * @property string $software2
 * @property integer $require_number
 *
 * @property RSoftware $software10
 * @property RSoftware $software20
 */
class NnSoftwareSoftware extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_software_software';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['software1', 'software2'], 'required'],
            [['software1', 'software2', 'require_number'], 'integer'],
            [['software1', 'software2'], 'unique', 'targetAttribute' => ['software1', 'software2'], 'message' => 'The combination of Software1 and Software2 has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'software1' => 'Software1',
            'software2' => 'Software2',
            'require_number' => 'Require Number',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSoftware10()
    {
        return $this->hasOne(RSoftware::className(), ['id' => 'software1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSoftware20()
    {
        return $this->hasOne(RSoftware::className(), ['id' => 'software2']);
    }
}
