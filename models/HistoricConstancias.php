<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "historic_constancias".
 *
 * @property int $id
 * @property int $user_id
 * @property int $mes
 * @property int $anio
 * @property string $created_at
 */
class HistoricConstancias extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'historic_constancias';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'mes', 'anio'], 'required'],
            [['user_id', 'mes', 'anio'], 'integer'],
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
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

}
