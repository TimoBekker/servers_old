<?php

namespace app\models\registry;

use app\models\relation\NnEquipmentInfosysContour;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\reference\CVariantValidation;
use app\models\reference\CLevelPrivacy;
use app\models\reference\CLevelProtection;
use app\models\reference\SResponseInformationSystem;
use app\models\relation\NnIsContract;
use app\models\relation\NnLdocIs;
use app\models\relation\NnAuthitemIs;
use app\models\relation\NnIsSoftware;
use app\models\relation\NnIsSoftinstall;
use app\models\registry\RPassword;
use app\models\registry\RDocumentation;
use app\components\utils\Utils;
use app\components\utils\Cryptonite;
use yii\web\UploadedFile;

/**
 * This is the model class for table "r_information_system".
 *
 * @property string $id
 * @property string $name_short
 * @property string $name_full
 * @property string $description
 * @property string $validation
 * @property string $protection
 * @property string $privacy
 *
 * @property NnAuthitemIs[] $nnAuthitemIs
 * @property NnEventIs[] $nnEventIs
 * @property NnIsContract[] $nnIsContracts
 * @property NnIsSoftinstall[] $nnIsSoftinstalls
 * @property NnIsSoftware[] $nnIsSoftwares
 * @property NnLdocIs[] $nnLdocIs
 * @property RDocumentation[] $rDocumentations
 * @property CVariantValidation $validation0
 * @property CLevelPrivacy $privacy0
 * @property CLevelProtection $protection0
 * @property RPassword[] $rPasswords
 * @property SResponseInformationSystem[] $sResponseInformationSystems
 * @property NnEquipmentInfosysContour[] $nnEquipmentInfosysContours
 */
