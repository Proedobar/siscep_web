<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Nominas;

/**
 * NominasSearch represents the model behind the search form of `app\models\Nominas`.
 */
class NominasSearch extends Nominas
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nomina_id'], 'integer'],
            [['nomina', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Nominas::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'nomina_id' => $this->nomina_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'nomina', $this->nomina]);

        return $dataProvider;
    }
}
