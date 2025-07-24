<?php

namespace app\models\registry;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\registry\RSoftwareInstalled;

/**
 * RSoftwareInstalledSearch represents the model behind the search form about `app\models\registry\RSoftwareInstalled`.
 */
class RSoftwareInstalledSearch extends RSoftwareInstalled
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'software', 'equipment'], 'integer'],
            [['description', 'date_commission', 'bitrate'], 'safe'],
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
        $query = RSoftwareInstalled::find();

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
            'software' => $this->software,
            'equipment' => $this->equipment,
            'date_commission' => $this->date_commission,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'bitrate', $this->bitrate]);

        // принимать даты будем в формате дд.мм.гггг (настроим так датапикер)
        // если данные пришли валидуем, а потом сконвертируем в гггг-мм-дд и добавим в фильтр
        // подготовим валидатор дат
        $validator = new \yii\validators\DateValidator(['format'=>'dd.MM.yyyy']);
        $error='';
        // date_commission
        if(!empty($params['date_commission_from']) &&
                            $validator->validate($params['date_commission_from'], $error)){
            // конвертим в мускул формат
            $date_commission_from = (new \Datetime($params['date_commission_from']))
                ->format('Y-m-d H:i:s');
            $query->andFilterWhere(
                ['>', 'date_commission', $date_commission_from]
            );
        }
        if(!empty($params['date_commission_to']) &&
                            $validator->validate($params['date_commission_to'], $error)){
            // конвертим в мускул формат
            $date_commission_to = (new \Datetime($params['date_commission_to']))
                ->format('Y-m-d H:i:s');
                // var_dump($date_commission_to);exit;
            $query->andFilterWhere(
                ['<', 'date_commission', $date_commission_to]
            );
        }

        return $dataProvider;
    }
}
