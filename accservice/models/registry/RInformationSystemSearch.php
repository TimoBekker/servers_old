<?php

namespace app\models\registry;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\registry\RInformationSystem;

/**
 * RInformationSystemSearch represents the model behind the search form about `app\models\registry\RInformationSystem`.
 */
class RInformationSystemSearch extends RInformationSystem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'validation', 'protection', 'privacy'], 'integer'],
            [['name_short', 'name_full', 'description'], 'safe'],
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
        $query = RInformationSystem::find();

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
            'validation' => $this->validation,
            'protection' => $this->protection,
            'privacy' => $this->privacy,
        ]);

        $query->andFilterWhere(['like', 'name_short', $this->name_short])
            ->andFilterWhere(['like', 'name_full', $this->name_full])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
