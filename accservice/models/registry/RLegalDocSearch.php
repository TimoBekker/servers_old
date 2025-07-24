<?php

namespace app\models\registry;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\registry\RLegalDoc;

/**
 * RLegalDocSearch represents the model behind the search form about `app\models\registry\RLegalDoc`.
 */
class RLegalDocSearch extends RLegalDoc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'status'], 'integer'],
            [['name', 'date', 'number', 'regulation_subject', 'url'], 'safe'],
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
        $query = RLegalDoc::find();

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
            'date' => $this->date,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'regulation_subject', $this->regulation_subject])
            ->andFilterWhere(['like', 'url', $this->url]);

        // принимать даты будем в формате дд.мм.гггг (настроим так датапикер)
        // если данные пришли валидуем, а потом сконвертируем в гггг-мм-дд и добавим в фильтр
        // подготовим валидатор дат
        $validator = new \yii\validators\DateValidator(['format'=>'dd.MM.yyyy']);
        $error='';
        // date
        if(!empty($params['date_from']) &&
                            $validator->validate($params['date_from'], $error)){
            // конвертим в мускул формат
            $date_from = (new \Datetime($params['date_from']))
                ->format('Y-m-d H:i:s');
            $query->andFilterWhere(
                ['>', 'date', $date_from]
            );
        }
        if(!empty($params['date_to']) &&
                            $validator->validate($params['date_to'], $error)){
            // конвертим в мускул формат
            $date_to = (new \Datetime($params['date_to']))
                ->format('Y-m-d H:i:s');
                // var_dump($date_to);exit;
            $query->andFilterWhere(
                ['<', 'date', $date_to]
            );
        }

        return $dataProvider;
    }
}
