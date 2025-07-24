<?php

namespace app\models\reference;

use Yii;
use app\models\reference\CAgreement;
use app\models\relation\NnEquipmentClaim;
use app\models\registry\RDocumentation;
use yii\web\UploadedFile;
use app\components\utils\Utils;

/**
 * This is the model class for table "s_claim".
 *
 * @property string $id
 * @property string $name
 * @property string $agreement
 *
 * @property NnEquipmentClaim[] $nnEquipmentClaims
 * @property RDocumentation[] $rDocumentations
 * @property CAgreement $agreement0
 */
class SClaim extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Заявки";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_claim';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'agreement'], 'required'],
            [['agreement'], 'integer'],
            [['name'], 'string', 'max' => 128]
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
            'agreement' => 'Соглашение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNnEquipmentClaims()
    {
        return $this->hasMany(NnEquipmentClaim::className(), ['claim' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRDocumentations()
    {
        return $this->hasMany(RDocumentation::className(), ['claim' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreement0()
    {
        return $this->hasOne(CAgreement::className(), ['id' => 'agreement']);
    }


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
            $targetDocDir = 'docs/claims/'.$this->id;
            // поскольку файл с клаймом м.б. только один, то перед записью просто рекурс удал директорию на всякий
            Utils::remove_folder($targetDocDir);
            try {
                mkdir($targetDocDir);
            } catch( \yii\base\ErrorException $e) {
                exit('В момент загрузки файла на сервер произошла ошибка доступа. Файл не записался на диск.
                      Попробуйте обновить страницу с подтверждением отправки данных или
                      повторите процедуру загрузки заново');
            }
            // $saveFileName = $targetDocDir.'/'.$objUploadFile->baseName . '.' . $objUploadFile->extension;
            $saveFileName = $targetDocDir.'/'.$objUploadFile->name;
            // mb_internal_encoding("UTF-8");
            // $saveFileName = mb_convert_encoding($saveFileName, "UTF-8", "Windows-1251");
            // $saveFileName = convert_cyr_string($saveFileName, 'w', 'k');
            // $saveFileName = iconv("windows-1251", "UTF-8", $saveFileName);
            // var_dump($saveFileName);exit;
            if ( $objUploadFile->saveAs($saveFileName) ) {
                // если файл сохранился на диске, то ищем и редактируем одну запись
                $docModel = $this->findDocument((int)$this->id);
                $docModel = $docModel ?: new RDocumentation;
                $docModel->url = $saveFileName;
                $docModel->name = 'Заявка '.$this->id;
                $docModel->description = 'Файл заявки '.$this->id;
                $docModel->equipment =
                $docModel->information_system =
                $docModel->software =
                $docModel->agreement = null;
                $docModel->claim = $this->id;
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
                'claim' => $id,
                'equipment' => null,
                'information_system' => null,
                'software' => null,
                'agreement' => null,
            ]
        );
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->nnEquipmentClaims /*|| $this->rDocumentations*/ )
            return false;
        $this->delete();
        return true;
    }
}
