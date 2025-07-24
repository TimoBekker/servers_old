<?php
use app\models\registry\REvent;
use yii\helpers\Html;
// var_dump($events);
/* @var $this yii\web\View */
$this->title = 'Система учета серверов';
?>
<div class="site-index">
    <div class="body-content">
        <h1>
            События
            <?php /*
            <br>
            <p style="margin-left:100px;">
            <span style='color:#E0E0E0;'>&#9679;</span><span style='font-size: 20px;'> - завершенные</span>
            <span style='color:#F2DBDB;'>&#9679;</span><span style='font-size: 20px;'> - текущие</span>
            <span style='color:#DBF2E2;'>&#9679;</span><span style='font-size: 20px;'> - предстоящие</span>
            </p>
            */ ?>
        </h1>
        <?php

        $pr = count($events['prevent']);
        $cr = count($events['current']);
        $nx = count($events['next']);

        $crPrint = $cr;
        if($nx >= 2)$nxPrint = 2;else $nxPrint = $nx;
        if($nxPrint==0&&$crPrint==0)$prPrint = 3;
        elseif($crPrint==0 || $nxPrint==0)$prPrint=2;
        else$prPrint=1;
        if($prPrint>$pr)$prPrint=$pr;
        // echo -$prPrint,'<br>';
        // echo $crPrint,'<br>';
        // echo $nxPrint,'<br>';

        function printEvents(array $arrEvents, $cntEvents, $way, $bgColor, $fntColor="#000") {
            $rusmon = [
                'Jan' => 'Янв',
                'Feb' => 'Фев',
                'Mar' => 'Март',
                'Apr' => 'Апр',
                'May' => 'Май',
                'Jun' => 'Июнь',
                'Jul' => 'Июль',
                'Aug' => 'Авг',
                'Sep' => 'Сен',
                'Oct' => 'Окт',
                'Nov' => 'Нояб',
                'Dec' => 'Дек',
            ];
            if($way > 0){
                $arrEvents = array_slice($arrEvents, 0, $cntEvents);
            }elseif($way < 0){
                $arrEvents = array_slice($arrEvents, -$cntEvents);
            }
            foreach ($arrEvents as $key => $model) {
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-left" style="">
                            <div class="text-center" style="width:140px; padding:40px 0px 30px 0px;margin-top:5px;/*color:<?=$fntColor?>;*/ background:url(img/maincalendar.jpg) center no-repeat; background-size:contain">
                                <span style="font: bold 20px Arial, sans-serif"><?=(new \Datetime($model->date_begin))->format('d')?></span><br>
                                <?php $mon = (new \Datetime($model->date_begin))->format('M') ?>
                                <span style="font: bold 20px Arial, sans-serif"><?=$rusmon[$mon]?></span>
                            </div>
                        </div>
                        <div style="background:<?=$bgColor?>;margin:5px 0 15px 0;padding:10px 3px 1px 170px; box-shadow:1px 5px 10px rgba(102, 102, 102, 0.5)">
                            <h4 style='color:saddlebrown;font-size:20px'><?= $model->name ?></h4><hr style="border-color:#FFF">
                            <p class='' style='padding-bottom:10px;'>
                                Начало: <strong><?=(new \Datetime($model->date_begin))->format('d.m.Y H:i')?></strong>
                                Окончание: <strong><?=(new \Datetime($model->date_end))->format('d.m.Y H:i')?></strong><br>
                                Тип: <strong><?= $model->type0->name ?></strong><br>
                                Описание: <strong><?= $model->description ?></strong><br>
                                    <?php $arrEquipes = $model->nnEventEquipments ?>
                                <?php echo $arrEquipes ? 'Задействованное оборудование:<br>' : '' ?>
                                    <?php foreach ($arrEquipes as $keyEq => $valEq): ?>
                                        &nbsp;&nbsp;&nbsp;
                                        <strong>
                                        <?= Html::a(
                                            $valEq->equipment0->name,
                                            ['equip/view', 'id' => $valEq->equipment0->id],
                                            ['class' => 'profile-link']
                                        ) ?>
                                        </strong><br/>
                                    <?php endforeach ?>
                                    <?php $arrInfosys = $model->nnEventIs ?>
                                <?php echo $arrInfosys ? 'Задействованные системы:<br>' : '' ?>
                                    <?php foreach ($arrInfosys as $keyIs => $valIs): ?>
                                        &nbsp;&nbsp;&nbsp;
                                        <strong>
                                        <?= Html::a(
                                            $valIs->informationSystem->name_short,
                                            ['infosys/view', 'id' => $valIs->informationSystem->id],
                                            ['class' => 'profile-link']
                                        ) ?>
                                        </strong><br/>
                                    <?php endforeach ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php
            }
        }

        printEvents(array_reverse($events['next']), $nxPrint, 1,'#DBF2E2', '#45CC45');
        printEvents(array_reverse($events['current']), $crPrint, 0,'#F2DBDB', '#CC4545');
        printEvents(array_reverse($events['prevent']), $prPrint, -1,'#EEEEEE', '#8C8C8C');
        ?>

    </div>
</div>