class RInformationSystem extends \yii\db\ActiveRecord
{
	public $showPassInputInForm = false; // вспомогательное поле

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'r_information_system';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name_short', 'name_full'/*, 'description'*/], 'required'],
			[['validation', 'protection', 'privacy'], 'integer'],
			[['name_short'], 'string', 'max' => 128],
			[['name_full'], 'string', 'max' => 1024],
			[['description'], 'string', 'max' => 2048]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name_short' => 'Наименование (краткое)',
			'name_full' => 'Наименование (полное)',
			'description' => 'Описание',
			'validation' => 'Аттестация системы',
			'protection' => 'Уровень защищенности',
			'privacy' => 'Класс защищенности',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnAuthitemIs()
	{
		return $this->hasMany(NnAuthitemIs::className(), ['information_system' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnEventIs()
	{
		return $this->hasMany(NnEventIs::className(), ['information_system' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnIsContracts()
	{
		return $this->hasMany(NnIsContract::className(), ['information_system' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnIsSoftinstalls()
	{
		return $this->hasMany(NnIsSoftinstall::className(), ['information_system' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnIsSoftwares()
	{
		return $this->hasMany(NnIsSoftware::className(), ['information_system' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnLdocIs()
	{
		return $this->hasMany(NnLdocIs::className(), ['information_system' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRDocumentations()
	{
		return $this->hasMany(RDocumentation::className(), ['information_system' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getValidation0()
	{
		return $this->hasOne(CVariantValidation::className(), ['id' => 'validation']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPrivacy0()
	{
		return $this->hasOne(CLevelPrivacy::className(), ['id' => 'privacy']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProtection0()
	{
		return $this->hasOne(CLevelProtection::className(), ['id' => 'protection']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRPasswords()
	{
		return $this->hasMany(RPassword::className(), ['information_system' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSResponseInformationSystems()
	{
		return $this->hasMany(SResponseInformationSystem::className(), ['information_system' => 'id']);
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNnEquipmentInfosysContours()
    {
        return $this->hasMany(NnEquipmentInfosysContour::class, ['information_system' => 'id']);
    }

	/*
		Перед сохранением
	*/
	// Внедряемся в проесс сохранения (обработчик: )
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {

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

		// Связи с контрактами
		$this->_asContracts($insert);

		// Связи с нормативно-правовыми актами
		$this->_asLegalDocs($insert);

		// Ответственные лица
		$this->_asResponsePersons($insert);

		// Состоит из следующих компонентов ПО
		$this->_asIsSoftware($insert);

		// Связь с установленным ПО
		$this->_asIsSoftinstall($insert);

		// Документация
		$this->_asDocumentation($insert);

		// данные пользователя для логирования
		$forLogUserModel = \app\models\User::findOne(Yii::$app->user->id);
		$forLogUserName = $forLogUserModel->last_name.' '.$forLogUserModel->first_name;

		// Данные аутентификации
		$this->_asAuthenticate($insert, $forLogUserName);

		// Данная информационная система доступна пользователям
		$this->_asAuthItemsIs($insert);

		// логируем если это изменение существующей записи
		if (!$insert){
			Yii::$app->systemlog->dolog(Yii::$app->user->id, 201, "Пользователь {$forLogUserName} изменил свойства информационной системы: {$this->name_short}");
		}

		// echo '</pre>';
		// return $this->needRollback = true;
	}

	// _Связи с контрактами
	private function _asContracts($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с контрактами
			if(Yii::$app->request->post('NnIsContract'))
				NnIsContract::deleteAll(['information_system' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnIsContract')){
			$arrNnIsContract = Utils::array_unique_callback(
				Yii::$app->request->post('NnIsContract'),
				function ($items) {
					 return $items['contract'];
				},
				true
			);
			array_walk($arrNnIsContract, function(&$value, $key){$value['information_system'] =  $this->id;});
			$arrNnIsContractModels = [];
			foreach ($arrNnIsContract as $val) {
			   $arrNnIsContractModels[] = new NnIsContract();
			}
			if( NnIsContract::loadMultiple($arrNnIsContractModels, ['NnIsContract' => array_values($arrNnIsContract)])
					&& NnIsContract::validateMultiple($arrNnIsContractModels)){
				foreach ($arrNnIsContractModels as $NnIsContractModel) {
					$NnIsContractModel->save(false);
				}
			}
		}
		/*
		var_dump(Yii::$app->request->post('NnIsContract'));
		var_dump($arrNnIsContract);
		var_dump( NnIsContract::loadMultiple($arrNnIsContractModels, ['NnIsContract' => array_values($arrNnIsContract)])
					&& NnIsContract::validateMultiple($arrNnIsContractModels));
		*/
	}

	// _Связи с нормативно-правовыми актами
	private function _asLegalDocs($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с контрактами
			if(Yii::$app->request->post('NnLdocIs'))
				NnLdocIs::deleteAll(['information_system' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnLdocIs')){
			$arrNnLdocIs = Utils::array_unique_callback(
				Yii::$app->request->post('NnLdocIs'),
				function ($items) {
					 return $items['legal_doc'];
				},
				true
			);
			array_walk($arrNnLdocIs, function(&$value, $key){$value['information_system'] =  $this->id;});
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
		// var_dump(Yii::$app->request->post('NnLdocIs'));
		// var_dump($arrNnLdocIs);
		// var_dump( NnLdocIs::loadMultiple($arrNnLdocIsModels, ['NnLdocIs' => array_values($arrNnLdocIs)])
		// 			&& NnLdocIs::validateMultiple($arrNnLdocIsModels));
	}

	// _Ответственные лица
	private function _asResponsePersons($insert){
		// очищение
		if(Yii::$app->request->post('SResponseInformationSystem'))
			SResponseInformationSystem::deleteAll(['information_system' => $this->id]);
		// добавление
		if(Yii::$app->request->post('SResponseInformationSystem')){
			$arrSResponseInformationSystem = Yii::$app->request->post('SResponseInformationSystem');
			array_walk($arrSResponseInformationSystem, function(&$value, $key){$value['information_system'] =  $this->id;});
			$arrSResponseInformationSystemModels = [];
			foreach ($arrSResponseInformationSystem as $key => $val) {
				if(empty($val['response_person']) || empty($val['responsibility'])){
					unset($arrSResponseInformationSystem[$key]);
					continue;
				}
				$arrSResponseInformationSystemModels[] = new SResponseInformationSystem();
			}
			if( SResponseInformationSystem::loadMultiple(
					$arrSResponseInformationSystemModels,
					['SResponseInformationSystem' => array_values($arrSResponseInformationSystem)]
				)
				&& SResponseInformationSystem::validateMultiple($arrSResponseInformationSystemModels)){
				foreach ($arrSResponseInformationSystemModels as $SResponseInformationSystemModel) {
					$SResponseInformationSystemModel->save(false);
				}
			}
		}
	}

	// Состоит из следующих компонентов ПО
	private function _asIsSoftware($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с дистрибутивами
			if(Yii::$app->request->post('NnIsSoftware'))
				NnIsSoftware::deleteAll(['information_system' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnIsSoftware')){
			$arrNnIsSoftware = Utils::array_unique_callback(
				Yii::$app->request->post('NnIsSoftware'),
				function ($items) {
					 return $items['software'];
				},
				true
			);
			array_walk($arrNnIsSoftware, function(&$value, $key){$value['information_system'] =  $this->id;});
			$arrNnIsSoftwareModels = [];
			foreach ($arrNnIsSoftware as $val) {
			   $arrNnIsSoftwareModels[] = new NnIsSoftware();
			}
			if( NnIsSoftware::loadMultiple($arrNnIsSoftwareModels, ['NnIsSoftware' => array_values($arrNnIsSoftware)])
					&& NnIsSoftware::validateMultiple($arrNnIsSoftwareModels)){
				foreach ($arrNnIsSoftwareModels as $NnIsSoftwareModel) {
					$NnIsSoftwareModel->save(false);
				}
			}
		}
		// var_dump(Yii::$app->request->post('NnIsSoftware'));
		// var_dump($arrNnIsSoftware);
		// var_dump( NnIsSoftware::loadMultiple($arrNnIsSoftwareModels, ['NnIsSoftware' => array_values($arrNnIsSoftware)])
		// 			&& NnIsSoftware::validateMultiple($arrNnLdocIsModels));
	}

	// Связь с установленным ПО
	private function _asIsSoftinstall($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с установленным ПО
			if(Yii::$app->request->post('NnIsSoftinstall'))
				NnIsSoftinstall::deleteAll(['information_system' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnIsSoftinstall')){
			$arrNnIsSoftinstall = Utils::array_unique_callback(
				Yii::$app->request->post('NnIsSoftinstall'),
				function ($items) {
					 return $items['software_installed'];
				},
				true
			);
			array_walk($arrNnIsSoftinstall, function(&$value, $key){$value['information_system'] =  $this->id;});
			$arrNnIsSoftinstallModels = [];
			foreach ($arrNnIsSoftinstall as $val) {
			   $arrNnIsSoftinstallModels[] = new NnIsSoftinstall();
			}
			if( NnIsSoftinstall::loadMultiple($arrNnIsSoftinstallModels, ['NnIsSoftinstall' => array_values($arrNnIsSoftinstall)])
					&& NnIsSoftinstall::validateMultiple($arrNnIsSoftinstallModels)){
				foreach ($arrNnIsSoftinstallModels as $NnIsSoftinstallModel) {
					$NnIsSoftinstallModel->save(false);
				}
			}
		}
		// var_dump(Yii::$app->request->post('NnIsSoftinstall'));
		// var_dump($arrNnIsSoftinstall);
		// var_dump( NnIsSoftinstall::loadMultiple($arrNnIsSoftinstallModels, ['NnIsSoftinstall' => array_values($arrNnIsSoftinstall)])
		// 			&& NnIsSoftinstall::validateMultiple($arrNnIsSoftinstallModels));
	}

	/* Гетер пути
	*/
	const BASE_DOC_DIR = 'docs/documentation';
	public function getTargetDocDir()
	{
		if ( $this->id ) {
			return self::BASE_DOC_DIR.'/infosys'.$this->id;
		}
		return null;
	}

	/* Создание директории для инфосистемы
	*/
	private function _createDocDir()
	{
        if ( !file_exists($this->targetDocDir) && !mkdir($this->targetDocDir) ){
            throw new \Exception("Невозможно создать директорию! Проверте доступ к папке с документацией!");
        }
	}

	/* Удаление директории инфосистемы
	*/
	private function _deleteDocDir()
	{
		Utils::remove_folder($this->targetDocDir);
	}

	/* Удалить все связанные документы
	*/
	private function _bucketDocs(array $notDeletedDocsId)
	{
		$models = RDocumentation::find()
		->where([
			'and',
			['information_system' => $this->id],
			['not in', 'id', $notDeletedDocsId]
		])
		->all();
		foreach ($models as $model) {
			$model->deleteWithFile();
		}
	}
	/* Документация
	*/
	private function _asDocumentation($insert)
	{
		$this->_createDocDir();

		$notDeletedDocsId = [];

		$root = Yii::getAlias('@app/web/');

		if ( $docItems = Yii::$app->request->post('RDocumentation') ) {

			$validator = new \yii\validators\FileValidator(['extensions' => Yii::$app->params['aviableFileUploadExtensions']]);

			foreach ($docItems as $key => $docItem) {

				$uploadFile = UploadedFile::getInstanceByName("RDocumentation[{$key}][url]");
				$url = $oldUrl = '';

				if ( $uploadFile ) {
					$url = $this->targetDocDir.'/'.$uploadFile->name;
				}

				if ( empty($docItem['currentdocid']) || !$docModel = RDocumentation::findOne($docItem['currentdocid']) ) {
					$docModel = new RDocumentation();
					$docModel->url = $url;
					$docModel->information_system = $this->id;
				} else {
					if ( $url ) {
						$oldUrl = $docModel->url;
						$docModel->url = $url;
					}
				}

				$docModel->name = $docItem['name'];
				$docModel->description = $docItem['description'];

				if ( $docModel->validate() && $validator->validate( $uploadFile, $errors ) ) {

                    if ( $oldUrl ) {
                    	Utils::deleteFile( $root . $oldUrl ); // если не удалиться, то пускай лежит.
                    }
                    if ( !$uploadFile->saveAs($docModel->url) ) {
                        throw new \Exception('Невозможно сохранить файл на диске!');
                    }
					$docModel->save(false);
					$notDeletedDocsId[] = $docModel->id;

				} elseif ($docModel->validate() && $docModel->id) {

					if ( $oldUrl ) {
						$docModel->url = $oldUrl;
					}
					$docModel->save(false);
					$notDeletedDocsId[] = $docModel->id;

				}
			}
		}

		$this->_bucketDocs($notDeletedDocsId);
	}

	/* Сохранение паролей
	*/
	private function _asAuthenticate($insert, $forLogUserName)
	{
		// $dump = function($item) {
		// 	echo '<pre>'; var_dump($item); exit;
		// };

		if ( $passwordsItems = Yii::$app->request->post('RPassword') ) {
			$notDeletedIds = []; // массив ИД неудаляемых элементов

			foreach ($passwordsItems as $passwordItem) {
				$passwordModel = new RPassword();
				$passwordModel->id = null;
				$passwordModel->login = $passwordItem['login'];
				$passwordModel->password = $passwordItem['password'];
				$passwordModel->confirmation = $passwordItem['confirmation'];
				$passwordModel->description = $passwordItem['description'];
				$passwordModel->equipment = null;
				$passwordModel->information_system = $this->id;
				$passwordModel->next = null;
				$passwordModel->finale = 0;
				if ($passwordModel->validate()) {
					// провести сохранение пароля
					if ( !empty($passwordItem['currentid']) && ($oldModel = RPassword::findOne($passwordItem['currentid'])) ) {
						// значит меняется текущий элемент
						// меняем и логируем, если изменился логин, либо пароль, либо описание
						if ( Cryptonite::decodePassword($oldModel->password) !== $passwordModel->password
								|| $oldModel->description !== $passwordModel->description
								|| $oldModel->login !== $passwordModel->login ) {
							// добавляем новую запись в таблицу паролей, после удаляем старую через next и логируем это все
							$passwordModel->save(false); // передаем false, чтобы не проверялось confirmation rules, везде аналогично
							$oldModel->next = $passwordModel->id;
							$oldModel->save(false);
							// логируем изменения
							Yii::$app->systemlog->dolog(Yii::$app->user->id, 404, "Пользователь {$forLogUserName}
								изменил логин, или пароль, или описание от учетной записи {$passwordModel->login} к информационной системе: {$this->name_short}");
						}
					} else {
						// иначе просто добавляем новую запись в таблицу паролей
						$passwordModel->save(false);
					}
					if ( $passwordModel->id ) { // будет нуль, если не изменился ни логин, ни пароль, ни описание
						$notDeletedIds[] = $passwordModel->id;
					} else {
						$notDeletedIds[] = $oldModel->id;
					}
				}
			}

			// Выбираем все Живые пароли данного Оборудования, которые не входят в массив неудаляемых ИД
			// и удаляем их, делая их финальными: finale = 1, а также логируем это действие
			// $dump($notDeletedIds); exit;
			foreach (
				RPassword::find()
				->where(
					[
						'and',
						['information_system' => $this->id],
						['next' => null],
						['finale' => 0],
						['not in', 'id', $notDeletedIds]
					]
				)
				->All() as $valDelPass
			) {
				$valDelPass->finale = 1;
				$valDelPass->save(false);
				// логируем изменения
				Yii::$app->systemlog->dolog(Yii::$app->user->id, 406, "Пользователь {$forLogUserName}
					удалил учетную запись {$valDelPass->login} к информационной системе: {$this->name_short}");
			}
		}
	}

	// Данная информационная система доступна пользователям
	private function _asAuthItemsIs($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с группами пользователей
			if(Yii::$app->request->post('NnAuthitemIs'))
				NnAuthitemIs::deleteAll(['information_system' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnAuthitemIs')){
			$arrNnAuthitemIs = Utils::array_unique_callback(
				Yii::$app->request->post('NnAuthitemIs'),
				function ($items) {
					 return $items['auth_item'];
				},
				true
			);
			array_walk($arrNnAuthitemIs, function(&$value, $key){$value['information_system'] =  $this->id;});
			$arrNnAuthitemIsModels = [];
			foreach ($arrNnAuthitemIs as $key => $val) {
				$arrNnAuthitemIsModels[] = new NnAuthitemIs();
			}
			if( NnAuthitemIs::loadMultiple($arrNnAuthitemIsModels, ['NnAuthitemIs' => array_values($arrNnAuthitemIs)])
					&& NnAuthitemIs::validateMultiple($arrNnAuthitemIsModels)){
				foreach ($arrNnAuthitemIsModels as $NnAuthitemIsModel) {
					$NnAuthitemIsModel->save(false);
				}
			}
		}
	}

	public function afterDelete()
	{
		parent::afterDelete();
		// логируем удаление
		$forLogUserModel = \app\models\User::findOne(Yii::$app->user->id);
		$forLogUserName = $forLogUserModel->last_name.' '.$forLogUserModel->first_name;
		Yii::$app->systemlog->dolog(Yii::$app->user->id, 202, "Пользователь {$forLogUserName} удалил информационную систему: {$this->name_short}");
		// удаляем директорию с файлами
		$this->_deleteDocDir();
	}

	// запрос пароля
	public static function getPassword($isid, $passid, $passlogin){
		if(empty($passid) || empty($isid) || !is_numeric($passid) || !is_numeric($isid))
			return false;
		if($res = RPassword::find()->where(['information_system' => $isid, 'id' => $passid, 'login' => $passlogin])->One()){
			// логируем запрос пароля
			$forLogUserModel = \app\models\User::findOne(Yii::$app->user->id);
			$forLogUserName = $forLogUserModel->last_name.' '.$forLogUserModel->first_name;
			$is_name = self::findOne($isid)->name_short;
			Yii::$app->systemlog->dolog(Yii::$app->user->id, 403, "Пользователь {$forLogUserName}
				просмотрел пароль учетной записи {$passlogin} к информационной системе: {$is_name}");
			if ($res->password)
				return Cryptonite::decodePassword($res->password);
		}
		return false;
	}
}
