<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\reference\CTypeEquipment;
// use app\models\reference\SPlacement;
use app\models\reference\SOrganization;
use app\models\reference\SVariantAccessRemote;
use app\models\reference\CVariantResponsibility;
use app\models\reference\SResponseEquipment; // по сути расширенная NN-таблица
use app\models\relation\NnEquipmentVlan;
use app\models\relation\NnEquipmentClaim;
use app\models\reference\CVlanNet;
use app\models\reference\SClaim;
use app\models\User;
use app\models\registry\RLegalDoc;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model app\models\registry\REquipment */
/* @var $form yii\widgets\ActiveForm */

/*
	js счетчики множественных полей
	var cntrVlanMax
*/
?>

<?php // Модальное диалоговое окно ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel"><?= $modalName = 'Оборудование успешно добавлено' ?></h4>
			</div>
			<!-- <div class="modal-body">
			</div> -->
			<div class="modal-footer">
				<?= Html::a('Перейти', ['equip/view', 'id' => isset($switcherId) ? $switcherId : ''], ['class' => 'btn btn-primary'])?>
				<button type="button" class="btn btn-default" data-dismiss="modal">Добавить новое</button>
			</div>
		</div>
	</div>
</div>

<?php
$refrashUrl = Yii::$app->urlManager->createUrl(['equip/create']);
$this->registerJs("
$('#myModal').on('hidden.bs.modal', function (e) {
	window.location.replace('{$refrashUrl}');
})
", $this::POS_READY);
if(!empty($showModalSwitcher))
	$this->registerJs("$('#myModal').modal({'backdrop':'static'});", $this::POS_READY);
/**/
?>


<div class="requipment-form">

	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'vmware_name')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'description')->textArea(['maxlength' => 2048]) ?>

	<?=
	$form->field($model, 'type')->dropDownList(
		ArrayHelper::map(CTypeEquipment::find()->select('id, name')->asArray()->All(), 'id', 'name'),
		['prompt'=>'Выберите элемент']
	)
	?>

	<?= $form->field($model, 'manufacturer')->textInput(['maxlength' => 128]) ?>

	<?= $form->field($model, 'serial_number')->textInput(['maxlength' => 128]) ?>

	<?= $form->field($model, 'inventory_number')->textInput(['maxlength' => 128]) ?>

	<?php // Создано по заявке ?>
	<?php // многие ко многим - Связь с заявками: --------------------------------------------------------------------?>
	<?php if ($arrEquipmentClaims = $model->nnEquipmentClaims): ?>
		<?= Html::script('var claimMax = '.(count($arrEquipmentClaims)-1), []); ?>
		<?php foreach ($arrEquipmentClaims as $keyv => $v): ?>
			<?php // var_dump($v->claim0->id); ?>
			<?= $form->beginField(new NnEquipmentClaim, "[{$keyv}]claim"); ?>
				<?php if ($keyv == 0): ?>
					<label class="control-label" for="nnequipmentclaim-claim">Создано по заявке</label>
				<?php endif ?>
				<div class="input-group">
					<?= Html::DropDownList(
						// new NnEquipmentClaim,
						"NnEquipmentClaim[{$keyv}][claim]",
						$v->claim0->id,
						ArrayHelper::map(
							SClaim::find()->select('id, name')->asArray()->All(),'id','name'
						),
						[
							// 'id'=>'nnequipmentclaim-'.$keyv.'-claim',
							'prompt'=>'Выберите элемент',
							'class'=>'form-control',
							'style'=>'z-index:0'
						]
					) ?>
					<span class="input-group-btn"></span>
					<span class="input-group-btn">
						<button class="btn btn-success field-create-claim" type="button">+</button>
						<button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
					</span>
				</div>
				<div class="help-block"></div>
			<?= $form->endField(); ?>

		<?php endforeach ?>
	<?php else: ?>
		<?= Html::script('var claimMax = 0', []); ?>
		<?= $form->beginField(new NnEquipmentClaim, '[0]claim'); ?>
			<label class="control-label" for="nnequipmentclaim-claim">Создано по заявке</label>
			<div class="input-group">
				<?= Html::DropDownList(
					'NnEquipmentClaim[0][claim]',
					false,
					ArrayHelper::map(
						SClaim::find()->select('id, name')->asArray()->All(), 'id', 'name'
					),
					[
						'prompt'=>'Выберите элемент',
						'class'=>'form-control',
						'style'=>'z-index:0'
					]
				)?>
				<span class="input-group-btn"></span>
				<span class="input-group-btn">
					<button class="btn btn-success field-create-claim" type="button">+</button>
					<button class="btn btn-danger field-remove disabled" type="button">x</button>
				</span>
			</div>
			<div class="help-block"></div>
		<?= $form->endField(); ?>
	<?php endif ?>
	<div class="form-group">
		<?= Html::a('Добавить новую заявку', ['refs/create', 'refid'=>'SClaim'], ['class' => 'btn btn-success center-block', 'target'=>'_blank']) ?>
	</div>
	<?php //--------------------------------------------------------------------------------------------------------- ?>

	<?php // Дата ввода в эксплуатацию ?>
	<?= $form->field($model, 'date_commission')->widget(\yii\jui\DatePicker::classname(), [
		'language' => 'ru-Ru',
		// 'dateFormat' => 'yyyy-MM-dd',
		'dateFormat' => 'dd.MM.yyyy',
		'options' => [
			'class' => 'form-control',
		]
	]) ?>

	<?php // Дата вывода из эксплуатации ?>
	<?= $form->field($model, 'date_decomission')->widget(\yii\jui\DatePicker::classname(), [
		'language' => 'ru',
		// 'dateFormat' => 'yyyy-MM-dd',
		'dateFormat' => 'dd.MM.yyyy',
		'options' => [
			'class' => 'form-control',
		]
	]) ?>

	<?php // Место размещения ?>
	<?php echo $this->render('_form/_block_splacement', ['model' => $model, 'form' => $form]); ?>

	<?php // Организация-владелец ?>
	<?= $form->beginField($model, 'organization'); ?>
		<?= Html::activeLabel($model, 'organization'); ?>
		<div class="input-group">
			<?= Html::activeDropDownList($model, 'organization', ArrayHelper::map(
				SOrganization::find()->select('id, name')->orderBy('name')->asArray()->All(), 'id', 'name'),
				['prompt'=>'Выберите элемент', 'class'=>'form-control', 'style'=>'z-index:0']) ?>
			<span class="input-group-btn">
				<?= Html::a('Добавить новую организацию', ['refs/create', 'refid'=>'SOrganization'], ['class' => 'btn btn-success', 'target'=>'_blank']) ?>
			</span>
		</div>
	<?= $form->endField() ?>

	<?php // Ответственные лица ?>
	<?php //Ответственные лица многие-ко-многим---------------------------------------------------------------------- ?>
	<?php if ($arrResponseEquipment = $model->sResponseEquipments): ?>
		<?= Html::script('var responseEquipmentMax = '.(count($arrResponseEquipment)-1), []); ?>
		<?php foreach ($arrResponseEquipment as $keyv => $v): ?>
			<?php // var_dump($v->); ?>
			<?= $form->beginField(new SResponseEquipment, "[{$keyv}]sresponseequipments"); ?>
				<?php if ($keyv == 0): ?>
					<label class="control-label" for="sresponseequipment-sresponseequipments">Ответственные лица</label>
				<?php endif ?>
			<div class="input-group">
				<?= Html::DropDownList(
					"SResponseEquipment[{$keyv}][response_person]",
					!is_null($v->responsePerson) ? $v->responsePerson->id : false,
					ArrayHelper::map(
						User::find()
							->select(['user.id as id', 'concat(
															user.last_name, " ",
															user.first_name, " ",
															user.second_name, " (",
															so.name, " )"
														) as name'])
							->join('INNER JOIN', 's_organization so', 'so.id = user.organization')
							->orderBy('user.last_name, user.first_name, user.second_name')
							->asArray()
							->All(), 'id', 'name'
					),
					[
						'prompt'=>'Ответственное лицо: выберите элемент',
						'class'=>'form-control',
						'style'=>'z-index:0'
					]
				)?>
				<span class="input-group-btn" style="width:0px"></span>
				<?= Html::DropDownList(
					"SResponseEquipment[{$keyv}][responsibility]",
					!is_null($v->responsibility0) ? $v->responsibility0->id : false,
					ArrayHelper::map(
						CVariantResponsibility::find()
							->select('id, name')
							->orderBy('name')
							->asArray()
							->All(), 'id', 'name'
					),
					[
						'prompt'=>'Вид ответственности: выберите элемент',
						'class'=>'form-control',
						'style'=>'z-index:0'
					]
				)?>
				<span class="input-group-btn" style="width:0px"></span>
				<?= Html::DropDownList(
					"SResponseEquipment[{$keyv}][legal_doc]",
					!is_null($v->legalDoc) ? $v->legalDoc->id : false,
					ArrayHelper::map(
						RLegalDoc::find()
							->select(['r_legal_doc.id', 'concat(
															ctld.name, ", ",
															if(r_legal_doc.date, concat(date_format(`r_legal_doc`.`date`, "%d.%m.%Y"),", "), ""),
															r_legal_doc.name
														) as name'])
							->join('INNER JOIN', 'c_type_legal_doc ctld', 'ctld.id = r_legal_doc.type')
							->orderBy('ctld.name, r_legal_doc.date, r_legal_doc.name')
							->asArray()
							->All(), 'id', 'name'
					),
					[
						'prompt'=>'Ссылка на приказ: выберите элемент',
						'class'=>'form-control',
						'style'=>'z-index:0'
					]
				)?>
				<span class="input-group-btn"></span>
				<span class="input-group-btn">
					<button class="btn btn-success field-create-sresponseequipment" type="button">+</button>
					<button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
				</span>
			</div>
				<div class="help-block"></div>
			<?= $form->endField(); ?>
		<?php endforeach ?>
	<?php else: ?>
		<?= Html::script('var responseEquipmentMax = 0', []); ?>
		<?= $form->beginField(new SResponseEquipment, '[0]sresponseequipments'); ?>
			<label class="control-label" for="sresponseequipment-sresponseequipments">Ответственные лица</label>
			<div class="input-group">
				<?= Html::DropDownList(
					'SResponseEquipment[0][response_person]',
					false,
					ArrayHelper::map(
						User::find()
							->select(['user.id as id', 'concat(
															user.last_name, " ",
															user.first_name, " ",
															user.second_name, " (",
															so.name, " )"
														) as name'])
							->join('INNER JOIN', 's_organization so', 'so.id = user.organization')
							->orderBy('user.last_name, user.first_name, user.second_name')
							->asArray()
							->All(), 'id', 'name'
					),
					[
						'prompt'=>'Ответственное лицо: выберите элемент',
						'class'=>'form-control',
						'style'=>'z-index:0'
					]
				)?>
				<span class="input-group-btn" style="width:0px"></span>
				<?= Html::DropDownList(
					'SResponseEquipment[0][responsibility]',
					false,
					ArrayHelper::map(
						CVariantResponsibility::find()
							->select('id, name')
							->orderBy('name')
							->asArray()
							->All(), 'id', 'name'
					),
					[
						'prompt'=>'Вид ответственности: выберите элемент',
						'class'=>'form-control',
						'style'=>'z-index:0'
					]
				)?>
				<span class="input-group-btn" style="width:0px"></span>
				<?= Html::DropDownList(
					'SResponseEquipment[0][legal_doc]',
					false,
					ArrayHelper::map(
						RLegalDoc::find()
							->select(['r_legal_doc.id', 'concat(
															ctld.name, ", ",
															if(r_legal_doc.date, concat(date_format(`r_legal_doc`.`date`, "%d.%m.%Y"),", "), ""),
															r_legal_doc.name
														) as name'])
							->join('INNER JOIN', 'c_type_legal_doc ctld', 'ctld.id = r_legal_doc.type')
							->orderBy('ctld.name, r_legal_doc.date, r_legal_doc.name')
							->asArray()
							->All(), 'id', 'name'
					),
					[
						'prompt'=>'Ссылка на приказ: выберите элемент',
						'class'=>'form-control',
						'style'=>'z-index:0'
					]
				)?>
				<span class="input-group-btn"></span>
				<span class="input-group-btn">
					<button class="btn btn-success field-create-sresponseequipment" type="button">+</button>
					<button class="btn btn-danger field-remove disabled" type="button">x</button>
				</span>
			</div>
			<div class="help-block"></div>
		<?= $form->endField(); ?>
	<?php endif ?>
	<div class="form-group">
		<?= Html::a('Добавить новое ответственное лицо', ['user/create'], ['class' => 'btn btn-success center-block', 'target'=>'_blank']) ?>
	</div>
	<?php //Конец: Ответственные лица многие-ко-многим--------------------------------------------------------------- ?>

	<?php // Количество ядер ?>
	<?= $form->field($model, 'count_cores')->textInput(['maxlength' => 5]) ?>

	<?php // Объем ОЗУ ?>
	<?= $form->field($model, 'amount_ram')->textInput(['maxlength' => 8, 'placeholder'=>'В гигабайтах', 'value' => $model->amount_ram ? $model->amount_ram / 10 : ''])->label(null, ['title'=>'В гигабайтах']) ?>

	<?php // Объем HDD ?>
	<?= $form->field($model, 'volume_hdd')->textInput(['maxlength' => 8, 'placeholder'=>'В гигабайтах', 'value' => $model->volume_hdd ? $model->volume_hdd * 5 : ''])->label(null, ['title'=>'В гигабайтах']) ?>

	<?php // IP-адреса ?>
	<?php /* ip-адреса многие-ко-многим*/ ?>
	<?php //--------------------------------------------------------------------------------------------------------- ?>
	<?php // Связь c vlan-сетями: ?>
	<?php if ($arrVlanNets = $model->nnEquipmentVlans): ?>
		<?= Html::script('var cntrVlanMax = '.(count($arrVlanNets)-1), []); ?>
		<?php foreach ($arrVlanNets as $keyv => $v): ?>
			<?php // var_dump($v->); ?>
			<?= $form->beginField(new NnEquipmentVlan, "[{$keyv}]vlan_net"); ?>
				<?php if ($keyv == 0): ?>
					<label class="control-label" for="nnequipmentvlan-vlan_net">IP-адреса</label>
				<?php endif ?>
				<div class="input-group">
					<?= Html::input(
						'text',
						"NnEquipmentVlan[{$keyv}][ip_address]",
						long2ip($v->ip_address),
						['class' => 'form-control', 'placeholder'=>'Введите IP-адрес в формате 10.0.1.2'/*, 'required'=>true*/
						,'pattern' => '((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)']
					) ?>
					<span class="input-group-btn" style="width:0px"></span>
					<?= Html::input(
						'text',
						"NnEquipmentVlan[{$keyv}][mask_subnet]",
						long2ip($v->mask_subnet),
						['class' => 'form-control', 'placeholder'=>'Введите маску подсети в фор. 255.255.255.0'/*, 'required'=>true*/
						,'pattern' => '((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)']
					) ?>
					<span class="input-group-btn" style="width:0px"></span>
					<?= Html::DropDownList(
						"NnEquipmentVlan[{$keyv}][vlan_net]",
						!is_null($v->vlan_net) ? $v->vlanNet->id : false,
						ArrayHelper::map(
							CVlanNet::find()->select('id, name')->asArray()->All(),'id','name'
						),
						[
							'prompt'=>'Vlan-сеть: выберите элемент',
							'class'=>'form-control',
							'style'=>'z-index:0'
						]
					) ?>
					<span class="input-group-btn"></span>
					<span class="input-group-btn">
						<button class="btn btn-success field-create-vlan_net" type="button">+</button>
						<button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
					</span>
				</div>
				<div class="help-block"></div>
			<?= $form->endField(); ?>

		<?php endforeach ?>
	<?php else: ?>
		<?= Html::script('var cntrVlanMax = 0', []); ?>
		<?= $form->beginField(new NnEquipmentVlan, '[0]vlan_net'); ?>
			<label class="control-label" for="nnequipmentvlan-vlan_net">IP-адреса</label>
			<div class="input-group">
				<?= Html::input(
					'text',
					'NnEquipmentVlan[0][ip_address]',
					'',
					['class' => 'form-control', 'placeholder'=>'Введите IP-адрес в формате 10.0.1.2'/*, 'required'=>true*/
					,'pattern' => '((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)']
				) ?>
				<span class="input-group-btn" style="width:0px"></span>
				<?= Html::input(
					'text',
					'NnEquipmentVlan[0][mask_subnet]',
					'',
					['class' => 'form-control', 'placeholder'=>'Введите маску подсети в фор. 255.255.255.0'/*, 'required'=>true*/
					,'pattern' => '((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)']
				) ?>
				<span class="input-group-btn" style="width:0px"></span>
				<?= Html::DropDownList(
					'NnEquipmentVlan[0][vlan_net]',
					false,
					ArrayHelper::map(
						CVlanNet::find()->select('id, name')->asArray()->All(), 'id', 'name'
					),
					[
						'prompt'=>'Vlan-сеть: выберите элемент',
						'class'=>'form-control',
						'style'=>'z-index:0'
					]
				)?>
				<span class="input-group-btn"></span>
				<span class="input-group-btn">
					<button class="btn btn-success field-create-vlan_net" type="button">+</button>
					<button class="btn btn-danger field-remove disabled" type="button">x</button>
				</span>
			</div>
			<div class="help-block"></div>
		<?= $form->endField(); ?>
	<?php endif ?>
	<div class="form-group">
		<?= Html::a('Связать IP-адреса с DNS-именами', ['refs/create', 'refid' => 'SIpdnsTable'], ['class' => 'btn btn-success center-block', 'target'=>'_blank']) ?>
	</div>
	<?php //-конец ip-адреса----------------------------------------------------------------------------------------- ?>

	<?php // открытые порты ?>
	<?php echo $this->render('_form/_block_sopenport', ['model' => $model, 'form' => $form]); ?>

	<?php // Доступ в кспд ?>
	<?= $form->field($model, 'access_kspd')->checkbox() ?>

	<?php // Доступ в интернет ?>
	<?= $form->field($model, 'access_internet')->checkbox() ?>

	<?php // Соединение с argsight ?>
	<?= $form->field($model, 'connect_arcsight')->checkbox() ?>

	<?php // Удаленный доступ ?>
	<?php //= $form->field($model, 'access_remote')->dropDownList($tempRes = ArrayHelper::map(SVariantAccessRemote::find()->select('id, variant')->asArray()->All(), 'id', 'variant'), ['prompt'=>'Нет']) ?>
	<?= $form->field($model, 'access_remote')->dropDownList(ArrayHelper::map(Yii::$app->db->createCommand(
			'select sv.id as id, concat(ct.name, ", ", sv.variant) as variant
			   from s_variant_access_remote sv, c_type_access_remote ct
			  where sv.type = ct.id'
				)->queryAll(), 'id', 'variant'), ['prompt'=>'Выберите элемент']) ?>

	<?php // Проброс в интернет ?>
	<?= $form->field($model, 'icing_internet')->textInput(['maxlength' => 512]) ?>

	<?php // Родительское оборудование ?>
	<?= $form->field($model, 'parent')->dropDownList(
			$tempRes = ArrayHelper::map(
				$model::find()
					->select(['r_equipment.id as id', 'concat(r_equipment.name, ", ",cte.name) as name'])
					->join('INNER JOIN', 'c_type_equipment cte', 'cte.id = r_equipment.type')
					->where('r_equipment.id != :id', ['id'=> is_null($model->id) ? false : $model->id])
					->orderBy('name')
					->asArray()
					->All(),
				'id',
				'name'
			),
			['prompt'=>'Выберите элемент']
		) ?>

	<?php // документация ?>
	<?php echo $this->render('_form/_block_rdocumentation', ['model' => $model, 'form' => $form]); ?>

	<?php // Программное обеспечение ?>
	<?php echo $this->render('_form/_block_rsoftinstalled', ['model' => $model, 'form' => $form]); ?>

	<?php
	// Пароли
	if ($model->showPassInputInForm) {
		echo $this->render('_form/_block_rpassword', ['model' => $model, 'form' => $form]);
	}
	?>

	<?php // Группы доступа ?>
	<?php echo $this->render('_form/_block_usergroups', ['model' => $model, 'form' => $form, 'isCreateScena' => $isCreateScena]); ?>


	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
<?php
// далее скрипты на JS
$search = <<< JS
	// для динамически добавляемых полей используем делегирование через on
	jQuery(document).on('click', '.field-remove',function(event) {
		$(this).parent().parent().parent().remove();
		// alert('test');
		event.preventDefault();
	});
	//отвественные лица---этот блок почти по феншую
	jQuery(document).on('click', '.field-create-sresponseequipment',function(event) {
		var newel;
		$(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
		newel.children('label').remove();
		responseEquipmentMax++;
		newel.attr('class', 'form-group field-sresponseequipment-'+(responseEquipmentMax)+'customgroup' );
		newel.find('select[name *= "response_person"]').attr({
			'name': 'SResponseEquipment['+(responseEquipmentMax)+'][response_person]',
			'id': 'sresponseequipment-'+(responseEquipmentMax)+'-response_person'
		});
		newel.find('select[name *= "responsibility"]').attr({
			'name': 'SResponseEquipment['+(responseEquipmentMax)+'][responsibility]',
			'id': 'sresponseequipment-'+(responseEquipmentMax)+'-responsibility'
		});
		newel.find('select[name *= "legal_doc"]').attr({
			'name': 'SResponseEquipment['+(responseEquipmentMax)+'][legal_doc]',
			'id': 'sresponseequipment-'+(responseEquipmentMax)+'-legal_doc'
		});
		newel.find('button').removeClass('disabled');
	});
	//вланы и айпишники-----------------------------
	jQuery(document).on('click', '.field-create-vlan_net',function(event) {
		var newel;
		$(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
		newel.children('label').remove();
		cntrVlanMax++;
		var ninputClass =
			'form-group field-nnequipmentvlan-'+(cntrVlanMax)+'-vlan_net required';
		newel.attr('class', ninputClass);
		var ninputName = 'NnEquipmentVlan['+(cntrVlanMax)+'][vlan_net]';
		newel.find('select').attr('name', ninputName);
		var ninputId = 'nnequipmentvlan-'+(cntrVlanMax)+'-vlan_net';
		newel.find('select').attr('id', ninputId);
		newel.find('input:first-of-type').attr('name', 'NnEquipmentVlan['+(cntrVlanMax)+'][ip_address]');
		newel.find('input:first-of-type').val('');
		newel.find('input:last-of-type').attr('name', 'NnEquipmentVlan['+(cntrVlanMax)+'][mask_subnet]');
		newel.find('input:last-of-type').val('');
		newel.find('button').removeClass('disabled');
	});
	//заявки
	easyInputManage('claim','claimMax','NnEquipmentClaim');

	function easyInputManage(elEnty, varMaxName, relationName){
		jQuery(document).on('click', '.field-create-'+elEnty, function(event) {
			var newel;
			$(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
			newel.children('label').remove();
			window[varMaxName]++;
			var ninputClass =
				'form-group field-'+relationName.toLowerCase()+'-'+(window[varMaxName])+'-'+elEnty+' required';
			newel.attr('class', ninputClass);
			var ninputName = relationName+'['+(window[varMaxName])+']['+elEnty+']';
			newel.find('select').attr('name', ninputName);
			var ninputId = relationName.toLowerCase()+'-'+(window[varMaxName])+'-'+elEnty;
			newel.find('select').attr('id', ninputId);
			newel.find('button').removeClass('disabled');
		});
	}
JS;
	$this->registerJs($search, View::POS_READY);
	// where $position can be View::POS_READY (the default),
	// or View::POS_HEAD, View::POS_BEGIN, View::POS_END