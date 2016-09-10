<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Counterparty;

/**
 * CounterpartySearch represents the model behind the search form about `app\models\Counterparty`.
 */
class CounterpartySearch extends Counterparty
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id'], 'integer'],
            [['name'], 'safe'],
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
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = 'app\behaviors\SearchBehavior';
        return $behaviors;
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
        $query = Counterparty::find()
            ->joinWith('category category');

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
        ;

        return $dataProvider;
    }
}
