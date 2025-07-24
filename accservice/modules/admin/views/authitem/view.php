<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AuthItem */

$this->title = $model->alias;
$this->params['breadcrumbs'][] = ['label' => 'Управление группами и правами', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($model->type === 2): ?>
            <?= Html::a('Редактировать', ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->name], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите удалить данный элемент?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif ?>
    </p>

    <?php

    /* Подготовка данных */

    $innerGroups = function() use($model) {
        $html = '';
        if ($groupNames = $model->recursiveChildren){
            $html .= '<ul>';
            foreach ($groupNames as $groupName) {
                $tm = $model->findOne($groupName);
                if ($tm->type === 2 || $tm->type === 3) // Нас интересуют только Простые Группы Типа 2 и автогруппы пользователей 3
                    $html .= '<li>'.Html::a($tm->alias, ['view', 'id' => $groupName]).'</li>';
            }
            $html .= '</ul>';
            if ($html == '<ul></ul>') $html = ''; // избавл.от пуст списка, если вс-ки были группы, но которые мы не выводим
        }
        return $html;
    };
    $outGroups = function() use($model) {
        $html = '';
        if ($groupNames = $model->recursiveParents){
            $html .= '<ul>';
            foreach ($groupNames as $groupName) {
                $tm = $model->findOne($groupName);
                if ($tm->type === 2) // Нас интересуют только Простые Группы Типа 2
                    $html .= '<li>'.Html::a($tm->alias, ['view', 'id' => $groupName]).'</li>';
            }
            $html .= '</ul>';
            if ($html == '<ul></ul>') $html = ''; // избавл.от пуст списка, если вс-ки были группы, но которые мы не выводим
        }
        return $html;
    };
    $accessRules = function() use($model) {
        $html = '';
        if ($ruleNames = $model->aviableRules){
            $html .= '<ul>';
            foreach ($ruleNames as $ruleName) {
                $tm = $model->findOne($ruleName);
                    $html .= '<li>'.$tm->alias.'</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    };
    $accessRulesAll = function() use($model) {
        $html = '';
        if ($ruleNames = $model->aviableRulesRecursive){
            $html .= '<ul>';
            foreach ($ruleNames as $ruleName) {
                $tm = $model->findOne($ruleName);
                    $html .= '<li>'.$tm->alias.'</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    };
    $chainEquip = function() use($model) {
        $html = '';
        if ($nnaieqs = $model->nnAuthitemEquipments) {
            $html .= '<ul>';
            foreach ($nnaieqs as $nnaq) {
                 $html .= '<li>'.Html::a($nnaq->equipment0->name, ['/equip/view', 'id' => $nnaq->equipment]).'</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    };
    $chainEquipAll = function() use($model) {
        $html = '';
        if ($nnaieqs = $model->nnAuthitemEquipmentsRecursive) {
            $html .= '<ul>';
            foreach ($nnaieqs as $nnaq) {
                 $html .= '<li>'.Html::a($nnaq->equipment0->name, ['/equip/view', 'id' => $nnaq->equipment]).'</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    };
    $chainInfosys = function() use($model) {
        $html = '';
        if ($nnaiiss = $model->nnAuthitemIs) {
            $html .= '<ul>';
            foreach ($nnaiiss as $nnai) {
                 $html .= '<li>'.Html::a($nnai->informationSystem->name_short, ['/infosys/view', 'id' => $nnai->information_system]).'</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    };
    $chainInfosysAll = function() use($model) {
        $html = '';
        if ($nnaiiss = $model->nnAuthitemIsRecursive) {
            $html .= '<ul>';
            foreach ($nnaiiss as $nnai) {
                 $html .= '<li>'.Html::a($nnai->informationSystem->name_short, ['/infosys/view', 'id' => $nnai->information_system]).'</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    };
    $chainSoft = function() use($model) {
        $html = '';
        if ($nnaiss = $model->nnAuthitemSoftwares) {
            $html .= '<ul>';
            foreach ($nnaiss as $nn) {
                 $html .= '<li>'.Html::a($nn->software0->name, ['/soft/view', 'id' => $nn->software]).'</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    };
    $chainSoftAll = function() use($model) {
        $html = '';
        if ($nnaiss = $model->nnAuthitemSoftwaresRecursive) {
            $html .= '<ul>';
            foreach ($nnaiss as $nn) {
                 $html .= '<li>'.Html::a($nn->software0->name, ['/soft/view', 'id' => $nn->software]).'</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    };

    /* Вывод данных */

    $notset = '<i style="color:#c55">(не задано)</i>'
    ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'alias',
            // 'name',
            // 'type',
            'description:ntext' => [
                'attribute' => 'description',
                'format' => 'ntext',
                'value' => $model->description ?: null,
            ],
            // 'rule_name',
            // 'data:ntext',
            // 'created_at',
            // 'updated_at',
            [
                'label' => 'Входящие группы (рекурсивно)',
                'format' => 'html',
                'value'=> $innerGroups() ?: $notset,
            ],
            [
                'label' => 'Входит в состав групп (рекурсивно)',
                'format' => 'html',
                'value'=> $outGroups() ?: $notset,
            ],
            [
                'label' => 'Разрешенные правила (собственные)',
                'format' => 'html',
                'value'=> $accessRules() ?: $notset,
            ],
            [
                'label' => 'Разрешенные правила (вместе с наследуемыми от родительский групп)',
                'format' => 'html',
                'value'=> $accessRulesAll() ?: $notset,
            ],
            [
                'label' => 'Связанное оборудование (собственное)',
                'format' => 'html',
                'value'=> $chainEquip() ?: $notset,
            ],
            [
                'label' => 'Связанное оборудование (вместе с наследуемым от родительский групп)',
                'format' => 'html',
                'value'=> $chainEquipAll() ?: $notset,
            ],
            [
                'label' => 'Связанные информационные системы (собственные)',
                'format' => 'html',
                'value'=> $chainInfosys() ?: $notset,
            ],
            [
                'label' => 'Связанные информационные системы (вместе с наследуемыми от родительский групп)',
                'format' => 'html',
                'value'=> $chainInfosysAll() ?: $notset,
            ],
            [
                'label' => 'Связанные дистрибутивы (собственные)',
                'format' => 'html',
                'value'=> $chainSoft() ?: $notset,
            ],
            [
                'label' => 'Связанные дистрибутивы (вместе с наследуемыми от родительский групп)',
                'format' => 'html',
                'value'=> $chainSoftAll() ?: $notset,
            ],
        ],
    ]) ?>

</div>
