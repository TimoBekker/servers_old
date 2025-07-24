<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * This is the model class for table "view_search".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $dparam1
 */
class ViewSearch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_search';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'dparam1'], 'required'],
            [['description', 'dparam1', 'fact_alias'], 'string'],
            [['id'], 'string', 'max' => 18],
            [['name'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fact_id' => 'Fact_id',
            'fact_alias' => 'Тип сущности',
            'name' => 'Наименование',
            'description' => 'Описание',
            'dparam1' => 'Дополнительный параметр 1',
            'dparam2' => 'Дополнительный параметр 2',
            'dparam3' => 'Дополнительный параметр 3',
            'dparam4' => 'Дополнительный параметр 4',
            'nsparam1' => 'Непоисковый параметр 1',
            'nsparam2' => 'Непоисковый параметр 2',
        ];
    }

    /**
     * Функция поиска по Представлению view_search
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // пример:
        // $query = ViewSearch::find()->where(['fact_id' => 1, 'fact_alias'=>'Оборудование']);
        $query = ViewSearch::find()
            ->select(new Expression("
                id, 
                ANY_VALUE(fact_id) fact_id, 
                ANY_VALUE(name) name, 
                ANY_VALUE(fact_alias) fact_alias,
                ANY_VALUE(description) description,
                ANY_VALUE(dparam1) dparam1,
                ANY_VALUE(dparam2) dparam2,
                ANY_VALUE(dparam3) dparam3,
                ANY_VALUE(dparam4) dparam4,
                ANY_VALUE(nsparam1) nsparam1,
                ANY_VALUE(nsparam2) nsparam2               
            "))
            ->where(['like', 'name', $params])
            ->orWhere(['like', 'fact_alias', $params])
            ->orWhere(['like', 'description', $params])
            ->orWhere(['like', 'dparam1', $params])
            ->orWhere(['like', 'dparam2', $params])
            ->orWhere(['like', 'dparam3', $params])
            ->orWhere(['like', 'dparam4', $params])
            ->groupby('id'); // замена дистинкт по id
        /*
            todo: Для поиска по названиям типов обор-я, ПО и др. аналогичным, где у нас име-
            ются только айдишники отношений, нам будет проще переделать представление в базе,
            чтобы сразу в нем были текстовые данные этих полей. В таком случае надо не забыть
            переделать вывод этих полей в searchresult.php
        */

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        // $this->load($params);

        // if (!$this->validate()) {
        //     // uncomment the following line if you do not want to any records when validation fails
        //     // $query->where('0=1');
        //     return $dataProvider;
        // }

        // $query->andFilterWhere([
        //     'id' => $this->id,
        //     'status' => $this->status,
        //     'created_at' => $this->created_at,
        //     'updated_at' => $this->updated_at,
        //     'organization' => $this->organization,
        //     'leader' => $this->leader,
        // ]);

        // $query->andFilterWhere(['like', 'username', $this->username])
        //     ->andFilterWhere(['like', 'auth_key', $this->auth_key])
        //     ->andFilterWhere(['like', 'password_hash', $this->password_hash])
        //     ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
        //     ->andFilterWhere(['like', 'email', $this->email])
        //     ->andFilterWhere(['like', 'first_name', $this->first_name])
        //     ->andFilterWhere(['like', 'last_name', $this->last_name])
        //     ->andFilterWhere(['like', 'second_name', $this->second_name])
        //     ->andFilterWhere(['like', 'position', $this->position])
        //     ->andFilterWhere(['like', 'phone_work', $this->phone_work])
        //     ->andFilterWhere(['like', 'phone_cell', $this->phone_cell])
        //     ->andFilterWhere(['like', 'skype', $this->skype])
        //     ->andFilterWhere(['like', 'additional', $this->additional]);

        return $dataProvider;
    }
}
