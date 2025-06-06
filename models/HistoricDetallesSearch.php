<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\HistoricDetalles;

class HistoricDetallesSearch extends HistoricDetalles
{
    public function rules()
    {
        return [
            [['id', 'user_id', 'mes', 'anio', 'periodo', 'estado'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = HistoricDetalles::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'mes' => $this->mes,
            'anio' => $this->anio,
            'periodo' => $this->periodo,
            'estado' => $this->estado,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }
} 