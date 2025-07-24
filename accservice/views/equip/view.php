<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\relation\NnEquipmentVlan;
use app\models\reference\SIpdnsTable;
use app\models\registry\RDocumentation;

/* @var $this yii\web\View */
/* @var $model app\models\registry\REquipment */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Оборудование', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<div class="requipment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger showdelpopup',
            'data' => [
                // 'confirm' => 'Вы действительно хотите удалить это оборудование?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <h3><?= Html::encode("Основные данные:") ?></h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            'name',
            [
                'attribute' => 'vmware_name',
                'value' => $model->vmware_name ?: null,
            ],
            'type' => [
                // 'label'=>'альтернативный заголовок',
                'attribute' => 'type',
                'value' => $model->type0->name // в этом виде мы можем так использовать Релейшн
            ],
            'description' => [
                'attribute' => 'description',
                'value' => $model->description ?: null
            ],
            'parent' => [
                'attribute' => 'parent',
                'format' => 'html',
                'value' => !is_null($model->parent0)
                    ?
                        Html::a($model->parent0->name, ['view', 'id' => $model->parent])
                    :
                        null
            ],
            'manufacturer' => [
                'attribute' => 'manufacturer',
                'value' => $model->manufacturer ?: null
            ],
            'serial_number' => [
                'attribute' => 'serial_number',
                'value' => $model->serial_number ?: null
            ],
            'inventory_number' => [
                'attribute' => 'inventory_number',
                'value' => $model->inventory_number ?: null
            ],
            'date_commission:date',
            'date_decomission:date',
            'placement' => [
                'attribute' => 'placement',
                'value' => !is_null($model->placement0) ?
                    $model->placement0->region.', г. '.
                    $model->placement0->city.', ул. '.
                    $model->placement0->street.' - '.
                    $model->placement0->house.', каб. '.
                    $model->placement0->office.', шк. '.
                    $model->placement0->locker.', п. '.
                    $model->placement0->shelf
                    : null
            ],
            'organization' => [
                'attribute' => 'organization',
                'format' => 'html',
                'value' => !is_null($model->organization0)
                    ?
                        Html::a($model->organization0->name, ['refs/view', 'refid'=>'SOrganization', 'id' => $model->organization])
                    :
                        null
            ],
            'count_cores' => [
                'attribute' => 'count_cores',
                'label' => 'Количество ядер CPU',
            ],
            'amount_ram' => [
                'attribute' => 'amount_ram',
                'label' => 'Объем ОЗУ',
                'value' => $model->amount_ram ? (string)($model->amount_ram * 0.1).' Гб' : null
            ],
            'volume_hdd' => [
                'attribute' => 'volume_hdd',
                'label' => 'Объем HDD',
                'value' => $model->volume_hdd ? (string)($model->volume_hdd * 5).' Гб' : null
            ],
            'access_kspd' => [
                'attribute' => 'access_kspd',
                'value' => $model->access_kspd ? 'есть' : 'нет' // при проверке тут достаточно автоприведения типов
            ],
            'access_internet' => [
                'attribute' => 'access_internet',
                'value' => $model->access_internet ? 'есть' : 'нет'
            ],
            'connect_arcsight' => [
                'attribute' => 'connect_arcsight',
                'value' => $model->connect_arcsight ? 'есть' : 'нет'
            ],
            'access_remote' => [
                'attribute' => 'access_remote',
                'value' => !is_null($model->accessRemote) ?
                    $model->accessRemote->variant.' '.
                    $model->accessRemote->type0->findOne($model->accessRemote->type)->name // так правильно находит
                    : null
            ],
            'icing_internet' => [
                'attribute' => 'icing_internet',
                'value' => $model->icing_internet ?: null
            ],
        ],
    ]) ?>

    <h3><?= Html::encode("Дополнительные данные:") ?></h3>
    <?php
        // var_dump($model->nnEventEquipments);
        $arrVlanHtml = '';
        foreach ($model->nnEquipmentVlans as $k => $v) {
            // var_dump($v);
            $n = $k+1;
            $vlanNetName = !is_null($v->vlan_net) ? $v->vlanNet->name." ({$v->vlanNet->description})" : 'нет';
            $arrVlanHtml .= "{$n}.
                 ip-адрес: <strong>".long2ip($v->ip_address)."</strong>,
                 маска подсети: <strong>".long2ip($v->mask_subnet)."</strong>,
                 Vlan: <strong>{$vlanNetName}</strong> <br>";
            // $arrVlanHtml .= "{$n}. <a href='".
            //     \Yii::$app->UrlManager->createUrl(['equip/view', 'id'=>$v->equipment0->id]).
            // "'>{$v->equipment0->name}</a><br>";
        }

        // Днс имена
        $dnsNames = '';
        $query = SIpdnsTable::find()
            ->select('s_ipdns_table.id, s_ipdns_table.ip_address, s_ipdns_table.dns_name')
            ->join('INNER JOIN', 'nn_equipment_vlan nev', 'nev.ip_address = s_ipdns_table.ip_address')
            ->join('INNER JOIN', 'r_equipment e', 'e.id = nev.equipment')
            ->where(['e.id' => $model->id])
            ->asArray()
            /*->all()*/;
        foreach ($query->each() as $items) {
            $dnsNames .= $items['dns_name'].' -> '.long2ip($items['ip_address']).'<br/>';
            // var_dump($items);
        }

        // Открытые порты
        $openPorts = '';
        foreach ($model->sOpenPorts as $value) {
            $openPorts .= $value->port_number.' '.$value->protocol0->name.' '.$value->description.'<br/>';
        }

        // Ответственные за оборудование
        $responsePersons = '';
        foreach ($model->sResponseEquipments as $value) {
            $fio = $value->responsePerson->last_name.' '.$value->responsePerson->first_name.' '
                                                                                .$value->responsePerson->second_name;
            $responsePersons .= Html::a($fio, ['user/view', 'id' => $value->responsePerson->id]).' ';
            $responsePersons .= '<ul>';
            $responsePersons .= '<li>'.$value->responsibility0->name.'</li>';
            $responsePersons .= $value->legalDoc
                ?
                    '<li>'.Html::a($value->legalDoc->name, ['ldoc/view', 'id' => $value->legalDoc->id]).'</li>'
                :
                    '';
            $responsePersons .= '</ul>';
            $responsePersons .= '<br/>';
        }

        // Связанные заявки
        $claims = '';
        foreach ($model->nnEquipmentClaims as $value) {
            $docMod = RDocumentation::find()->where(['claim' => $value->claim0->id])->One();
            $claims .= Html::a($value->claim0->name, ['refs/index', 'refid' => 'SClaim','id' => $value->claim0->id]).' ';
            $claims .= $docMod ? Html::a(
                            '',
                            $docMod->url,
                            //'docs/legaldocs/testfile3.docx',
                            ['class'=>'glyphicon glyphicon-download-alt']
                       ).', ' : ', ';
        }
        $claims = mb_substr($claims, 0, -2); // убираем последнюю запятую

        // Документация
        $documents = '';
        foreach ($model->rDocumentations as $value) {
            $documents .= $value->name.' '.$value->description.' ';
            $documents .= Html::a('', $value->url, ['class'=>'glyphicon glyphicon-download-alt']).'<br/><br/>';
        }

        // установленное по
        $installsoft = '';
        foreach ($model->rSoftwareInstalleds as $value) {
            $softlink = $value->software0->name.' ';
            $softlink .= $value->software0->version.' ';
            $installsoft .= Html::a($softlink, ['softins/view', 'id' => $value->id]);
            $installsoft .= '<ul>';
                foreach ($value->nnIsSoftinstalls as $value2) {
                    $installsoft .= '<li>'.
                        Html::a(
                            $value2->informationSystem->name_short, ['infosys/view', 'id'=>$value2->informationSystem->id])
                    .'</li>';
                }
            $installsoft .= '</ul>';
            $installsoft .= '<br/>';
        }

        // пароли
        $passwords = '';
        $passModels = $model->rPasswords;
        if ( $passModels && $accessViewPassword ) {

            $passwords = '<table>';
            foreach ($passModels as $value) {
                if ( $value->finale || $value->next ) continue; // не выводим "Уничтоженные" пароли
                $getpass = "<span class='btn btn-primary btn-sm showpass' style='margin:3px'>Показать пароль<span>";
                $passwords .= sprintf('
                        <tr class="%s">
                            <td>Логин:&nbsp;&nbsp;</td>
                            <td>%s</td>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td>(Описание: %s)</td>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td>%s</td>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td nowrap></td>
                        </tr>
                ', $value->id, $value->login, $value->description ?: 'не задано', $getpass );
            }
            $passwords .= '</table>';
            if ($passwords !== '<table></table>') {
                // далее подключим скрипт отображения пароля
                $search = <<< JS
                    $('.showpass').click(function(event) {
                        var button = $(this);
                        if(button.parent().next().next().text() != ''){
                            button.parent().next().next().text('');
                            button.text('Показать пароль');
                            button.css('background', '');
                            return;
                        }
                        var param1 = $(this).parent().parent().attr('class');
                        var param2 =  button.parent().prev().prev().prev().prev().text();
                        $.post('', {param1: param1, param2: param2}, function(data, textStatus, xhr) {
                            if(textStatus == 'success'){
                                button.text('Скрыть пароль');
                                button.css('background', 'darkred');
                                if(data)
                                    button.parent().next().next().text(data);
                                else
                                    button.parent().next().next().text('Пустой пароль');
                            }
                            else
                                alert('Ошибка! Обратитесь к разработчику!');
                        });
                    });
JS;
                $this->registerJs($search, \yii\web\View::POS_READY);
            } else { $passwords = ''; } // если были пароли, но только те, которые выводить не следует

        } elseif ( !$accessViewPassword ) {

            $passwords = '<i style="color:#c55">Вы не имеете доступа к данному разделу системы</i>';

        }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Связанные IP-адреса и VLAN-сети',
                'format' => 'html',
                'value'=> $arrVlanHtml ? $arrVlanHtml : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связанные DNS-имена',
                'format' => 'html',
                'value'=> $dnsNames ? $dnsNames : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Порты, прослушиваемые на оборудовании',
                'format' => 'html',
                'value'=> $openPorts ? $openPorts : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Ответственные за оборудование',
                'format' => 'html',
                'value'=> $responsePersons ? $responsePersons : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связанные заявки',
                'format' => 'html',
                'value'=> $claims ? $claims : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Документация',
                'format' => 'html',
                'value'=> $documents ? $documents : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Установленное ПО',
                'format' => 'html',
                'value'=> $installsoft ? $installsoft : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Пароли к оборудованию',
                'format' => 'html',
                'value' => $passwords ? $passwords : '<i style="color:#c55">(не задано)</i>',
            ],
        ],
    ]) ?>
</div>