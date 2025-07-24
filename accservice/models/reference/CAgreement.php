<?php

namespace app\models\reference;

use Yii;
use app\models\registry\RDocumentation;
use app\models\reference\SClaim;
use yii\web\UploadedFile;
use app\components\utils\Utils;

/**
 * This is the model class for table "c_agreement".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 *
 * @property SClaim[] $sClaims
 * @property RDocumentation[] $rDocumentations
 */
class CAgreement extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Соглашения";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_agreement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['name'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 512],
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
        ],
        'description' => [
            'textarea',
            '512',
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
            'name' => 'Наименование соглашения',
            'description' => 'Описание',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRDocumentations()
    {
        return $this->hasMany(RDocumentation::className(), ['agreement' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSClaims()
    {
        return $this->hasMany(SClaim::className(), ['agreement' => 'id']);
    }

    /*
        Триггер после сохранения
    */
    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);
        // Загрузка файла на сервак
        $this->_asUploadFile();
    }

    private function _asUploadFile()
    {
        $validator = new \yii\validators\FileValidator(['extensions' => Yii::$app->params['aviableFileUploadExtensions']]);
        $objUploadFile = UploadedFile::getInstanceByName('RDocumentation[url]');
        if ( !is_null($objUploadFile) && $validator->validate($objUploadFile, $errors) ) {
            $targetDocDir = 'docs/agreements/'.$this->id;
            // поскольку файл с клаймом м.б. только один, то перед записью просто рекурс удал директорию на всякий
            Utils::remove_folder($targetDocDir);
            try {
                mkdir($targetDocDir);
            } catch( \yii\base\ErrorException $e) {
                exit('В момент загрузки файла на сервер произошла ошибка доступа. Файл не записался на диск.
                      Попробуйте обновить страницу с подтверждением отправки данных или
                      повторите процедуру загрузки заново');
            }
            $saveFileName = $targetDocDir.'/'.$objUploadFile->name;
            if ( $objUploadFile->saveAs($saveFileName) ) {
                // если файл сохранился на диске, то ищем и редактируем одну запись
                $docModel = $this->findDocument((int)$this->id);
                $docModel = $docModel ?: new RDocumentation;
                $docModel->url = $saveFileName;
                $docModel->name = 'Соглашение '.$this->id;
                $docModel->description = 'Файл соглашения '.$this->id;
                $docModel->equipment =
                $docModel->information_system =
                $docModel->software =
                $docModel->claim = null;
                $docModel->agreement = $this->id;
                $docModel->save();
            }
            else
                throw new \Exception('Невозможно сохранить файл на диске!');
        }
    }

    /*
        Перед удалением
    */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $docModel = $this->findDocument((int)$this->id);
            if ( $docModel ) {
                Utils::remove_folder( dirname($docModel->url) );
                if ( $docModel->delete() )
                    return true;
                else
                    return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /*
        Найти документ
    */
    public function findDocument($id)
    {
        if ( !is_int($id) || $id < 1 ) throw new \Exception(Yii::$app->params['TYPE_HINT_ERROR']);
        return RDocumentation::findOne(
            [
                'claim' => null,
                'equipment' => null,
                'information_system' => null,
                'software' => null,
                'agreement' => $this->id,
            ]
        );
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->sClaims /*|| $this->rDocumentations*/ )
            return false;
        $this->delete();
        return true;
    }
}
