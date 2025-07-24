<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\relation\NnEquipmentVlan;
use app\models\User;

/* @var $this yii\web\View */
$this->title = 'Результаты поиска';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $model,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // 'name',
            'fact_alias',
            [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function($model){

                    $link_name = $model->name;

                    if(mb_strpos($model->id, 'equip_') !== false){
                        $entity = 'equip/view';
                        $link_name = 'Hostname: '.str_replace('>>>', '<br>VMware:', $link_name);
                    }
                    elseif(mb_strpos($model->id, 'infosys_') !== false)
                        $entity = 'infosys/view';
                    elseif(mb_strpos($model->id, 'soft_') !== false){
                        $entity = 'soft/view';
                        $link_name = $model->name.' '.$model->dparam1;
                    }
                    elseif(mb_strpos($model->id, 'contr_') !== false)
                        $entity = 'contr/view';
                    elseif(mb_strpos($model->id, 'legald_') !== false)
                        $entity = 'ldoc/view';
                    elseif(mb_strpos($model->id, 'claim_') !== false){
                        $entity = 'refs/view';
                        $entityRef = 'SClaim';
                    }
                    elseif(mb_strpos($model->id, 'agree_') !== false){
                        $entity = 'refs/view';
                        $entityRef = 'CAgreement';
                    }
                    elseif(mb_strpos($model->id, 'organiz_') !== false){
                        $entity = 'refs/view';
                        $entityRef = 'SOrganization';
                    }
                    else // user_
                        $entity = 'user/view';

                    return Html::a(
                        $link_name,
                        isset($entityRef) ? [$entity, 'id' => $model->fact_id, 'refid' => $entityRef] : [$entity, 'id' => $model->fact_id],
                        ['class' => 'profile-link']
                    );
                }
            ],
			[
                'attribute' => 'description',
                'value' => function($model){
                    if(mb_strlen($model->description) > 100)
                        return mb_substr($model->description, 0, 100).'...';
                    else
                        return $model->description;
                },
            ],
            [
                'attribute' => null,
                'label' => 'Информация',
                'format' => 'html',
                'value' => function($model){

                    $html = '';

                    if(mb_strpos($model->id, 'equip_') !== false){
                        $query = NnEquipmentVlan::find()
                            ->select('ip_address, mask_subnet')
                            ->where(['equipment' => $model->fact_id])
                            ->asArray()
                            /*->all()*/;
                        foreach ($query->each() as $items) {
                            $html .= long2ip($items['ip_address']).' : '.long2ip($items['mask_subnet']).'<br/>';
                            // var_dump($items);
                        }
                        $query = User::find()
                            ->select(['user.id','concat(user.last_name, " ",
                                                        substr(user.first_name,1,1), ".",
                                                        substr(user.second_name,1,1),".") as name'])
                            ->join('INNER JOIN', 's_response_equipment sre', 'sre.response_person = user.id')
                            ->join('INNER JOIN', 'r_equipment e', 'e.id = sre.equipment')
                            ->where(['e.id' => $model->fact_id])
                            ->asArray()
                            /*->all()*/;
                        foreach ($query->each() as $items) {
                            $html .= $items['name'].'<br/>';
                            // var_dump($items);
                        }
                    }
                    elseif(mb_strpos($model->id, 'infosys_') !== false){
                        $html .= $model->dparam1.'<br/>';
                        $query = User::find()
                            ->select(['user.id','concat(user.last_name, " ",
                                                        substr(user.first_name,1,1), ".",
                                                        substr(user.second_name,1,1),".") as name'])
                            ->join('INNER JOIN', 's_response_information_system sri', 'sri.response_person = user.id')
                            ->join('INNER JOIN', 'r_information_system i', 'i.id = sri.information_system')
                            ->where(['i.id' => $model->fact_id])
                            ->asArray()
                            /*->all()*/;
                        foreach ($query->each() as $items) {
                            $html .= $items['name'].'<br/>';
                            // var_dump($items);
                        }
                    }
                    elseif(mb_strpos($model->id, 'soft_') !== false){
                        $html .= "Тип: {$model->nsparam1}".'<br/>';
                        $avl = $model->nsparam2 === '0' ? 'неограничено' : $model->nsparam2 ? : 'не задано';
                        $html .= "Доступно: {$avl}".'<br/>';
                    }
                    elseif(mb_strpos($model->id, 'contr_') !== false){
                        $date_complete = (new \Datetime($model->nsparam1))
                            ->format('d.m.Y');
                        $html .= "Срок окончания работ: {$date_complete}".'<br/>';
                    }
                    elseif(mb_strpos($model->id, 'legald_') !== false){
                        $html .= "{$model->nsparam1}".'<br/>';
                        $html .= "{$model->nsparam2}".'<br/>';
                    }
                    elseif(mb_strpos($model->id, 'claim_') !== false){
                        $html .= "Соглашение: {$model->nsparam1}".'<br/>';
                    }
                    elseif(mb_strpos($model->id, 'agree_') !== false)
                        $entity = '';
                    elseif(mb_strpos($model->id, 'organiz_') !== false){
                        $html .= "Адрес: {$model->nsparam1}".'<br/>';
                        $html .= "Контактный телефон: {$model->nsparam2}".'<br/>';
                    }
                    else{ // user_
                        $html .= "Организация: {$model->nsparam1}".'<br/>';
                        $html .= "Должность: {$model->nsparam2}".'<br/>';
                    }
                    return $html;
                }
            ],
			/*[
                'attribute' => 'dparam1',
                'value' => function($model){
                    if(mb_strpos($model->id, 'equip_') !== false)
                        return CTypeEquipment::findOne((int)$model->dparam1)->name;
                    elseif(mb_strpos($model->id, 'soft_') !== false)
                        return CTypeSoftware::findOne((int)$model->dparam1)->name;
                    else
                        return $model->dparam1;
                },
            ],*/

        ],
    ]); ?>

</div>