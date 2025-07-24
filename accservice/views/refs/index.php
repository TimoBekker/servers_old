<?php
/* индексная (админская) страница текущего справочника */
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $refTitle;
$this->params['breadcrumbs'][] = ['label' => 'Редактирование справочников', 'url' => ['refs/index']];
$this->params['breadcrumbs'][] = $this->title;
// var_dump($this->params['breadcrumbs']);exit;
?>

<?php if (isset($notdeleted) && $notdeleted === true): ?>
    <?php // Модальное диалоговое окно ?>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <?php // <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> ?>
                    <h4 class="modal-title" id="myModalLabel">
                        <?= $modalName = 'Данный элемент справочника невозможно удалить' ?>
                    </h4>
                </div>
                <div class="modal-body">
                    Удаление данного элемента справочника станет<br/>
                    возможным только после удаления всех записей<br/>
                    системы, использующих этот элемент !
                </div>
                <div class="modal-footer">
                    <?= Html::a('Закрыть', ['refs/index', 'refid' => $refname], ['class' => 'btn btn-primary'])?>
                </div>
            </div>
        </div>
    </div>
    <?php
    $this->registerJs("$('#myModal').modal({'backdrop':'static'});", $this::POS_READY);
    ?>
<?php endif ?>

<div class="ctype-equipment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a("Создать элемент справочника", ['create', 'refid'=>$refname], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
        // продвинутые справочники отображаем своими партиалами
        switch ($refname) {
            case 'SClaim':
            case 'CAgreement':
            case 'SOrganization':
            case 'SOpenPort':
            // case 'SPlacement': // здесь все просто, поэтому отображаем как простые справочники
            case 'SVariantAccessRemote':
            case 'SContractor':
            case 'SResponseWarrantySupport':
            case 'SResponseEquipment':
            case 'SResponseInformationSystem':
            case 'SIpdnsTable':
                echo $this->render('advindex/_ind_'.mb_strtolower($refname), [
                        'classname' => $classname,
                        'refname' => $refname,
                        'refTitle' => $classname::$tableLabelName,
                        'dataProvider' => $dataProvider,
                    ]);
                break;

            default:
                // динамически в зависимости от модели подготавливаем выводимые поля
                $attributes = (new $classname)->attributes();
                unset($attributes[array_search('id', $attributes)]);
                $columns = [
                    ['class' => 'yii\grid\SerialColumn'],
                    // 'id',
                    // 'name',
                    // 'address',
                    // 'phone',
                    // 'site',
                    // 'parent' => [
                    //     'attribute' => 'parent',
                    //     // 'label'=>'Альтернативный загловок столбца',
                    //     // следующая вставка очень кривая, но как сделать по другому пока моего xp yii не хватило
                    //     'value' => function ($model, $key, $index, $column) {
                    //         if($model->findOne($key)->parent != null)
                    //            return $model->findOne($model->findOne($key)->parent)->name;
                    //     }
                    // ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=> 'Действия',
                        'template'=>'{update} &nbsp; {delete}',
                        'urlCreator'=>function($action, $model, $key, $index) use ($refname) {
                            return [$action, 'refid'=>$refname, 'id' => $key];
                        },
                        'headerOptions' => ['style'=>'text-align: center;'],
                        'contentOptions' => ['style'=>'text-align: center;'],
                    ],
                ];
                array_splice($columns, -1, 0, $attributes);
                // var_export($columns);exit;

                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $columns,
                ]);

                break;
        }
    ?>
</div>
