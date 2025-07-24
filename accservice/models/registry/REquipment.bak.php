<?php

namespace app\models\registry;

use app\models\reference\SContourInformationSystem;
use app\models\relation\NnEquipmentInfosysContour;
use Yii;
use app\components\utils\Cryptonite;
use app\components\utils\Utils;
use app\models\reference\CStateEquipment;
use app\models\reference\CTypeEquipment;
use app\models\reference\CVolumeHdd;
use app\models\reference\SIpdnsTable;
use app\models\reference\SOpenPort;
use app\models\reference\SOrganization;
use app\models\reference\SPlacement;
use app\models\reference\SResponseEquipment;
use app\models\reference\SVariantAccessRemote;
use app\models\registry\RDocumentation;
use app\models\registry\RPassword;
use app\models\relation\NnAuthitemEquipment;
use app\models\relation\NnEquipmentClaim;
use app\models\relation\NnEquipmentVlan;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "r_equipment".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property string $manufacturer
 * @property string $serial_number
 * @property string $inventory_number
 * @property string $date_commission
 * @property string $date_decomission
 * @property string $placement
 * @property string $organization
 * @property integer $count_cores
 * @property string $amount_ram
 * @property string $volume_hdd
 * @property integer $access_kspd
 * @property integer $access_internet
 * @property integer $connect_arcsight
 * @property string $access_remote
 * @property string $icing_internet
 * @property string $parent
 *
 * @property NnAuthitemEquipment[] $nnAuthitemEquipments
 * @property NnEquipmentClaim[] $nnEquipmentClaims
 * @property NnEventEquipment[] $nnEventEquipments
 * @property RDocumentation[] $rDocumentations
 * @property CTypeEquipment $type0
 * @property SPlacement $placement0
 * @property SOrganization $organization0
 * @property SVariantAccessRemote $accessRemote
 * @property REquipment $parent0
 * @property REquipment[] $rEquipments
 * @property RPassword[] $rPasswords
 * @property RSoftwareInstalled[] $rSoftwareInstalleds
 * @property SOpenPort[] $sOpenPorts
 * @property SResponseEquipment[] $sResponseEquipments
 * @property NnEquipmentVlan[] $nnEquipmentVlans
 * @property NnEquipmentInfosysContour[] $nnEquipmentInfosysContours
 *
 */
class REquipment extends \yii\db\ActiveRecord
{

    /**
     * @var bool Выставляем, если работаем в движке API.
     */
    public $WORK_IN_API_ENV = false;
	public $showPassInputInForm = false; // вспомогательное поле
    public $showResponsePersonsInputInForm = false; // вспомогательное поле
    /* Несколько дополнительных
    вспомогательных свойств, которые пришлось использовать
    для расширенной таблицы в экшене index
    */
    public $ip_address;
    public $vlan_name;
    public $eq_ip_helper;
    public $response_persons;
    public $claims;
    public $soft_installed;
    public $information_systems;
    public $information_system_contours;

	/*
	public function __construct($config = []){
		// подписываемся на события перед добавлением и редактированием записи
		$this->on(parent::EVENT_AFTER_INSERT, [$this, 'afterInsert']);
		$this->on(parent::EVENT_AFTER_UPDATE, [$this, 'afterUpdate']);
		parent::__construct($config);
	}
	*/

	/*
	 * Для логирования объекта до и после изменения используем доп поля и методы
	 * */
	public $logDumpingBefore = "";
	public $logDumpingAfter = "";

	public function afterFind()
    {
        parent::afterFind();
        // старые данные занесем в лог поле для дальнейшего использования
        $this->logDumpingBefore = $this->getObjectLogDump();
    }

    public function getObjectLogDump($createNewInstance = false)
    {
        $model = $this;
        if ($createNewInstance) {
            $model = self::find()
                ->joinWith(['state0'])
                ->joinWith(['type0'])
                ->joinWith(['organization0'])
                ->joinWith(['cVolumeHdds'])
                // ->joinWith(['rPasswords'])
                ->joinWith(['nnEquipmentVlans.vlanNet'])
                ->joinWith(['nnEquipmentVlans.dnsName'])
                ->joinWith(['sResponseEquipments.responsePerson'])
                ->joinWith(['nnEquipmentClaims.claim0'])
                ->joinWith(['rSoftwareInstalleds.software0'])
                ->joinWith(['nnEquipmentInfosysContours.contourModel'])
                ->joinWith(['nnEquipmentInfosysContours.informationSystemModel'])
                ->where(["r_equipment.id" => $this->id])
                ->one();
        }
	    return print_r(Utils::convertModelToArray($model), true);
    }

