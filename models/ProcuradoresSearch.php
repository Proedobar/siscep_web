<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Procuradores;

/**
 * ProcuradoresSearch represents the model behind the search form of `app\models\Procuradores`.
 */
class ProcuradoresSearch extends Procuradores
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'activo'], 'integer'],
            [['nombre', 'resolucion', 'fecha_resolucion', 'gaceta', 'fecha_gaceta', 'firma_base64'], 'safe'],
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
        $query = Procuradores::find();

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
            'id' => $this->id,
            'fecha_resolucion' => $this->fecha_resolucion,
            'fecha_gaceta' => $this->fecha_gaceta,
            'activo' => $this->activo,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'resolucion', $this->resolucion])
            ->andFilterWhere(['like', 'gaceta', $this->gaceta])
            ->andFilterWhere(['like', 'firma_base64', $this->firma_base64]);

        return $dataProvider;
    }
}
