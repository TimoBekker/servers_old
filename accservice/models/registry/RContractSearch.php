<?php

namespace app\models\registry;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\registry\RContract;

/**
 * RContractSearch represents the model behind the search form about `app\models\registry\RContract`.
 */
class RContractSearch extends RContract
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cost'], 'integer'],
            [['name', 'contract_subject', 'requisite', 'date_complete', 'date_begin_warranty', 'date_end_warranty', 'url'], 'safe'],
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
        $query = RContract::find();

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
            'cost' => $this->cost,
            'date_complete' => $this->date_complete,
            'date_begin_warranty' => $this->date_begin_warranty,
            'date_end_warranty' => $this->date_end_warranty,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'contract_subject', $this->contract_subject])
            ->andFilterWhere(['like', 'requisite', $this->requisite])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
