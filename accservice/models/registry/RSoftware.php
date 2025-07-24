<?php

namespace app\models\registry;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\reference\CTypeSoftware;
use app\models\reference\CTypeLicense;
use app\models\reference\SOrganization;
use app\models\reference\CMethodLicense;
use app\models\relation\NnAuthitemSoftware;
use app\models\relation\NnSoftwareContract;
use app\models\relation\NnIsSoftware;
use app\models\relation\NnSoftwareSoftware;
use app\models\registry\RDocumentation;
use app\components\utils\Utils;
use yii\web\UploadedFile;

/**
 * This is the model class for table "r_software".
 *
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $version
 * @property string $description
 * @property string $type_license
 * @property integer $count_license
 * @property string $date_end_license
 * @property string $owner
 * @property string $method_license
 * @property string $developer
 * @property integer $count_cores
 * @property string $amount_ram
 * @property string $volume_hdd
 * @property string $inventory_number
 *
 * @property NnAuthitemSoftware[] $nnAuthitemSoftwares
 * @property NnIsSoftware[] $nnIsSoftwares
 * @property NnSoftwareContract[] $nnSoftwareContracts
 * @property NnSoftwareSoftware[] $nnSoftwareSoftwares
 * @property RDocumentation[] $rDocumentations
 * @property CTypeSoftware $type0
 * @property CTypeLicense $typeLicense
 * @property SOrganization $owner0
 * @property CMethodLicense $methodLicense
 * @property SOrganization $developer0
 * @property RSoftwareInstalled[] $rSoftwareInstalleds
 */
class RSoftware extends \yii\db\ActiveRecord
{
	public $helperproperty; // вспомогательное свойство. Использовал в запросах для отчетов

