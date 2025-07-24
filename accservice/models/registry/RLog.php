<?php

namespace app\models\registry;

use Yii;
use app\models\User;

/**
 * This is the model class for table "r_log".
 *
 * @property string $id
 * @property integer $code
 * @property string $grade
 * @property string $date_emergence
 * @property string $user
 * @property string $content
 *
 * @property User $user0
 */
class RLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'grade', 'date_emergence', 'user', 'content'], 'required'],
            [['code'], 'integer'],
            [['date_emergence'], 'safe'],
            [['grade'], 'string', 'max' => 128],
            [['user'], 'string', 'max' => 255],
            [['content'], 'string', 'max' => 1024]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Код',
            'grade' => 'Важность',
            'date_emergence' => 'Время возникновения',
            'user' => 'Инициатор',
            'content' => 'Содержание',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser0()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }
}
