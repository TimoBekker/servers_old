<?php

namespace app\models\registry;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\registry\RSoftware;

/**
 * RSoftwareSearch represents the model behind the search form about `app\models\registry\RSoftware`.
 */
class RSoftwareSearch extends RSoftware
{
    public $typeName; // для организации правильной сортировки и поиска по наименованию типа

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'type_license', 'count_license', 'owner', 'method_license', 'developer', 'count_cores', 'amount_ram', 'volume_hdd'], 'integer'],
            [['name', 'version', 'description', 'date_end_license', 'inventory_number'], 'safe'],
            // для сортировки по связанному
            [['typeName'], 'safe'],
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
        $query = RSoftware::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith(['type0']);
        $dataProvider->sort->attributes['typeName'] = [
            'asc' => ['c_type_software.name' => SORT_ASC],
            'desc' => ['c_type_software.name' => SORT_DESC],
            'default' => SORT_ASC,
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'type_license' => $this->type_license,
            'count_license' => $this->count_license,
            'date_end_license' => $this->date_end_license,
            'owner' => $this->owner,
            'method_license' => $this->method_license,
            'developer' => $this->developer,
            'count_cores' => $this->count_cores,
            'amount_ram' => $this->amount_ram,
            'volume_hdd' => $this->volume_hdd,
        ]);

        $query->andFilterWhere(['like', 'r_software.name', $this->name])
            ->andFilterWhere(['like', 'version', $this->version])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'inventory_number', $this->inventory_number])
            ->andFilterWhere(['like', 'c_type_software.name', $this->typeName]);

        // принимать даты будем в формате дд.мм.гггг (настроим так датапикер)
        // если данные пришли валидуем, а потом сконвертируем в гггг-мм-дд и добавим в фильтр
        // подготовим валидатор дат
        $validator = new \yii\validators\DateValidator(['format'=>'dd.MM.yyyy']);
        $error='';
        // date_end_license
        if(!empty($params['date_end_license_from']) &&
                            $validator->validate($params['date_end_license_from'], $error)){
            // конвертим в мускул формат
            $date_end_license_from = (new \Datetime($params['date_end_license_from']))
                ->format('Y-m-d H:i:s');
            $query->andFilterWhere(
                ['>', 'date_end_license', $date_end_license_from]
            );
        }
        if(!empty($params['date_end_license_to']) &&
                            $validator->validate($params['date_end_license_to'], $error)){
            // конвертим в мускул формат
            $date_end_license_to = (new \Datetime($params['date_end_license_to']))
                ->format('Y-m-d H:i:s');
                // var_dump($date_end_license_to);exit;
            $query->andFilterWhere(
                ['<', 'date_end_license', $date_end_license_to]
            );
        }

        return $dataProvider;
    }
}
