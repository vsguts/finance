<?php

namespace app\models\search;

use app\models\components\SearchTrait;
use app\models\Counterparty;
use yii\data\ActiveDataProvider;

/**
 * CounterpartySearch represents the model behind the search form about `app\models\Counterparty`.
 */
class CounterpartySearch extends Counterparty
{
    use SearchTrait;

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Counterparty::find()
            ->joinWith('category category')
        ;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $this->getPaginationDefaults(),
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ],
            ],
        ]);

        $dataProvider->sort->attributes['category'] = [
            'asc' => ['category.name' => SORT_ASC],
            'desc' => ['category.name' => SORT_DESC],
        ];

        $params = $this->processParams($params);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $where_condition = [
            'counterparty.id' => $this->id,
        ];

        if ($this->category_id === '0') {
            $query->andWhere(['category_id' => null]);
        } else {
            $where_condition['category_id'] = $this->category_id;
        }

        // grid filtering conditions
        $query->andFilterWhere($where_condition);

        $query
            ->andFilterWhere(['like', 'counterparty.name', $this->name])
            ->andFilterWhere(['like', 'counterparty.notes', $this->notes])
        ;

        return $dataProvider;
    }
}
