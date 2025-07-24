<?php

use app\models\relation\NnEquipmentInfosysContour;
use yii\helpers\Url;

/* @var NnEquipmentInfosysContour[] $informationSystems */
?>
<div class="col-md-6 need-height">
    <div style="background: aliceblue; height: inherit; padding: 5px;">
        <h5><strong>ИНФОМАЦИОННЫЕ СИСТЕМЫ</strong></h5>
        <?php $contour = 0; ?>
        <?php foreach ($informationSystems as $isystem): ?>
            <?php
                if ($contour !== $isystem->contour) {
                    echo $contour ? "</ul>" : "";
                    $contour = $isystem->contour;
                    echo "<h5>{$isystem->contourModel->name}</h5><ul>";
                }
                echo "<li><a href='". Url::to(["infosys/view", "id"=>$isystem->information_system])."'>{$isystem->informationSystemModel->name_short}</a></li>";
            ?>
        <?php endforeach ?>
        <!-- закрыть последний ul -->
        <?= $contour ? "</ul>" : IT_NOT_DEF; ?>
    </div>
</div>