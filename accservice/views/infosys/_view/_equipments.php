<?php

use app\models\reference\SIpdnsTable;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\utils\Utils;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RInformationSystem */

if (empty($model->nnEquipmentInfosysContours)) {
    echo '<i style="color:#c55;">(не задано)</i>';
    return;
}

$modelsNNEIC = $model->getNnEquipmentInfosysContours()->orderBy("contour")->all();
if (empty($modelsNNEIC)) {
    return false;
}

$mappedData = [];
foreach ($modelsNNEIC as $mnValue) {
    $mappedData[$mnValue->contourModel->name][] = $mnValue;
}

?>

<?php foreach ($mappedData as $mappedDataKey => $mappedDataValue): ?>

        <div style="padding: 0px 0 20px 0;font-size: 16px">
            <strong><?= $mappedDataKey ?></strong>
        </div>

        <?php foreach ($mappedDataValue as $nnEquipmentInfosysContour): ?>
            <div style="padding-left: 55px">
                <span style="margin-left:   -10px;font-weight: bold"><?= $nnEquipmentInfosysContour->equipmentModel->type0->name, ":<br>" ?></span>
                <span>HOSTNAME: <?= Html::a($nnEquipmentInfosysContour->equipmentModel->name, ['equip/view', 'id' => $nnEquipmentInfosysContour->equipment]), "<br>" ?></span>
                <span>VMware-имя: <?= $nnEquipmentInfosysContour->equipmentModel->vmware_name ?></span><br>
                <span><span style="color:green">Время последнего бекапа: </span><?= Yii::$app->formatter->asDatetime($nnEquipmentInfosysContour->equipmentModel->last_backuped) ?></span>
                <div style="padding: 0 0 15px 0">
                    <?php
                    $dnsNames = '';
                    $query = SIpdnsTable::find()
                        ->select('s_ipdns_table.id, s_ipdns_table.ip_address, s_ipdns_table.dns_name')
                        ->join('INNER JOIN', 'nn_equipment_vlan nev', 'nev.ip_address = s_ipdns_table.ip_address')
                        ->join('INNER JOIN', 'r_equipment e', 'e.id = nev.equipment')
                        ->where(['e.id' => $nnEquipmentInfosysContour->equipmentModel->id])
                        ->asArray()
                        /*->all()*/;
                    $dnsNamesArr = [];
                    foreach ($query->each() as $items) {
                        $dnsNames .= $items['dns_name'].' -> '.long2ip($items['ip_address']).'<br/>';
                        $dnsNamesArr[long2ip($items['ip_address'])][] = $items['dns_name'];
                    }
                    $arrVlanHtml = '';
                    foreach ($nnEquipmentInfosysContour->equipmentModel->nnEquipmentVlans as $k => $v) {
                        $n = $k+1;
                        $vlanNetName = !is_null($v->vlan_net) ? Html::a($v->vlanNet->name." ({$v->vlanNet->description})", '#', ['onclick'=>'
                                var mdl = $("#information-modal");
                                var text = mdl.find(".modal-header h4").text();
                                mdl.modal("show").find(".modal-header h4").text("'.$v->vlanNet->name." ({$v->vlanNet->description})".'");
                                $.post("'.Url::to(['equip/get-vlan-list']).'", { id : "'.$v->vlanNet->id.'"}, function(data) {
                                    $("#information-modal-body").html(data);
                                });
                                return false;
                            ']) : 'нет';
        //                $arrVlanHtml .= "<div>{$n}.
        //                    IP: <strong>".long2ip($v->ip_address)."</strong><br>
        //                    <span style='margin-left:30px'> Маска: ".Utils::maskToBin($v->mask_subnet)."
        //                    (".long2ip($v->mask_subnet).") </span><br>
        //                    <span style='margin-left:30px'> VLAN: <strong>{$vlanNetName}</strong></span><br>
        //                </div>";
                        $arrVlanHtml .= "<div>{$n}.
                            IP: <strong>".long2ip($v->ip_address)."</strong><br>
                        </div>";
                        if ( isset($dnsNamesArr[long2ip($v->ip_address)]) ) {
                            foreach ( $dnsNamesArr[long2ip($v->ip_address)] as $valueDnsName) {
                                // $arrVlanHtml .= "<div>DNS: $valueDnsName</div>";
                                $arrVlanHtml .= "<div style='margin-left:30px'>DNS: ".Html::a($valueDnsName, 'http://'.$valueDnsName,['target'=>'__blank'])."</div>";
                            }
                        }
                        $arrVlanHtml .= '';
                    }

                    echo $arrVlanHtml;
                    ?>


                </div>
            </div>
        <?php endforeach ?>


<?php endforeach; ?>


