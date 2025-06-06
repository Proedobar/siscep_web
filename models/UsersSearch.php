<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;

/**
 * UsersSearch represents the model behind the search form of `app\models\Users`.
 */
class UsersSearch extends Users
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'empleado_id', 'state', 'rol_id', 'is_verified', 'tfa_on', 'is_deleted'], 'integer'],
            [['foto_perfil', 'email', 'password_hash', 'ultima_vez', 'auth_key', 'verification_code', 'tfa_code', 'tfa_vence'], 'safe'],
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
        $query = Users::find();

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
            'user_id' => $this->user_id,
            'empleado_id' => $this->empleado_id,
            'state' => $this->state,
            'ultima_vez' => $this->ultima_vez,
            'rol_id' => $this->rol_id,
            'is_verified' => $this->is_verified,
            'tfa_on' => $this->tfa_on,
            'tfa_vence' => $this->tfa_vence,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'foto_perfil', $this->foto_perfil])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'verification_code', $this->verification_code])
            ->andFilterWhere(['like', 'tfa_code', $this->tfa_code]);

        return $dataProvider;
    }
}
