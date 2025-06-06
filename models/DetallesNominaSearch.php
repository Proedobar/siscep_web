<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DetallesNomina;

/**
 * DetallesNominaSearch represents the model behind the search form of `app\models\DetallesNomina`.
 */
class DetallesNominaSearch extends DetallesNomina
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mes', 'anio'], 'required', 'message' => 'Este campo es obligatorio'],
            [['nomina_id', 'mes', 'anio'], 'integer'],
            [['cargo', 'tipo_cargo'], 'safe'],
            [['sueldo_quinc', 'prima_hijos', 'prima_prof', 'prima_antig', 'ivss', 'pie', 'faov', 'tesoreria_ss', 'caja_ahorro', 'aporte_suep', 'cesta_tickets', 'bono_vac', 'total_a', 'total_d', 'montopagar'], 'number'],
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
     * Obtiene las nóminas disponibles para el empleado actual
     * @return array
     */
    public function getNominasDisponibles()
    {
        $empleado_id = Yii::$app->user->identity->empleado_id;
        
        // Obtener los IDs de nóminas únicos para el empleado actual
        $nominasIds = DetallesNomina::find()
            ->select('nomina_id')
            ->where(['empleado_id' => $empleado_id])
            ->distinct()
            ->asArray()
            ->all();

        // Obtener los modelos de nóminas correspondientes
        $nominas = Nominas::find()
            ->where(['in', 'nomina_id', array_column($nominasIds, 'nomina_id')])
            ->all();

        // Crear el array para el dropDownList
        $nominasArray = [];
        foreach ($nominas as $nomina) {
            $nominasArray[$nomina->nomina_id] = $nomina->nomina;
        }

        return $nominasArray;
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
        $query = DetallesNomina::find();

        // Obtener el ID del empleado del usuario actual
        $empleado_id = Yii::$app->user->identity->empleado_id;

        // Configurar el DataProvider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 6, // Mostrar 6 tarjetas por página
            ],
            'sort' => [
                'defaultOrder' => [
                    'anio' => SORT_DESC,
                    'mes' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // Siempre filtrar por el empleado actual
        $query->andWhere(['empleado_id' => $empleado_id]);

        // Condiciones de filtrado
        $query->andFilterWhere([
            'nomina_id' => $this->nomina_id,
            'mes' => $this->mes,
            'anio' => $this->anio,
        ]);

        return $dataProvider;
    }
}
