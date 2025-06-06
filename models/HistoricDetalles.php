<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "historic_detalles".
 *
 * @property int $id
 * @property int $user_id
 * @property int $mes
 * @property int $anio
 * @property int $periodo
 * @property int $estado
 * @property string $created_at
 */
class HistoricDetalles extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'historic_detalles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'mes', 'anio', 'periodo', 'estado'], 'required'],
            [['user_id', 'mes', 'anio', 'periodo', 'estado'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'mes' => Yii::t('app', 'Mes'),
            'anio' => Yii::t('app', 'Anio'),
            'periodo' => Yii::t('app', 'Periodo'),
            'estado' => Yii::t('app', 'Estado'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

}
