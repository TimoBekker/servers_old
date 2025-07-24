<?php

namespace app\models\registry;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\registry\REvent;

/**
 * REventSearch represents the model behind the search form about `app\models\registry\REvent`.
 */
class REventSearch extends REvent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['name', 'description', 'date_begin', 'date_end'], 'safe'],
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
        $query = REvent::find();

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
            'type' => $this->type,
            'date_begin' => $this->date_begin,
            'date_end' => $this->date_end,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'description', $this->description]);



        // принимать даты будем в формате дд.мм.гггг (настроим так датапикер)
        // если данные пришли валидуем, а потом сконвертируем в гггг-мм-дд и добавим в фильтр
        // подготовим валидатор дат
        $validator = new \yii\validators\DateValidator(['format'=>'dd.MM.yyyy']);
        $error='';
        // date_begin
        if(!empty($params['date_begin_from']) &&
                            $validator->validate($params['date_begin_from'], $error)){
            // конвертим в мускул формат
            $date_begin_from = (new \Datetime($params['date_begin_from']))
                ->format('Y-m-d H:i:s');
            $query->andFilterWhere(
                ['>', 'date_begin', $date_begin_from]
            );
        }
        if(!empty($params['date_begin_to']) &&
                            $validator->validate($params['date_begin_to'], $error)){
            // конвертим в мускул формат
            $date_begin_to = (new \Datetime($params['date_begin_to']))
                ->format('Y-m-d H:i:s');
                // var_dump($date_begin_to);exit;
            $query->andFilterWhere(
                ['<', 'date_begin', $date_begin_to]
            );
        }
        // date_end
        if(!empty($params['date_end_from']) &&
                            $validator->validate($params['date_end_from'], $error)){
            // конвертим в мускул формат
            $date_end_from = (new \Datetime($params['date_end_from']))
                ->format('Y-m-d H:i:s');
            $query->andFilterWhere(
                ['>', 'date_end', $date_end_from]
            );
        }
        if(!empty($params['date_end_to']) &&
                            $validator->validate($params['date_end_to'], $error)){
            // конвертим в мускул формат
            $date_end_to = (new \Datetime($params['date_end_to']))
                ->format('Y-m-d H:i:s');
                // var_dump($date_end_to);exit;
            $query->andFilterWhere(
                ['<', 'date_end', $date_end_to]
            );
        }

        return $dataProvider;
    }
}