    /**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'r_equipment';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name', 'type', 'state'], 'required'],
			[['type', 'placement', 'organization', 'access_kspd', 'access_internet', 'connect_arcsight', 'access_remote', 'parent'], 'integer'],
			[['count_cores','count_processors','state'], 'integer', 'max' => 65535],
			/* старые требования для информации
			// правила для ОЗУ и ПЗУ. В базе максимальное число 16777215
			// единица ОЗУ в базе - это 100 Мб. В поле мы вводим данные в Гб => (16777215*100)/1000 ~ 1677721
			// единица ПЗУ в базе - это 5Гб. В поле мы вводим данные в Гб => (16777215*5) ~ 83886075
			// [['amount_ram', 'volume_hdd'], 'integer', 'max' => 16777215],
			*/
			/* новые требования
			озу и пзу в храняться в мегабайтай т.е. единица хранения = 1мб
			Примечание к volume_hdd, т.к. объемы большие, то в базу данных мы задаем тип
			UNSIGNED BIGINT,
			который может сохранить число 18446744073709551615;
			в PHP нет квантификатора UNSIGNED, поэтому максимальное положительное число  на 64 битной
			системы мы можем запихнуть 9223372036854775807, что логично вдвое меньше
			*/
			[['amount_ram'], 'double', 'max' => 4294967296 / 1024 - 1, 'on' => ['for_the_form']],
			[['amount_ram'], 'integer', 'max' => 4294967295, 'on' => ['default','work_in_api_env']],
			[['volume_hdd'], 'filter', 'filter' => function($value) {
				if (!is_numeric($value)) {
					return '0';
				}
				if (gmp_cmp($value, '18446744073709551615') === 1) {
					return '18446744073709551615';
				}
				if (gmp_cmp($value, '0') === -1) {
					return '0';
				}
				return $value;
			}],
			[['date_commission', 'date_decomission'], 'safe'],
			[['date_commission', 'date_decomission'], 'string', 'max' => 10],
			[['date_commission', 'date_decomission'], 'date', 'format'=>'dd.MM.yyyy', 'on' => ['default']],
			[['date_commission', 'date_decomission'], 'date', 'format'=>'yyyy-MM-dd', 'on' => ['work_in_api_env']],
			// [['last_backuped'], 'date', 'format'=>'dd.MM.yyyy HH:mm', 'on' => ['default']],
			[['last_backuped'], 'date', 'format'=>'yyyy-MM-dd HH:mm', 'on' => ['default']],
			[['last_backuped'], 'date', 'format'=>'yyyy-MM-dd HH:mm:ss', 'on' => ['work_in_api_env']],
			// [['date_commission', 'date_decomission'], 'validateDate'],
			// [['date_commission', 'date_decomission'], 'match', 'pattern'=>'/[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])/'],
			[['date_commission', 'date_decomission'], 'match', 'pattern'=>'/(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}/', 'on' => ['default']],
			// todo: еще между датами где-то надо будет делать проверку, что дата ввода не может быть больше даты вывода
			[['name','vmware_name','model'], 'string', 'max' => 255],
			[['description'], 'string', 'max' => 2048],
			[['manufacturer', 'serial_number', 'inventory_number'], 'string', 'max' => 128],
			[['icing_internet'], 'string', 'max' => 512],
			[['serial_number'], 'unique'],
			[['inventory_number'], 'unique'],
			// ['serial_number', 'default', 'value' => function($model){$model->serial_number = NULL;}],
			[['serial_number', 'inventory_number'], 'default', 'value' => null],
			// для чекбоксов ограничим интегер 1
			[['access_kspd','access_internet','connect_arcsight','backuping'], 'integer', 'max' => 1],
		];
	}

