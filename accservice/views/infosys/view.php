<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RInformationSystem */

$this->title = $model->name_short;
$this->params['breadcrumbs'][] = ['label' => 'Информационные системы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<?php Modal::begin([
    'id' => 'information-modal',
    'header' => '<h4></h4>',
    // 'size' => Modal::SIZE_LARGE,
    'options' => [
        'data-backdrop'=>"static",
    ], ]); ?>
<div id = "information-modal-body"></div>
<?php Modal::end(); ?>

<div class="rinformation-system-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger showdelpopup',
            'data' => [
                // 'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <h3><?= Html::encode("Основные данные:") ?></h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            'name_short',
            'name_full',
            'description' => [
                'attribute' => 'description',
                'value' => ($model->description) ?
                    $model->description
                    : null
            ],
            'validation' => [
                'attribute' => 'validation',
                'value' => !is_null($model->validation0) ?
                    $model->validation0->name
                    : null
            ],
            'protection' => [
                'attribute' => 'protection',
                'value' => !is_null($model->protection0) ?
                    $model->protection0->name
                    : null
            ],
            'privacy' => [
                'attribute' => 'privacy',
                'value' => !is_null($model->privacy0) ?
                    $model->privacy0->name
                    : null
            ],
        ],
    ]) ?>

    <h3><?= Html::encode("Дополнительные данные:") ?></h3>
    <?php
        // ответственные лица
        $responsePersons = '';
        foreach ($model->sResponseInformationSystems as $value) {
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

        // контракты
        $contracts = '';
        foreach ($model->nnIsContracts as $value) {
            $date_complete = $value->contract0->date_complete;
            $date_complete = $date_complete && $date_complete != '0000-00-00' ? (new \Datetime($value->contract0->date_complete))->format('d.m.Y') : '';
            $date_end_warranty = $value->contract0->date_end_warranty;
            $date_end_warranty = $date_end_warranty && $date_end_warranty != '0000-00-00' ? (new \Datetime($value->contract0->date_end_warranty))->format('d.m.Y') : '';
            // $cost = money_format('%i', $value->contract0->cost); // функция не определена в Windows
            $cost = $value->contract0->cost ? number_format($value->contract0->cost, 2, ',', ' ').' руб.' : '';
            $docs = $value->contract0->url ?: '';
            $contracts .= Html::a($value->contract0->name, ['contr/view', 'id' => $value->contract0->id]).' ';
            $contracts .= '<ul>';
                $contracts .= $date_complete ? "<li>Окончание работ: {$date_complete}</li>" : '';
                $contracts .= $date_end_warranty ? "<li>Окончание гарантии: {$date_end_warranty}</li>" : '';
                $contracts .= $cost ? "<li>Стоимость: {$cost}</li>" : '';
                $contracts .= $docs ? "<li>Скачать: ".Html::a('', $docs, ['class'=>'glyphicon glyphicon-download-alt'])."</li>" : '';
            $contracts .= '</ul>';
            $contracts .= '<br/>';
        }

        // нпа
        $ldocs = '';
        foreach ($model->nnLdocIs as $value) {
            $date = $value->legalDoc->date;
            $date = $date && $date != '0000-00-00' ? (new \Datetime($value->legalDoc->date))->format('d.m.Y') : '';
            $docs = $value->legalDoc->url ?: '';
            $ldocs .= Html::a($value->legalDoc->name, ['ldoc/view', 'id' => $value->legalDoc->id]).' ';
            $ldocs .= $date ?: '';
            $ldocs .= $value->legalDoc->status ? ' Отменен' : ' Действующий';
            $ldocs .= $docs ? ' Скачать: '.Html::a('', $docs, ['class'=>'glyphicon glyphicon-download-alt']) : '';
            $ldocs .= '<br/>';
        }

        // дистрибутивы по
        $soft = '';
        foreach ($model->nnIsSoftwares as $value) {
            $href = $value->software0->type0->name.' ';
            $href .= $value->software0->name.' ';
            $href .= $value->software0->version.' ';
            $soft .= Html::a($href, ['soft/view', 'id' => $value->software0->id]);
            $soft .= '<br/>';
        }

        // связь с установленным по
        $installsoft = '';
        foreach ($model->nnIsSoftinstalls as $value) {
            $href = $value->softwareInstalled->software0->type0->name.' ';
            $href .= $value->softwareInstalled->software0->name.' ';
            $href .= $value->softwareInstalled->software0->version.' ';
            $equip = $value->softwareInstalled->equipment0->name;
            $installsoft .= Html::a($href, ['softins/view', 'id' => $value->softwareInstalled->id]);
            $installsoft .= '<ul>';
            $installsoft .= '<li>Описание: '.$value->softwareInstalled->description.'</li>';
            $installsoft .= '<li> Установлено на: '.Html::a($equip, ['equip/view', 'id' => $value->softwareInstalled->equipment0->id]).'</li>';
            $installsoft .= '</ul>';
            $installsoft .= '<br/>';
        }

        // документация
        $documents = '';
        foreach ($model->rDocumentations as $value) {
            $documents .= $value->name.' '.$value->description.' ';
            $documents .= Html::a('', $value->url, ['class'=>'glyphicon glyphicon-download-alt']).'<br/><br/>';
        }

        // пароли
        $passwords = '';
        $passModels = $model->rPasswords;
        if ( $passModels && $accessViewPassword ) {

            $passwords = '<table>';
            foreach ($model->rPasswords as $value) {
                if($value->finale || $value->next) continue; // не выводим "Уничтоженные" пароли
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
                ', $value->id, $value->login, $value->description ?: 'не задано', $getpass);
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
                'label' => 'Ответственные за информационную систему',
                'format' => 'html',
                'value'=> $responsePersons ? $responsePersons : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связанное оборудование',
                'format' => 'raw',
                'value' => $this->render("_view/_equipments", ['model' => $model]),
            ],
            [
                'label' => 'Связанные контракты',
                'format' => 'html',
                'value'=> $contracts ? $contracts : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связанные нормативно-правовые акты',
                'format' => 'html',
                'value'=> $ldocs ? $ldocs : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Требования к дистрибутивам ПО',
                'format' => 'html',
                'value'=> $soft ? $soft : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связь с установленным ПО',
                'format' => 'html',
                'value'=> $installsoft ? $installsoft : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Документация',
                'format' => 'html',
                'value'=> $documents ? $documents : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Пароли к информационным системам',
                'format' => 'html',
                'value' => $passwords ? $passwords : '<i style="color:#c55">(не задано)</i>',
            ],
        ],
    ]) ?>
</div>
