<?php

namespace app\models\registry;

use Yii;
use app\models\relation\NnLdocContract;
use app\models\relation\NnIsContract;
use app\models\relation\NnSoftwareContract;
use app\models\reference\SResponseWarrantySupport;
use app\models\reference\SContractor;
use yii\helpers\ArrayHelper;
use app\components\utils\Utils;
use yii\web\UploadedFile;

/**
 * This is the model class for table "r_contract".
 *
 * @property string $id
 * @property string $name
 * @property string $contract_subject
 * @property string $requisite
 * @property string $cost
 * @property string $date_complete
 * @property string $date_begin_warranty
 * @property string $date_end_warranty
 * @property string $url
 *
 * @property NnIsContract[] $nnIsContracts
 * @property NnLdocContract[] $nnLdocContracts
 * @property NnSoftwareContract[] $nnSoftwareContracts
 * @property SContractor[] $sContractors
 * @property SResponseWarrantySupport[] $sResponseWarrantySupports
 */
class RContract extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'r_contract';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name', /*'contract_subject', 'requisite', 'cost', 'date_complete', 'date_begin_warranty', 'date_end_warranty', 'url'*/], 'required'],
			[['cost'], 'integer', 'max' => 4294967295],
			[['name', 'contract_subject', 'requisite'], 'string', 'max' => 512],
			[['url'], 'string', 'max' => 2048],
			[['date_complete', 'date_begin_warranty', 'date_end_warranty'], 'safe'],
			[['date_complete', 'date_begin_warranty', 'date_end_warranty'], 'string', 'max' => 10],
			[['date_complete', 'date_begin_warranty', 'date_end_warranty'], 'date', 'format'=>'dd.MM.yyyy'],
			[['date_complete', 'date_begin_warranty', 'date_end_warranty'], 'match', 'pattern'=>'/(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}/'],
			// [['date_complete', 'date_begin_warranty', 'date_end_warranty'], 'date', 'format'=>'yyyy-MM-dd'],
			// [['date_complete', 'date_begin_warranty', 'date_end_warranty'], 'match', 'pattern'=>'/[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])/'],
		];
	}

	// преобразуем дату перед сохранением. обработчик события EVENT_AFTER_VALIDATE
	public function afterValidate(){
		parent::afterValidate();
		if($this->date_complete)
			$this->date_complete = (new \Datetime($this->date_complete))->format('Y-m-d');
		if($this->date_begin_warranty)
			$this->date_begin_warranty = (new \Datetime($this->date_begin_warranty))->format('Y-m-d');
		if($this->date_end_warranty)
			$this->date_end_warranty = (new \Datetime($this->date_end_warranty))->format('Y-m-d');
	}

	/*
		Перед сохранением
	*/
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {

			// header('Content-Type: text/html; charset=utf-8');
			// echo '<pre style = "margin-top:100px">';
			// var_dump($_FILES);

			// var_dump($this->id);

			// echo '</pre>';
			// return false;

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

		// Исполнитель по контракту, подрядчик (Генподрядчик - один)
		$primaryContractor = $this->_asPrimarySContractor($insert);

		// Исполнитель по контракту, субподрядчик
		$this->_asSContractors($insert, $primaryContractor);

		// Ответственные за гарантийную поддержку
		$this->_asResponsePersons($insert);

		// Связь с нормативно-правовыми актами
		$this->_asLegalDocs($insert);

		// Связать с информационными системами
		$this->_asInfosystems($insert);

		// Связь с программным обеспечением
		$this->_asSoftwares($insert);

		// Загрузка файла
		$this->_asUploadContract($insert, $changedAttributes);

		// echo '</pre>';
		// return $this->needRollback = true;
	}

	// _Исполнитель по контракту, подрядчик (Генподрядчик - один)
	private function _asPrimarySContractor($insert){
		if($PrimarySContractor = Yii::$app->request->post('PrimarySContractor')){
			// Пытаемся найти его в базе
			if(!$model = SContractor::find()->where(['contract' => $this->id, 'prime' => null])->One())
				$model = new SContractor();
			$model->contract = $this->id;
			$model->organization = $PrimarySContractor;
			if($model->save())
				return $model->organization;
			else
				return null;
		}
		else{
			// удаляем подрядчика и всех субподрядчиков
            SContractor::deleteAll(['contract' => $this->id]);
            return null;
		}
		return null;
		// var_dump($model);
		// var_dump(Yii::$app->request->post('PrimarySContractor'));
	}

	// _Исполнитель по контракту, субподрядчик
	private function _asSContractors($insert, $primaryContractor = null){
        // очищение
        if(!$insert){
            // Удаляем все связи c cубподрядчиками (те, у которых prime не нуль)
            if(Yii::$app->request->post('SContractor'))
                SContractor::deleteAll([ 'and', ['contract' => $this->id], ['not', ['prime' => null]] ]);
        }
        // добавление
        // если у нас не задан генподрядчик, то субподрядчиков не имеет смысла добавлять
        if(Yii::$app->request->post('SContractor') && $primaryContractor){
            $arrSContractor = Utils::array_unique_callback(
                Yii::$app->request->post('SContractor'),
                function ($items) {
                     return $items['organization'];
                },
                true
            );
            array_walk($arrSContractor, function(&$value, $key){$value['contract'] = $this->id;});
            $arrSContractorModels = [];
            foreach ($arrSContractor as $key => $val) {
            	$arrSContractor[$key]['prime'] = $primaryContractor;
            	$arrSContractorModels[] = new SContractor();
            }
            if( SContractor::loadMultiple($arrSContractorModels, ['SContractor' => array_values($arrSContractor)])
                    && SContractor::validateMultiple($arrSContractorModels)){
                foreach ($arrSContractorModels as $SContractorModel) {
                    $SContractorModel->save(false);
                }
            }
        }
	}

	// _Ответственные за гарантийную поддержку
	private function _asResponsePersons($insert){
		// очищение
		if(Yii::$app->request->post('SResponseWarrantySupport'))
			SResponseWarrantySupport::deleteAll(['contract' => $this->id]);
		// добавление (Здесь у ответственных нет видов ответственности, поэтому мы их уникалим)
		if(Yii::$app->request->post('SResponseWarrantySupport')){
			$arrSResponseWarrantySupport = Utils::array_unique_callback(
				Yii::$app->request->post('SResponseWarrantySupport'),
				function ($items) {
					 return $items['response_person'];
				},
				true
			);
			array_walk($arrSResponseWarrantySupport, function(&$value, $key){$value['contract'] =  $this->id;});
			$arrSResponseWarrantySupportModels = [];
			foreach ($arrSResponseWarrantySupport as $key => $val) {
				$arrSResponseWarrantySupportModels[] = new SResponseWarrantySupport();
			}
			if( SResponseWarrantySupport::loadMultiple(
					$arrSResponseWarrantySupportModels,
					['SResponseWarrantySupport' => array_values($arrSResponseWarrantySupport)]
				)
				&& SResponseWarrantySupport::validateMultiple($arrSResponseWarrantySupportModels)){
				foreach ($arrSResponseWarrantySupportModels as $SResponseWarrantySupportModel) {
					$SResponseWarrantySupportModel->save(false);
				}
			}
		}
	}

	// _Связь с нормативно-правовыми актами
	private function _asLegalDocs($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с контрактами
			if(Yii::$app->request->post('NnLdocContract'))
				NnLdocContract::deleteAll(['contract' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnLdocContract')){
			$arrNnLdocContract = Utils::array_unique_callback(
				Yii::$app->request->post('NnLdocContract'),
				function ($items) {
					 return $items['legal_doc'];
				},
				true
			);
			array_walk($arrNnLdocContract, function(&$value, $key){$value['contract'] =  $this->id;});
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
		// var_dump(Yii::$app->request->post('NnLdocContract'));
		// var_dump($arrNnLdocContract);
		// var_dump( NnLdocContract::loadMultiple($arrNnLdocContractModels, ['NnLdocContract' => array_values($arrNnLdocContract)])
		// 			&& NnLdocContract::validateMultiple($arrNnLdocContractModels));
	}

	// _Связать с информационными системами
	private function _asInfosystems($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с контрактами
			if(Yii::$app->request->post('NnIsContract'))
				NnIsContract::deleteAll(['contract' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnIsContract')){
			$arrNnIsContract = Utils::array_unique_callback(
				Yii::$app->request->post('NnIsContract'),
				function ($items) {
					 return $items['information_system'];
				},
				true
			);
			array_walk($arrNnIsContract, function(&$value, $key){$value['contract'] =  $this->id;});
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
	}

	// _Связь с программным обеспечением
	private function _asSoftwares($insert){
		// очищение
		if(!$insert){
			// Удаляем все связи с контрактами
			if(Yii::$app->request->post('NnSoftwareContract'))
				NnSoftwareContract::deleteAll(['contract' => $this->id]);
		}
		// добавление
		if(Yii::$app->request->post('NnSoftwareContract')){
			$arrNnSoftwareContract = Utils::array_unique_callback(
				Yii::$app->request->post('NnSoftwareContract'),
				function ($items) {
					 return $items['software'];
				},
				true
			);
			array_walk($arrNnSoftwareContract, function(&$value, $key){$value['contract'] =  $this->id;});
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
	}

	// _Загрузка файла
	private function _asUploadContract($insert, $changedAttributes){
		$validator = new \yii\validators\FileValidator(['extensions' => Yii::$app->params['aviableFileUploadExtensions']]);
		$objUploadFile = UploadedFile::getInstanceByName('RContract[url]');
		if ( $objUploadFile && $validator->validate($objUploadFile, $errors) ) {
			$targetDocDir = 'docs/contracts/'.$this->id;
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
		Yii::$app->systemlog->dolog(Yii::$app->user->id, 601, "Пользователь {$forLogUserName} удалил контракт: {$this->name}");
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Наименование',
			'contract_subject' => 'Предмет контракта',
			'requisite' => 'Реквизиты',
			'cost' => 'Стоимость',
			'date_complete' => 'Срок окончания работ',
			'date_begin_warranty' => 'Дата начала гарантийных обязательств',
			'date_end_warranty' => 'Дата окончания гарантийных обязательств',
			'url' => 'Документ в электронном виде',
		];
	}

	/**
	* @return \yii\db\ActiveQuery
	*/
	public function getNnSoftwareContracts()
	{
	   return $this->hasMany(NnSoftwareContract::className(), ['contract' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnIsContracts()
	{
		return $this->hasMany(NnIsContract::className(), ['contract' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnLdocContracts()
	{
		return $this->hasMany(NnLdocContract::className(), ['contract' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSContractors()
	{
		return $this->hasMany(SContractor::className(), ['contract' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSResponseWarrantySupports()
	{
		return $this->hasMany(SResponseWarrantySupport::className(), ['contract' => 'id']);
	}
}
