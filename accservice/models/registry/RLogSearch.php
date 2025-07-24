<?php

namespace app\models\registry;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\registry\RLog;

/**
 * RLogSearch represents the model behind the search form about `app\models\registry\RLog`.
 */
class RLogSearch extends RLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'code'], 'integer'],
            [['grade', 'date_emergence', 'content', 'user'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // var_dump($params);exit;
        $query = RLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'code' => $this->code,
            //'date_emergence' => $this->date_emergence,
            // 'user' => $this->user,
        ]);


        // принимать даты будем в формате дд.мм.гггг (настроим так датапикер)
        // если данные пришли валидуем, а потом сконвертируем в гггг-мм-дд и добавим в фильтр

        // подготовим валидатор дат
        $validator = new \yii\validators\DateValidator(['format'=>'dd.MM.yyyy']);
        // var_dump($validator);exit;
        $error='';
        if(!empty($params['date_emergence_from']) &&
                            $validator->validate($params['date_emergence_from'], $error)){
            // конвертим в мускул формат
            $date_emergence_from = (new \Datetime($params['date_emergence_from']))
                ->format('Y-m-d H:i:s');
            $query->andFilterWhere(
                ['>', 'date_emergence', $date_emergence_from]
            );
        }
/*        else{
            // var_dump($error);exit;
            return $dataProvider;
        }*/

        if(!empty($params['date_emergence_to']) &&
                            $validator->validate($params['date_emergence_to'], $error)){
            // конвертим в мускул формат
            $date_emergence_to = (new \Datetime($params['date_emergence_to']))
                ->format('Y-m-d H:i:s');
                // var_dump($date_emergence_to);exit;
            $query->andFilterWhere(
                ['<', 'date_emergence', $date_emergence_to]
            );
        }
/*        else{
            return $dataProvider;
        }*/
        // todo: хорошо бы еще сделать вывод ошибки после валидации в фильтре, как и везде
        // но для этого, я думаю нада проще подругомы было бы все реализовать:
        // добавить в модель 2 свойства date_emergence_to и date_emergence_from
        // тогда бы все попало в валидацию модели. Только надо еще было изментиь name полей
        // по правилам модели. И добавить ниже дивы для вывода ошибок.
        // Но от ввода ош. данных мы защитились и так. А их ввод просто не добавится в фильтр.
        // Поэтому мы не делаем else return $dataProvider. Если его делать, то проверка на
        // empty в условии не пойдет, поскольку если будет приходить пустая строка после уда
        // ления вручную значения в фильтре, то будет отрабатывать else и до второго условия
        // не дойдет. Вобщем при этом надо будет чуть сменить логигу.


        $query->andFilterWhere(['like', 'grade', $this->grade])
            ->andFilterWhere(['like', 'user', $this->user])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