	public function getTypeName()
	{
		return $this->type0->name;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'r_software';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name', 'type'/*, 'version', 'description', 'date_end_license', 'inventory_number'*/], 'required'],
			[['type', 'type_license', /*'count_license',*/ 'owner', 'method_license', 'developer'/*, 'count_cores', 'amount_ram', 'volume_hdd'*/], 'integer'],
			[['count_cores'], 'integer', 'max' => 65535],
			// правила для ОЗУ и ПЗУ. В базе максимальное число 16777215
			// В цифрах хранение отлично от аналогичных в таблице Оборудование
			// единица ОЗУ в базе - это 10 Мб. В поле мы вводим данные в Мб => (16777215*10) ~ 167772150
			// единица ПЗУ в базе - это 100 Мб. В поле мы вводим данные в Гб => (16777215*100)/1000 ~ 1677721
			// [['amount_ram', 'volume_hdd'], 'integer', 'max' => 16777215],
			[['amount_ram'], 'integer', 'max' => 167772150],
			[['volume_hdd'], 'integer', 'max' => 1677721],
			[['type', 'type_license', 'count_license', 'owner', 'method_license', 'developer', 'count_cores', 'amount_ram', 'volume_hdd'], 'integer'],
			[['name'], 'string', 'max' => 512],
			[['version'], 'string', 'max' => 255],
			[['description'], 'string', 'max' => 2048],
			[['inventory_number'], 'string', 'max' => 128],
			[['inventory_number'], 'unique'],
			[['inventory_number'], 'default', 'value' => null],
			[['count_license'], 'integer', 'max' => 65535],
			[['date_end_license'], 'safe'],
			[['date_end_license'], 'string', 'max' => 10],
			[['date_end_license'], 'date', 'format'=>'dd.MM.yyyy'],
			[['date_end_license'], 'match', 'pattern'=>'/(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}/'],
			// [['date_end_license'], 'date', 'format'=>'yyyy-MM-dd'],
			// [['date_end_license', 'date_decomission'], 'string', 'max' => 10],
			// [['date_end_license'], 'match', 'pattern'=>'/[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])/']
		];
	}

	// преобразуем дату перед сохранением. обработчик события EVENT_AFTER_VALIDATE
	public function afterValidate(){
		parent::afterValidate();
		if($this->date_end_license)
			$this->date_end_license = (new \Datetime($this->date_end_license))->format('Y-m-d');
	}

	// Внедряемся в проесс сохранения (обработчик: Перед сохранением)
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			// делаем правильные цифры ОЗУ и ПЗУ перед сохранением модели
			$this->amount_ram = $this->amount_ram ? (int)round( (int) $this->amount_ram / 10) : $this->amount_ram;
			$this->volume_hdd = $this->volume_hdd ? (int)($this->volume_hdd * 10) : $this->volume_hdd;
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

		// Дистрибутивы, требуемые данному
		$this->_asSoftwares($insert);

		// Является частью системных требований следующих информационных систем
		$this->_asIsSoftwares($insert);

		// Документация
		$this->_asDocumentation($insert);

		// Данный дистрибутив доступен пользователям
		$this->_asAuthItemsIs($insert);

		// логируем если это изменение существующей записи
		if (!$insert){
			$forLogUserModel = \app\models\User::findOne(Yii::$app->user->id);
			$forLogUserName = $forLogUserModel->last_name.' '.$forLogUserModel->first_name;
			Yii::$app->systemlog->dolog(Yii::$app->user->id, 301, "Пользователь {$forLogUserName} изменил свойства дистрибутива ПО: {$this->name} {$this->version}");
		}

		// echo '</pre>';
		// return $this->needRollback = true;
	}

	// _Связи с контрактами
	private function _asContracts($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с контрактами
			if(Yii::$app->request->post('NnSoftwareContract'))
				NnSoftwareContract::deleteAll(['software' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnSoftwareContract')){
			$arrNnSoftwareContract = Utils::array_unique_callback(
				Yii::$app->request->post('NnSoftwareContract'),
				function ($items) {
					 return $items['contract'];
				},
				true
			);
			array_walk($arrNnSoftwareContract, function(&$value, $key){$value['software'] =  $this->id;});
			$arrNnSoftwareContractModels = [];
			foreach ($arrNnSoftwareContract as $val) {
			   $arrNnSoftwareContractModels[] = new NnSoftwareContract();
			}
			if( NnSoftwareContract::loadMultiple($arrNnSoftwareContractModels, ['NnSoftwareContract' => array_values($arrNnSoftwareContract)])
					&& NnSoftwareContract::validateMultiple($arrNnSoftwareContractModels)){
				foreach ($arrNnSoftwareContractModels as $NnSoftwareContractModel) {
					$NnSoftwareContractModel->save(false);
				}
			}
		}
		/*
		var_dump(Yii::$app->request->post('NnSoftwareContract'));
		var_dump($arrNnSoftwareContract);
		var_dump( NnSoftwareContract::loadMultiple($arrNnSoftwareContractModels, ['NnSoftwareContract' => array_values($arrNnSoftwareContract)])
					&& NnSoftwareContract::validateMultiple($arrNnSoftwareContractModels));
		*/
	}

	// _Дистрибутивы, требуемые данному
	private function _asSoftwares($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с софтом
			if(Yii::$app->request->post('NnSoftwareSoftware'))
				NnSoftwareSoftware::deleteAll(['software1' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnSoftwareSoftware')){
			$arrNnSoftwareSoftware = Utils::array_unique_callback(
				Yii::$app->request->post('NnSoftwareSoftware'),
				function ($items) {
					 return $items['software2'];
				},
				true
			);
			array_walk($arrNnSoftwareSoftware, function(&$value, $key){$value['software1'] =  $this->id;});
			$arrNnSoftwareSoftwareModels = [];
			foreach ($arrNnSoftwareSoftware as $val) {
			   $arrNnSoftwareSoftwareModels[] = new NnSoftwareSoftware();
			}
			if( NnSoftwareSoftware::loadMultiple($arrNnSoftwareSoftwareModels, ['NnSoftwareSoftware' => array_values($arrNnSoftwareSoftware)])
					&& NnSoftwareSoftware::validateMultiple($arrNnSoftwareSoftwareModels)){
				foreach ($arrNnSoftwareSoftwareModels as $NnSoftwareSoftwareModel) {
					$NnSoftwareSoftwareModel->save(false);
				}
			}
		}
		// var_dump(Yii::$app->request->post('NnSoftwareSoftware'));
		// var_dump($arrNnSoftwareSoftware);
		// var_dump( NnSoftwareSoftware::loadMultiple($arrNnSoftwareSoftwareModels, ['NnSoftwareSoftware' => array_values($arrNnSoftwareSoftware)])
		// 			&& NnSoftwareSoftware::validateMultiple($arrNnSoftwareSoftwareModels));

	}

	// _Является частью системных требований следующих информационных систем
	private function _asIsSoftwares($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с ИС
			if(Yii::$app->request->post('NnIsSoftware'))
				NnIsSoftware::deleteAll(['software' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnIsSoftware')){
			$arrNnIsSoftware = Utils::array_unique_callback(
				Yii::$app->request->post('NnIsSoftware'),
				function ($items) {
					 return $items['information_system'];
				},
				true
			);
			array_walk($arrNnIsSoftware, function(&$value, $key){$value['software'] =  $this->id;});
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
		// 			&& NnIsSoftware::validateMultiple($arrNnSoftwareSoftwareModels));
	}

	/* Гетер пути
	*/
	const BASE_DOC_DIR = 'docs/documentation';
	public function getTargetDocDir()
	{
		if ( $this->id ) {
			return self::BASE_DOC_DIR.'/soft'.$this->id;
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
			['software' => $this->id],
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
					$docModel->software = $this->id;
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

	// _Данный дистрибутив доступен пользователям
	private function _asAuthItemsIs($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с группами пользователей
			if(Yii::$app->request->post('NnAuthitemSoftware'))
				NnAuthitemSoftware::deleteAll(['software' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnAuthitemSoftware')){
			$arrNnAuthitemSoftware = Utils::array_unique_callback(
				Yii::$app->request->post('NnAuthitemSoftware'),
				function ($items) {
					 return $items['auth_item'];
				},
				true
			);
			array_walk($arrNnAuthitemSoftware, function(&$value, $key){$value['software'] =  $this->id;});
			$arrNnAuthitemSoftwareModels = [];
			foreach ($arrNnAuthitemSoftware as $key => $val) {
				$arrNnAuthitemSoftwareModels[] = new NnAuthitemSoftware();
			}
			if( NnAuthitemSoftware::loadMultiple($arrNnAuthitemSoftwareModels, ['NnAuthitemSoftware' => array_values($arrNnAuthitemSoftware)])
					&& NnAuthitemSoftware::validateMultiple($arrNnAuthitemSoftwareModels)){
				foreach ($arrNnAuthitemSoftwareModels as $NnAuthitemSoftwareModel) {
					$NnAuthitemSoftwareModel->save(false);
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
		Yii::$app->systemlog->dolog(Yii::$app->user->id, 302, "Пользователь {$forLogUserName} удалил дистрибутив ПО: {$this->name} {$this->version}");
		// удаляем директорию с файлами
		$this->_deleteDocDir();
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
			'version' => 'Версия',
			'description' => 'Описание',
			'type_license' => 'Тип лицензии',
			'count_license' => 'Количество доступных',
			'date_end_license' => 'Дата окончания действия',
			'owner' => 'Организация-владелец',
			'method_license' => 'Способ лицензирования',
			'developer' => 'Фирма-разработчик',
			'count_cores' => 'Количество ядер',
			'amount_ram' => 'Объем ОЗУ',
			'volume_hdd' => 'Объем HDD',
			'inventory_number' => 'Инвентарный номер',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnAuthitemSoftwares()
	{
		return $this->hasMany(NnAuthitemSoftware::className(), ['software' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnIsSoftwares()
	{
		return $this->hasMany(NnIsSoftware::className(), ['software' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnSoftwareSoftwares()
	{
		return $this->hasMany(NnSoftwareSoftware::className(), ['software1' => 'id']);
	}

	/**
	* @return \yii\db\ActiveQuery
	*/
	public function getNnSoftwareContracts()
	{
	   return $this->hasMany(NnSoftwareContract::className(), ['software' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRDocumentations()
	{
		return $this->hasMany(RDocumentation::className(), ['software' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getType0()
	{
		return $this->hasOne(CTypeSoftware::className(), ['id' => 'type']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTypeLicense()
	{
		return $this->hasOne(CTypeLicense::className(), ['id' => 'type_license']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOwner0()
	{
		return $this->hasOne(SOrganization::className(), ['id' => 'owner']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMethodLicense()
	{
		return $this->hasOne(CMethodLicense::className(), ['id' => 'method_license']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDeveloper0()
	{
		return $this->hasOne(SOrganization::className(), ['id' => 'developer']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRSoftwareInstalleds()
	{
		return $this->hasMany(RSoftwareInstalled::className(), ['software' => 'id']);
	}
}