/*    public function validateDate($attr){
		$this->$attr = (new \Datetime($this->$attr))->format('Y-m-d');
		// $this->addError($attribute, 'Login incorrect');
		return true;
	}*/

	// преобразуем дату перед сохранением. обработчик события EVENT_AFTER_VALIDATE
	public function afterValidate(){
		parent::afterValidate();
        // Если мы находимся в АПИ, то не используем логику данного метода
        if ($this->WORK_IN_API_ENV){
            return;
        }
		if($this->date_commission)
			$this->date_commission = (new \Datetime($this->date_commission))->format('Y-m-d');
		if($this->date_decomission)
			$this->date_decomission = (new \Datetime($this->date_decomission))->format('Y-m-d');
		if($this->last_backuped)
			$this->last_backuped = (new \Datetime($this->last_backuped))->format('Y-m-d H:i:s');
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Hostname (имя в OS)',
			'vmware_name' => 'Наименование в VMware',
			'description' => 'Описание',
			'type' => 'Тип',
			'manufacturer' => 'Фирма производитель',
			'serial_number' => 'Серийный номер',
			'inventory_number' => 'Инвентарный номер',
			'date_commission' => 'Дата ввода в эксплуатацию',
			'date_decomission' => 'Дата вывода из эксплуатации',
			'placement' => 'Место размещения',
			'organization' => 'Организация-владелец',
			'count_cores' => 'Количество ядер',
			'amount_ram' => 'Объем ОЗУ',
			'volume_hdd' => 'Объем HDD',
			'access_kspd' => 'Доступ в КСПД',
			'access_internet' => 'Доступ в Интернет',
			'connect_arcsight' => 'Соединение с Arcsight',
			'access_remote' => 'Удаленный доступ',
			'icing_internet' => 'Проброс в интернет',
			'parent' => 'Родительское оборудование',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnAuthitemEquipments()
	{
		return $this->hasMany(NnAuthitemEquipment::className(), ['equipment' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnEquipmentClaims()
	{
		return $this->hasMany(NnEquipmentClaim::className(), ['equipment' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnEventEquipments()
	{
		return $this->hasMany(NnEventEquipment::className(), ['equipment' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRDocumentations()
	{
		return $this->hasMany(RDocumentation::className(), ['equipment' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getType0()
	{
		return $this->hasOne(CTypeEquipment::className(), ['id' => 'type']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getState0()
	{
		return $this->hasOne(CStateEquipment::className(), ['id' => 'state']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPlacement0()
	{
		return $this->hasOne(SPlacement::className(), ['id' => 'placement']);
	}

	/**
	 * @return string
	 */
	public function getPlacementLook()
	{
		return !is_null($this->placement0)
			?
	            $this->placement0->region.', г. '.
	            $this->placement0->city.', ул. '.
	            $this->placement0->street.' - '.
	            $this->placement0->house.', каб. '.
	            $this->placement0->office.', шк. '.
	            $this->placement0->locker.', п. '.
	            $this->placement0->shelf
            :
            	'';
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrganization0()
	{
		return $this->hasOne(SOrganization::className(), ['id' => 'organization']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAccessRemote()
	{
		return $this->hasOne(SVariantAccessRemote::className(), ['id' => 'access_remote']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getParent0()
	{
		return $this->hasOne(REquipment::className(), ['id' => 'parent']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getREquipments()
	{
		return $this->hasMany(REquipment::className(), ['parent' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRPasswords()
	{
		return $this->hasMany(RPassword::className(), ['equipment' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCVolumeHdds()
	{
		return $this->hasMany(CVolumeHdd::className(), ['equipment' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRSoftwareInstalleds()
	{
		return $this->hasMany(RSoftwareInstalled::className(), ['equipment' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSOpenPorts()
	{
		return $this->hasMany(SOpenPort::className(), ['equipment' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSResponseEquipments()
	{
		return $this->hasMany(SResponseEquipment::className(), ['equipment' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnEquipmentVlans()
	{
		return $this->hasMany(NnEquipmentVlan::className(), ['equipment' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNnEquipmentInfosysContours()
	{
		return $this->hasMany(NnEquipmentInfosysContour::class, ['equipment' => 'id']);
	}

	/* Вернуть часть адреса
	*/
	public function getPartplace()
	{
		if ( !$this->placement ) return null;
		$addr = Yii::$app->db->createCommand('
	        SELECT `alias`
	        FROM `view_partplacement`
	        WHERE
	        	`region` = :region AND
	        	`city` = :city AND
	        	`street` = :street AND
	        	`house` = :house
	    ')
	    ->bindValue(':region', $this->placement0->region)
	    ->bindValue(':city', $this->placement0->city)
	    ->bindValue(':street', $this->placement0->street)
	    ->bindValue(':house', $this->placement0->house)
	    ->queryOne();
        if ($addr) {
			return $addr['alias'];
        } else {
        	return null;
        }
	}

	// Внедряемся в проесс сохранения (обработчик: )
	public function beforeSave($insert)
	{
        // Если мы находимся в АПИ, то не используем логику данного метода
	    if ($this->WORK_IN_API_ENV){
	        return parent::beforeSave($insert);
        }
		if (parent::beforeSave($insert)) {
			/*
			header('Content-Type: text/html; charset=utf-8');
			echo '<pre style = "margin-top:100px">';
			echo $insert;
			var_dump($this->isNewRecord);
			var_dump($this->id);
			var_dump($this);
			echo '</pre>';
			// return false;
			*/
			/*
			// делаем правильные цифры ОЗУ и ПЗУ перед сохранением модели
			$this->amount_ram = $this->amount_ram ? (int)($this->amount_ram * 10) : $this->amount_ram;
			$this->volume_hdd = $this->volume_hdd ? (int)round( (int) $this->volume_hdd / 5) : $this->volume_hdd;
			*/
			// сохранение/создание адреса
			$this->_preparePlacement();
			return true;
		} else {
			return false;
		}
	}

	private function _preparePlacement()
	{
		$alias = !empty(Yii::$app->request->post('Partplace')['alias']) ? Yii::$app->request->post('Partplace')['alias'] : '';
		$office = !empty(Yii::$app->request->post('Partplace')['office']) ? Yii::$app->request->post('Partplace')['office'] : '';
		$locker = !empty(Yii::$app->request->post('Partplace')['locker']) ? Yii::$app->request->post('Partplace')['locker'] : '';
		$shelf = !empty(Yii::$app->request->post('Partplace')['shelf']) ? Yii::$app->request->post('Partplace')['shelf'] : '';

		if ( $alias ) {
			$RCDB = Yii::$app->db->createCommand('
	            SELECT `region`, `city`, `street`, `house`
	            FROM `view_partplacement`
	            WHERE `alias` = :alias
	        ')
	        ->bindValue(':alias', $alias)
	        ->queryOne();
	        if ($RCDB) {
	        	// здесь ищем место размещения, если не находим, то генерим и указываем вновь созданное
	        	$address = Yii::$app->db->createCommand('
		            SELECT `id`
		            FROM `s_placement`
		            WHERE
		            	`region` = :region AND
		            	`city` = :city AND
		            	`street` = :street AND
		            	`house` = :house AND
		            	`office` = :office AND
		            	`locker` = :locker AND
		            	`shelf` = :shelf
		        ')
		        ->bindValue(':region', $RCDB['region'])
		        ->bindValue(':city', $RCDB['city'])
		        ->bindValue(':street', $RCDB['street'])
		        ->bindValue(':house', $RCDB['house'])
		        ->bindValue(':office', $office)
		        ->bindValue(':locker', $locker)
		        ->bindValue(':shelf', $shelf)
		        ->queryOne();
		        if ( $address ) {
		        	$this->placement = $address['id'];
		        	// var_dump($address);exit;
		        } else {
		        	$addressModel = new SPlacement();
		        	$addressModel->id = null;
		        	$addressModel->region = $RCDB['region'];
		        	$addressModel->city = $RCDB['city'];
		        	$addressModel->street = $RCDB['street'];
		        	$addressModel->house = $RCDB['house'];
		        	$addressModel->office = $office;
		        	$addressModel->locker = $locker;
		        	$addressModel->shelf = $shelf;
		        	if ($addressModel->save()) {
		        		$this->placement = $addressModel->id;
		        	} else {
		        		$this->placement = null; // просто молча зануляем. не критично
		        	}
		        }
	        } else {
	        	$this->placement = null;
	        }
		}
	}

	public $needRollback = false;
	// После сохранения основной модели - Оборудование добавляем остальные поля.
	// Сохранение завернуто в транзакцию в Контроллере.
	// Если обработчик вернет выставит $this->needRollback, то в контроллере откатится транзакция. (В этом отличие от beforeSave)
	public function afterSave($insert, $changedAttributes){
		parent::afterSave($insert, $changedAttributes);
        // Если мы находимся в АПИ, то не используем логику данного метода
		if ($this->WORK_IN_API_ENV){
            return;
        }
		// header('Content-Type: text/html; charset=utf-8');
		// echo '<pre style = "margin-top:100px">';
		// var_dump($this->id);
		// var_dump($this);
		// echo '</pre>';
		// echo '<pre style = "margin-top:100px">';
		// var_dump($changedAttributes);

		// header('Content-Type: text/html; charset=utf-8');
		// echo '<pre style = "margin-top:100px">';
		// var_dump(Yii::$app->request->post());
		// var_dump($_FILES);
		// echo '</pre>';

		// если это не новая запись, то делаем необходимые действия
		if(!$insert){
			// Удаляем все связи с Заявками даже если не пришел пост
			NnEquipmentClaim::deleteAll(['equipment' => $this->id]);
			// Удаляем все связи с ответственными лицами даже если не пришел пост в том случае,
            // если поле ввода отвественных было показано на форме, иначе не удаляем никого
            if ($this->showResponsePersonsInputInForm) {
			    SResponseEquipment::deleteAll(['equipment' => $this->id]);
                // Связи с оборудованием тоже завязаны на этот блок кода
                NnAuthitemEquipment::deleteAll(['equipment' => $this->id]);
            }
			// Удаляем все связи с открытыми портами даже если не пришел пост
			SOpenPort::deleteAll(['equipment' => $this->id]);
		}

		/* осуществляем сохранение связанных данных */

		// Заявки
		if(Yii::$app->request->post('NnEquipmentClaim')){
			$arrPostClaims = Utils::array_unique_callback(
				Yii::$app->request->post('NnEquipmentClaim'),
				function ($claims) {
					 return $claims['claim'];
				},
				true
			);
			array_walk($arrPostClaims, function(&$value, $key){$value['equipment'] =  $this->id;});
			$arrClaimModels = [];
			foreach ($arrPostClaims as $val) {
			   $arrClaimModels[] = new NnEquipmentClaim();
			}
			if( NnEquipmentClaim::loadMultiple($arrClaimModels, ['NnEquipmentClaim' => array_values($arrPostClaims)])
					&& NnEquipmentClaim::validateMultiple($arrClaimModels)){
				foreach ($arrClaimModels as $claimModel) {
					$claimModel->save(false);
				}
			}
		}

		// Ответственные лица
		if(Yii::$app->request->post('SResponseEquipment')){
			/*$arrPostRespEquips = Utils::array_unique_callback(
				Yii::$app->request->post('SResponseEquipment'),
				function ($items) {
					 return $items['response_person'];
				},
				true
			);*/
			$arrPostRespEquips = Yii::$app->request->post('SResponseEquipment');
			array_walk($arrPostRespEquips, function(&$value, $key){$value['equipment'] =  $this->id;});
			$arrRespEquipsModels = [];
			foreach ($arrPostRespEquips as $key => $val) {
				if(empty($val['response_person']) || empty($val['responsibility'])){
					unset($arrPostRespEquips[$key]);
					continue;
				}
				$arrRespEquipsModels[] = new SResponseEquipment();
			}
			$arrAuthitemEquip = [];
			if( SResponseEquipment::loadMultiple($arrRespEquipsModels, ['SResponseEquipment' => array_values($arrPostRespEquips)])
					&& SResponseEquipment::validateMultiple($arrRespEquipsModels)){
				foreach ($arrRespEquipsModels as $respEquipModel) {
					$respEquipModel->save(false);
					$arrAuthitemEquip[] = ['auth_item' => "auto_item_new_user_".$respEquipModel->response_person];
				}
				// доступ к данному оборудованию
				array_walk($arrAuthitemEquip, function(&$value, $key){$value['equipment'] = $this->id;});
				$arrAuthitemEquip = Utils::array_unique_callback($arrAuthitemEquip,function ($items) {return $items['auth_item'];},true);
				$arrAuthitemEquipModels = [];
				foreach ($arrAuthitemEquip as $key => $val) {
					$arrAuthitemEquipModels[] = new NnAuthitemEquipment();
				}
				if( NnAuthitemEquipment::loadMultiple($arrAuthitemEquipModels, ['NnAuthitemEquipment' => array_values($arrAuthitemEquip)])
						&& NnAuthitemEquipment::validateMultiple($arrAuthitemEquipModels)){
					foreach ($arrAuthitemEquipModels as $authitemEquip) {
						$authitemEquip->save(false);
					}
				}
			}
		}

		// Открытые порты
		if(Yii::$app->request->post('SOpenPort')){
			$arrPostOpenPort = Utils::array_unique_callback(
				Yii::$app->request->post('SOpenPort'),
				function ($items) {
					 return $items['port_number'];
				},
				true
			);
			array_walk($arrPostOpenPort, function(&$value, $key){$value['equipment'] =  $this->id;});
			$arrOpenPortModels = [];
			foreach ($arrPostOpenPort as $key => $val) {
				if(empty($val['protocol'])){
					unset($arrPostOpenPort[$key]);
					continue;
				}
				$arrOpenPortModels[] = new SOpenPort();
			}
			if( SOpenPort::loadMultiple($arrOpenPortModels, ['SOpenPort' => array_values($arrPostOpenPort)])
					&& SOpenPort::validateMultiple($arrOpenPortModels)){
				foreach ($arrOpenPortModels as $openPort) {
					$openPort->save(false);
				}
			}
		}

		$this->_asEquipmentVlan($insert);

		$this->_asVolumeHdds($insert);

		// Документация
		$this->_asDocumentation($insert);

		// Установленное ПО
		$this->_asSoftwareInstalled($insert);

		// Информационные системы
        $this->_asHasInformationSystems($insert);

		// данные пользователя для логирования
		$forLogUserModel = \app\models\User::findOne(Yii::$app->user->id);
		$forLogUserName = $forLogUserModel->last_name.' '.$forLogUserModel->first_name;

		// Данные аутентификации
		$runpassedit = Yii::$app->request->post('runpassedit', 0);
		if ($runpassedit) {
			$this->_asAuthenticate($insert, $forLogUserName);
		}

		// Данное оборудование доступно пользователям
		// Теперь эта штука получает список пользователей не из NnAuthitemEquipment а из списка
		// SResponseEquipment (это согласно т.з.)
		// И она перенесена выше в блок SResponseEquipment
		/* if(Yii::$app->request->post('NnAuthitemEquipment')){
			$arrAuthitemEquip = Utils::array_unique_callback(
				Yii::$app->request->post('NnAuthitemEquipment'),
				function ($items) {
					 return $items['auth_item'];
				},
				true
			);
			array_walk($arrAuthitemEquip, function(&$value, $key){$value['equipment'] =  $this->id;});
			$arrAuthitemEquipModels = [];
			foreach ($arrAuthitemEquip as $key => $val) {
				$arrAuthitemEquipModels[] = new NnAuthitemEquipment();
			}
			if( NnAuthitemEquipment::loadMultiple($arrAuthitemEquipModels, ['NnAuthitemEquipment' => array_values($arrAuthitemEquip)])
					&& NnAuthitemEquipment::validateMultiple($arrAuthitemEquipModels)){
				foreach ($arrAuthitemEquipModels as $authitemEquip) {
					$authitemEquip->save(false);
				}
			}
		} */

        // новые данные занесем в лог поле для дальнейшего использования
        $this->logDumpingAfter = $this->getObjectLogDump(true);
		// логируем если это изменение существующей записи
		if (!$insert){
			$eq_name = $this->name;
			Yii::$app->systemlog->dolog(
			    Yii::$app->user->id,
                101,
                "Пользователь {$forLogUserName} изменил свойства оборудования: {$eq_name}. Список измненных полей:".$this->_getChangedPropsList(),
                $this->logDumpingBefore,
                $this->logDumpingAfter
            );
		}

		// echo '</pre>';
		// return $this->needRollback = true;
	}

	private function _getChangedPropsList(){
	    $rawProps = Yii::$app->request->post("changed-attributes");
        // Cырой массив с полями см. в файле info/rawChangedEquip.txt проекта
        $propsTranslate = [
            "REquipment[type]" => "ТИП",
            "REquipment[name]" => "HOSTNAME",
            "REquipment[vmware_name]" => "VMWARE-ИМЯ",
            "Partplace" => "МЕСТО РАЗМЕЩЕНИЯ",
            "REquipment[state]" => "СТАТУС",
            "Equipment[parent]" => "Родительское оборудование",
            "REquipment[description]" => "Описание",
            "REquipment[organization]" => "Организация-владелец",
            "SResponseEquipment" => "ОТВЕТСТВЕННЫЕ",
            "NnEquipmentVlan" => "IP-АДРЕСА и DNS-ИМЕНА",
            "count_processors" => "Процессоров",
            "count_cores" => "Ядер в каждом",
            "amount_ram" => "ОЗУ",
            "CVolumeHdd" => "HDD",
            "RSoftwareInstalled" => "ПРОГРАММНОЕ ОБЕСПЕЧЕНИЕ",
            "NnEquipmentInfosysContour" => "ИНФОРМАЦИОННЫЕ СИСТЕМЫ",
            "RDocumentation" => "ДОКУМЕНТАЦИЯ",
            "REquipment[access_kspd]" => "Доступ в КСПД",
            "REquipment[access_internet]" => "Доступ в Интернет",
            "REquipment[connect_arcsight]" => "Соединение с Arcsight",
            "REquipment[access_remote]" => "Удаленный доступ",
            "REquipment[icing_internet]" => "Проброс в интернет",
            "SOpenPort" => "Прослушиваемые порты",
            "REquipment[backuping]" => "Резервное копирование",
            "REquipment[date_commission]" => "Дата ввода в эксплуатацию",
            "REquipment[date_decomission]" => "Дата вывода из эксплуатации",
            "NnEquipmentClaim" => "СВЯЗАННЫЕ ЗАЯВКИ",
            "REquipment[manufacturer]" => "Фирма производитель",
            "REquipment[model]" => "Модель",
            "REquipment[serial_number]" => "Серийный номер",
            "REquipment[inventory_number]" => "Инвентарный номер",
            "RPassword" => "ПАРОЛИ",
            "REquipment[last_backuped]" => "Дата и время последнего бекапа",
        ];
        $result = "";
        foreach ($propsTranslate as $key => $item) {
            if (false !== strpos($rawProps, $key)) {
                $result .= " ".$item.",";
            }
        }
        return rtrim($result, ",");
	}
	/*
	  'CVolumeHdd' =>
	    array (size=3)
	      '45b59y2g9ny' =>
	        array (size=2)
	          'size' => string '23' (length=2)
	          'is_dynamic' => string '' (length=0)
	      'bur2gsaz5dd' =>
	        array (size=2)
	          'size' => string '54' (length=2)
	          'is_dynamic' => string '' (length=0)
	      'j4y8p5amybz' =>
	        array (size=2)
	          'size' => string '23423' (length=5)
	          'is_dynamic' => string '' (length=0)
	*/
	private function _asVolumeHdds($insert)
	{
		if (!$insert) {
			CVolumeHdd::deleteAll(['equipment' => $this->id]);
		}
		$data = Yii::$app->request->post('CVolumeHdd');
		if ( $data ) {
			foreach ($data as $val) {
				$modelConfig['equipment'] = $this->id;
				$modelConfig['is_dynamic'] = $val['is_dynamic'];
				$modelConfig['size'] = $val['size'];
				$model = new CVolumeHdd($modelConfig);
				$model->servicePrepareSize();
				$model->save();
			}
		}
	}

	// сохранение связи оборудования с vlan сетями и днс
	private function _asEquipmentVlan($insert)
	{
		/*
		Данные придут в следующем виде:
		'NnEquipmentVlan' =>
			array (size=2)
			  'rgnhiqe7fk1' =>
			    array (size=4)
			      'ip_address' => string '231.231.212.312' (length=15)
			      'mask_subnet' => string '3123' (length=4)
			      'vlan_net' => string '7' (length=1)
			      'dns_names' =>
			        array (size=3)
			          0 => string '12' (length=2)
			          1 => string '1231' (length=4)
			          2 => string '432' (length=3)
			  '7ftvl4393jo' =>
			    array (size=4)
			      'ip_address' => string '123.123.123.123' (length=15)
			      'mask_subnet' => string '234' (length=3)
			      'vlan_net' => string '7' (length=1)
			      'dns_names' =>
			        array (size=3)
			          0 => string '1122' (length=4)
			          1 => string '432' (length=3)
			          2 => string '123' (length=3)
		*/
		if (!$insert) {
			// Удаляем все связи с ip адресами + vlan
			NnEquipmentVlan::deleteAll(['equipment' => $this->id]);
		}
		$data = Yii::$app->request->post('NnEquipmentVlan');
		if ( $data ) {
			// подготовка данных
			foreach ($data as $val) {
				$modelConfig['ip_address'] = ip2long($val['ip_address']);
				// избавляемся от отрицательных значений адреса
				$modelConfig['ip_address'] = ($modelConfig['ip_address'] === false) ? false : sprintf("%u", $modelConfig['ip_address']);
				// преобразуем к виду с 123.123.123.123
				// $modelConfig['mask_subnet'] = Utils::lenToMask($val['mask_subnet']); // не захотели в виде /21
				// $modelConfig['mask_subnet'] = ip2long($modelConfig['mask_subnet']);
				$modelConfig['mask_subnet'] = ip2long($val['mask_subnet']);
				// избавляемся от отрицательных значений адресов
				$modelConfig['mask_subnet'] = ($modelConfig['mask_subnet'] === false) ? false : sprintf("%u", $modelConfig['mask_subnet']);
				$modelConfig['equipment'] = $this->id;
				$modelConfig['vlan_net'] = $val['vlan_net'];
				$model = new NnEquipmentVlan($modelConfig);
				if ( $model->save() ) {
					$dns_names = ( isset($val['dns_names']) && is_array($val['dns_names']) ) ? $val['dns_names'] : [];
					SIpdnsTable::deleteAll(['ip_address' => $modelConfig['ip_address']]);
					foreach ($dns_names as $dns_name) {
						$modelDnsIp = new SIpdnsTable(['dns_name' => $dns_name, 'ip_address' => $val['ip_address']]);
						$modelDnsIp->save();
					}
				}
			}
		}
	}


	/* Гетер пути
	*/
	const BASE_DOC_DIR = 'docs/documentation';
	public function getTargetDocDir()
	{
		if ( $this->id ) {
			return Yii::getAlias('@vendor/../web/').self::BASE_DOC_DIR.'/equip'.$this->id;
		}
		return null;
	}

	/* Создание директории для оборудования
	*/
	private function _createDocDir()
	{
        if ( !file_exists($this->targetDocDir) && !mkdir($this->targetDocDir, 0755, true) ){
            throw new \Exception("Невозможно создать директорию! Проверте доступ к папке с документацией!");
        }
	}

	/* Удаление директории оборудования
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
			['equipment' => $this->id],
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
			$validator = new \yii\validators\FileValidator([
				// 'extensions' => Yii::$app->params['aviableFileUploadExtensions'],
				'mimeTypes' => Yii::$app->params['aviableFileUploadMimetypes'],
				// 'enableClientValidation' => false,
			]);
			foreach ($docItems as $key => $docItem) {
				$uploadFile = UploadedFile::getInstanceByName("RDocumentation[{$key}][url]");
				// var_dump($uploadFile);
				$url = $oldUrl = '';
				if ( $uploadFile ) {
					$url = $this->targetDocDir.'/'.$uploadFile->name;
				}
				if ( empty($docItem['currentdocid']) || !$docModel = RDocumentation::findOne($docItem['currentdocid']) ) {
					$docModel = new RDocumentation();
					$docModel->url = $url;
					$docModel->equipment = $this->id;
				} else {
					if ( $url ) {
						$oldUrl = $docModel->url;
						$docModel->url = $url;
					}
				}
				$docModel->name = $docItem['name'];
				$docModel->description = $docItem['description'];
				// var_dump($validator, $validator->validate( $uploadFile, $errors ), $errors);exit;
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


	/*
		_Установленное ПО.
		Т.к. таблица является не просто таблицей связей, то мы не можем делать апдейт таких связей удалением-добавлением
		Необходимо вычислять добавляемы связи. И делать апдейт тех, которые существуют, лишние удалять.
		Проще всего будет подготовить массив записываемых элементов
		а потом поочередно апдейтить или добавлять или удалять.
		Короче в конце-концов анализ данного действия показал, что без вывода в форме id существующих записей ничего
		путного сделать не получиться.
		  'RSoftwareInstalled' =>
		    array (size=4)
		      '582964374986f0-91219586' =>
		        array (size=5)
		          'software' => string '20' (length=2)
		          'bitrate' => string '32' (length=2)
		          'description' => string 'Прогсто' (length=14)
		          'date_commission' => string '15.11.2016' (length=10)
		          'id' => string '604' (length=3)
		      '582964374afdf9-03055796' =>
		        array (size=5)
		          'software' => string '50' (length=2)
		          'bitrate' => string '64' (length=2)
		          'description' => string 'Описамниед' (length=20)
		          'date_commission' => string '17.11.2016' (length=10)
		          'id' => string '605' (length=3)
		      '582964374bf804-73793308' =>
		        array (size=5)
		          'software' => string '20' (length=2)
		          'bitrate' => string '32' (length=2)
		          'description' => string '' (length=0)
		          'date_commission' => string '' (length=0)
		          'id' => string '606' (length=3)
		      'p7gcpvkn7ca' =>
		        array (size=5)
		          'software' => string '20' (length=2)
		          'bitrate' => string '32' (length=2)
		          'description' => string '' (length=0)
		          'date_commission' => string '16.11.2016' (length=10)
		          'id' => string '' (length=0)
			*/
	private function _asSoftwareInstalled($insert)
	{
		if (!$insert) {
			RSoftwareInstalled::deleteAll(['equipment' => $this->id]);
		}
		$arrPostSoftInst = Yii::$app->request->post('RSoftwareInstalled', []);
		// подготавливаем добавляемые элементы
		array_walk($arrPostSoftInst, function(&$value, $key){$value['equipment'] =  $this->id;});
		// var_dump($arrPostSoftInst);exit;
		foreach ($arrPostSoftInst as $key => $val) {
			if(empty($val['software'])){
				unset($arrPostSoftInst[$key]);
				continue;
			}
			$model = new RSoftwareInstalled();
			$model->load(['RSoftwareInstalled'=>$val]) && $model->save();
		}
		// проход по массиву добавляемых элементов.
		// Если есть updatedsoftinstid, то это редактируемый элемент
		// $arrNonDeletedId = []; // массив связей, которые не надо будет удалять
		// foreach ($arrPostSoftInst as $key => $val) {
		// 	if ( !(!empty($val['updatedsoftinstid']) && ($model = RSoftwareInstalled::findOne($val['updatedsoftinstid'])) !== null) ) {
		// 		$model = new RSoftwareInstalled();
		// 	}
		// 	// var_dump($model);
		// 	if ($model->load([ 'RSoftwareInstalled' => $val] )) {
		// 		$model->save();
		// 		$arrNonDeletedId[] = $model->id;
		// 	}
		// }
		// удаляем все остальные связи, которых у нас не было в пришедших данных, но есть в таблице
		// RSoftwareInstalled::deleteAll(['and', ['equipment' => $this->id], ['not in', 'id',  $arrNonDeletedId]]);
	}

	/*
	 * Тут все просто как и в большинстве - при апдейте удаляем все старые, из поста добавляем все пришедшие.
	 * */
	private function _asHasInformationSystems($insert) {
        if (!$insert) {
            NnEquipmentInfosysContour::deleteAll(['equipment' => $this->id]);
        }
        $arrPostIss = Yii::$app->request->post('NnEquipmentInfosysContour', []);
        // подготавливаем добавляемые элементы
        array_walk($arrPostIss, function(&$value, $key){$value['equipment'] =  $this->id;});
        foreach ($arrPostIss as $key => $item) {
            if(empty($item['information_system']) || empty($item['contour'])){
                unset($arrPostIss[$key]);
                continue;
            }
            $model = new NnEquipmentInfosysContour();
            $model->load(['NnEquipmentInfosysContour' => $item]) && $model->save();
        }
    }

	/* Сохранение паролей
	  'RPassword' =>
	    array (size=6)
	      '5847cb8a711616-04470457' =>
	        array (size=5)
	          'login' => string 'test' (length=4)
	          'description' => string 'opisanie' (length=8)
	          'password' => string '-/V'I}:Q' (length=12)
	          'confirmation' => string '' (length=0)
	          'currentid' => string '670' (length=3)
	      '5847cb8a715494-90815915' =>
	        array (size=5)
	          'login' => string 'qwer' (length=4)
	          'description' => string 'qwer' (length=4)
	          'password' => string '(*+#T|3 <<' (length=15)
	          'confirmation' => string '' (length=0)
	          'currentid' => string '671' (length=3)
	      '5847cb8a715491-63086566' =>
	        array (size=5)
	          'login' => string '111' (length=3)
	          'description' => string '111' (length=3)
	          'password' => string '-8Wg:;Q' (length=11)
	          'confirmation' => string '' (length=0)
	          'currentid' => string '672' (length=3)
	      '5847cb8a715497-78383777' =>
	        array (size=5)
	          'login' => string '111reject' (length=9)
	          'description' => string '111(reject)' (length=11)
	          'password' => string '(*z#<Od*S[7 -<' (length=16)
	          'confirmation' => string '' (length=0)
	          'currentid' => string '673' (length=3)
	      '5847cb8a719311-54120900' =>
	        array (size=5)
	          'login' => string '111reject' (length=9)
	          'description' => string '111(reject)' (length=11)
	          'password' => string '/?;x99' (length=8)
	          'confirmation' => string '' (length=0)
	          'currentid' => string '674' (length=3)
	      'gky8mquu5n9' =>
	        array (size=5)
	          'login' => string 'hgjhgj' (length=6)
	          'description' => string 'hgjhgj' (length=6)
	          'password' => string 'hgjhgj' (length=6)
	          'confirmation' => string 'hgjhgj' (length=6)
	          'currentid' => string '' (length=0)
	*/
	private function _asAuthenticate($insert, $forLogUserName)
	{
		$postItems = Yii::$app->request->post('RPassword', []);
		$notDeletedIds = []; // массив ИД неудаляемых элементов
		foreach ($postItems as $postItem) {
            $passwordModel = new RPassword();
            $passwordModel->id = null;
            $passwordModel->login = $postItem['login'];
            $passwordModel->password = $postItem['password'];
            $passwordModel->confirmation = $postItem['confirmation'];
            $passwordModel->description = $postItem['description'];
            $passwordModel->equipment = $this->id;
            $passwordModel->information_system = null;
            $passwordModel->next = null;
            $passwordModel->finale = 0;
            $isValid = $passwordModel->validate();
			$isUpdate = !empty($postItem['currentid'])
				&& ($oldPasswordModel = RPassword::findOne($postItem['currentid']));
            if (!$isValid && $isUpdate) {
            	$notDeletedIds[] = $oldPasswordModel->id;
            } elseif ($isValid && !$isUpdate) {
            	$passwordModel->save(false);
            	$notDeletedIds[] = $passwordModel->id;
            } elseif (!$isValid && !$isUpdate) {
            	; // для наглядности. Ничего не делаем.
            } elseif ($isValid && $isUpdate) {
                $oldPassword = Cryptonite::decodePassword($oldPasswordModel->password);
            	// неизменность пароля достигаем за счет FAKE_PASS
            	if ($passwordModel->password === RPassword::FAKE_PASS) {
            		$passwordModel->password = $oldPassword;
            	}
            	// обновляем текущую запись через добавление если изменися логин или описание или пароль
                if ( $oldPassword !== $passwordModel->password
                    || $oldPasswordModel->description !== $passwordModel->description
                    || $oldPasswordModel->login !== $passwordModel->login ) {
                    // добавляем новую запись в таблицу паролей, после старую удаляем через next
                    $passwordModel->save(false);
	                $notDeletedIds[] = $passwordModel->id;
                    $oldPasswordModel->next = $passwordModel->id;
                    $oldPasswordModel->save(false);
                    // логируем изменения
                    Yii::$app->systemlog->dolog(Yii::$app->user->id, 402, "Пользователь {$forLogUserName}
                        изменил логин, или пароль, или описание от учетной записи {$oldPasswordModel->login} к оборудованию: {$this->name}");
                } else {
                	// если все осталось как есть, то оставляем старую запись, а новую не создаем
                	$notDeletedIds[] = $oldPasswordModel->id;
                }
            }
		}
        // Выбираем все Живые пароли данного Оборудования, которые не входят в массив неудаляемых ИД
        // и удаляем их, делая их финальными: finale = 1, а также логируем это действие
        $needBeDeletedList = RPassword::find()->where(['and',
        	['equipment' => $this->id],
            ['next'      => null],
            ['finale'    => 0],
            ['not in', 'id', $notDeletedIds]
        ])->All();
        foreach ( $needBeDeletedList as $valDelPass ) {
            $valDelPass->finale = 1;
            $valDelPass->save(false);
            // логируем изменения
            Yii::$app->systemlog->dolog(Yii::$app->user->id, 405, "Пользователь {$forLogUserName}
                удалил учетную запись {$valDelPass->login} к оборудованию: {$this->name}");
        }
	}

	public function afterDelete()
	{
		parent::afterDelete();
        // Если мы находимся в АПИ, то не используем логику данного метода
        if ($this->WORK_IN_API_ENV){
            return;
        }
		// логируем удаление
		$forLogUserModel = \app\models\User::findOne(Yii::$app->user->id);
		$forLogUserName = $forLogUserModel->last_name.' '.$forLogUserModel->first_name;
		$eq_name = $this->name;
		Yii::$app->systemlog->dolog(Yii::$app->user->id, 102, "Пользователь {$forLogUserName} удалил оборудование: {$eq_name}");
		// удаляем директорию с файлами
		$this->_deleteDocDir();
	}

	/* вспомогательные свойства и методы для использования в Видах и еще где угодно */
		// инициализация свойств

	// массив протоколов вида id => name.
	public function getIndexedProtocols(){
		return ArrayHelper::map(
			\app\models\reference\CProtocol::find()->select('id, name')->asArray()->All(), 'id', 'name'
		);
	}

	// массив дистрибутивов ПО вида id => name.
	public function getIndexedSoftware(){
		return ArrayHelper::map(
			\app\models\registry\RSoftware::find()
				->select([
					'r_software.id',
					'concat(
                        cts.name, ", ",
                        r_software.name, " ",
                        r_software.version
                    ) as name'
                ])
                ->join('INNER JOIN', 'c_type_software cts', 'cts.id = r_software.type')
                ->asArray()
                ->All(), 'id', 'name'
		);
	}
	public function getIndexedSoftware1(){
		return ArrayHelper::map(
			\app\models\registry\RSoftware::find()
				->select([
					'r_software.id',
					'IF(
						STRCMP(r_software.description, ""),
					    concat(
                            r_software.name, " ",
                            r_software.version, " (",
                            r_software.description, ")"
                    	) ,
                    	concat(
                            r_software.name, " ",
                            r_software.version
                    	)
                    ) as name'
                ])
                ->orderBy('r_software.name')
                ->asArray()
                ->all(), 'id', 'name'
		);
	}
	public function getIndexedSoftware2(){
		return ArrayHelper::map(
			\app\models\registry\RSoftware::find()
				->select([
					'r_software.id',
					'concat(
                        r_software.name, " ",
                        r_software.version,
                        if(s_organization.name IS NOT NULL, concat(" (",s_organization.name,")"), "")
                	) as name'
                ])
                // ->join('INNER JOIN', 's_organization', 's_organization.id = r_software.developer') // неправильно
                ->leftJoin('s_organization', 's_organization.id = r_software.developer')
                ->orderBy('r_software.name')
                ->asArray()
                ->all(), 'id', 'name'
		);
	}

	// запрос пароля
	public static function getPassword($eqid, $passid, $passlogin){
		if(empty($passid) || empty($eqid) || !is_numeric($passid) || !is_numeric($eqid))
			return false;
		if($res = RPassword::find()->where(['equipment' => $eqid, 'id' => $passid, 'login' => $passlogin])->One()) {
			// логируем запрос пароля
			$forLogUserModel = \app\models\User::findOne(Yii::$app->user->id);
			$forLogUserName = $forLogUserModel->last_name.' '.$forLogUserModel->first_name;
			$eq_name = self::findOne($eqid)->name;
			Yii::$app->systemlog->dolog(Yii::$app->user->id, 401, "Пользователь {$forLogUserName}
				просмотрел пароль учетной записи {$passlogin} к оборудованию: {$eq_name}");
			// возвращаем пароль, если он не пустой
			if ($res->password)
				return Cryptonite::decodePassword($res->password);
		}
		return false;
	}

	public function getSelectParentList()
	{
        $list = [];
        $equipments = self::find()
        	->with('placement0')
        	->with('nnEquipmentVlans')
            ->where('r_equipment.id != :id', ['id'=> is_null($this->id) ? false : $this->id])
            ->orderBy('r_equipment.name')
            ->all();
        foreach ($equipments as $equipment) {
        	$placement = $equipment->placement0 ? ", {$equipment->placement0->display}" : '';
       		$ips = '';
       		foreach ($equipment->nnEquipmentVlans as $ipandvlan) {
       			$ips .= ', '.long2ip($ipandvlan->ip_address);
       		}
       		$list[$equipment->id] = "{$equipment->name}{$ips}{$placement}";
        }
		return $list;
	}

	// всегда возвращаем true, строку не int и не float не трогаем
	public function servicePrepareAmountRam()
	{
		if (Utils::getRealNumType($this->amount_ram) === 'int') {
			$this->amount_ram *= 1024;
		} elseif (Utils::getRealNumType($this->amount_ram) === 'float') {
			$this->amount_ram = round($this->amount_ram * 1000);
		}
		return true;
	}


    /*
     * Для АПИ
     * */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['volume_hdd']);
        return $fields;
    }
}
