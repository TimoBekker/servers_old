<?php

namespace app\models\registry;

use Yii;
use app\models\reference\CTypeLegalDoc;
use app\models\reference\SResponseEquipment;
use app\models\reference\SResponseInformationSystem;
use app\models\relation\NnLdocContract;
use app\models\relation\NnLdocIs;
use yii\helpers\ArrayHelper;
use app\components\utils\Utils;
use yii\web\UploadedFile;

/**
 * This is the model class for table "r_legal_doc".
 *
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $date
 * @property string $number
 * @property string $regulation_subject
 * @property integer $status
 * @property string $url
 *
 * @property NnLdocContract[] $nnLdocContracts
 * @property NnLdocIs[] $nnLdocIs
 * @property CTypeLegalDoc $type0
 * @property SResponseEquipment[] $sResponseEquipments
 * @property SResponseInformationSystem[] $sResponseInformationSystems
 */
class RLegalDoc extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'r_legal_doc';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name', 'type'/*, 'date', 'number', 'regulation_subject', 'url'*/], 'required'],
			[['type', 'status'], 'integer'],
			[['date'], 'safe'],
			[['date'], 'string', 'max' => 10],
			[['date'], 'date', 'format'=>'dd.MM.yyyy'],
			[['date'], 'match', 'pattern'=>'/(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}/'],
			// [['date'], 'date', 'format'=>'yyyy-MM-dd'],
			// [['date'], 'match', 'pattern'=>'/[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])/'],
			[['name', 'regulation_subject'], 'string', 'max' => 512],
			[['number'], 'string', 'max' => 128],
			[['url'], 'string', 'max' => 2048],
			// ['status', 'default', 'value' => 1],
			// ['status', 'integer', 'when' => function ($model) { return !$model->status; }],
		];
	}

	public function afterValidate(){
		parent::afterValidate();
		if($this->date)
			$this->date = (new \Datetime($this->date))->format('Y-m-d');
	}

	/*
		Перед сохранением
	*/
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			// инвертируем значени из чекбокса
			$this->status = $this->status === '1' ? '0' : '1';
			return true;
		} else {
			return false;
		}
	}

	/*
		После сохранения
	*/
	public $needRollback = false; // для отмены сохранения транзакции

	public function afterSave($insert, $changedAttributes){
		parent::afterSave($insert, $changedAttributes);

		// header('Content-Type: text/html; charset=utf-8');
		// echo '<pre style = "margin-top:100px">';

		// Связь с контрактами
		$this->_asContracts($insert);

		// Связать с информационными системами
		$this->_asInfosystems($insert);

		// Загрузка файла
		$this->_asUploadLegaldocs($insert, $changedAttributes);

		// echo '</pre>';
		// return $this->needRollback = true;
	}

	// _Связь с контрактами
	private function _asContracts($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с контрактами
			if(Yii::$app->request->post('NnLdocContract'))
				NnLdocContract::deleteAll(['legal_doc' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnLdocContract')){
			$arrNnLdocContract = Utils::array_unique_callback(
				Yii::$app->request->post('NnLdocContract'),
				function ($items) {
					 return $items['contract'];
				},
				true
			);
			array_walk($arrNnLdocContract, function(&$value, $key){$value['legal_doc'] =  $this->id;});
			$arrNnLdocContractModels = [];
			foreach ($arrNnLdocContract as $val) {
			   $arrNnLdocContractModels[] = new NnLdocContract();
			}
			if( NnLdocContract::loadMultiple($arrNnLdocContractModels, ['NnLdocContract' => array_values($arrNnLdocContract)])
					&& NnLdocContract::validateMultiple($arrNnLdocContractModels)){
				foreach ($arrNnLdocContractModels as $NnLdocContractModel) {
					$NnLdocContractModel->save(false);
				}
			}
		}
		/*
		var_dump(Yii::$app->request->post('NnLdocContract'));
		var_dump($arrNnLdocContract);
		var_dump( NnLdocContract::loadMultiple($arrNnLdocContractModels, ['NnLdocContract' => array_values($arrNnLdocContract)])
					&& NnLdocContract::validateMultiple($arrNnLdocContractModels));
		*/
	}

	// _Связать с информационными системами
	private function _asInfosystems($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с контрактами
			if(Yii::$app->request->post('NnLdocIs'))
				NnLdocIs::deleteAll(['legal_doc' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnLdocIs')){
			$arrNnLdocIs = Utils::array_unique_callback(
				Yii::$app->request->post('NnLdocIs'),
				function ($items) {
					 return $items['information_system'];
				},
				true
			);
			array_walk($arrNnLdocIs, function(&$value, $key){$value['legal_doc'] =  $this->id;});
			$arrNnLdocIsModels = [];
			foreach ($arrNnLdocIs as $val) {
			   $arrNnLdocIsModels[] = new NnLdocIs();
			}
			if( NnLdocIs::loadMultiple($arrNnLdocIsModels, ['NnLdocIs' => array_values($arrNnLdocIs)])
					&& NnLdocIs::validateMultiple($arrNnLdocIsModels)){
				foreach ($arrNnLdocIsModels as $NnLdocIsModel) {
					$NnLdocIsModel->save(false);
				}
			}
		}
	}

	// _Загрузка файла
	private function _asUploadLegaldocs($insert, $changedAttributes){
		$validator = new \yii\validators\FileValidator(['extensions' => Yii::$app->params['aviableFileUploadExtensions']]);
		$objUploadFile = UploadedFile::getInstanceByName('RLegalDoc[url]');
		if ( $objUploadFile && $validator->validate($objUploadFile, $errors) ){
			$targetDocDir = 'docs/legaldocs/'.$this->id;
            Utils::remove_folder($targetDocDir);
            try {
                mkdir($targetDocDir);
            } catch( \yii\base\ErrorException $e) {
                exit('В момент загрузки файла на сервер произошла ошибка доступа. Файл не записался на диск.
                      Попробуйте обновить страницу с подтверждением отправки данных или
                      повторите процедуру загрузки заново');
            }
			$saveFileName = $targetDocDir.'/'.$objUploadFile->name;
			if($objUploadFile->saveAs($saveFileName)){
			   $this->url = $saveFileName;
			   $this->updateAll(['url' => $saveFileName], ['id' => $this->id]);
			}
			else
				throw new \Exception('Невозможно сохранить файл на диске!');
		}
		else{
			if($currenturl = Yii::$app->request->post('currenturl'))
				$this->updateAll(['url' => $currenturl], ['id' => $this->id]);
		}
	}

    /*
        Перед удалением
    */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            Utils::remove_folder( dirname($this->url) );
            return true;
        } else {
            return false;
        }
    }

    /*
		После удаления
    */
	public function afterDelete()
	{
		parent::afterDelete();
		// логируем удаление
		$forLogUserModel = \app\models\User::findOne(Yii::$app->user->id);
		$forLogUserName = $forLogUserModel->last_name.' '.$forLogUserModel->first_name;
		Yii::$app->systemlog->dolog(Yii::$app->user->id, 602, "Пользователь {$forLogUserName} удалил нормативно-правовой акт: {$this->name}");
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Наименование',
			'type' => 'Тип',
			'date' => 'Дата',
			'number' => 'Номер',
			'regulation_subject' => 'Предмет регулирования',
			'status' => 'Действующий', // логич. 0 - в работе, 1 - отменен
			'url' => 'Документ в электронном виде',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnLdocContracts()
	{
		return $this->hasMany(NnLdocContract::className(), ['legal_doc' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnLdocIs()
	{
		return $this->hasMany(NnLdocIs::className(), ['legal_doc' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getType0()
	{
		return $this->hasOne(CTypeLegalDoc::className(), ['id' => 'type']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSResponseEquipments()
	{
		return $this->hasMany(SResponseEquipment::className(), ['legal_doc' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSResponseInformationSystems()
	{
		return $this->hasMany(SResponseInformationSystem::className(), ['legal_doc' => 'id']);
	}

/*	public function loadFile($uploadfile = null){
		if(!empty($uploadfile)){
			$filePath = 'docs/legaldocs/'.$uploadfile->baseName . '.' . $uploadfile->extension;
			if($uploadfile->saveAs($filePath)){
			   $this->url = $filePath;
			}
			else throw new \Exception('Невозможно сохранить файл на диске!');
		}
		return true; // todo: надо найти подходящий Exception
	}*/
}
