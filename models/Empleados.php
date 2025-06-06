<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "empleados".
 *
 * @property int $empleado_id
 * @property string $ci
 * @property string $nombre
 * @property string $fecha_ingreso
 * @property string $created_at
 *
 * @property DetallesNomina[] $detallesNominas
 * @property Users[] $users
 */
class Empleados extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'empleados';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ci', 'nombre', 'fecha_ingreso'], 'required'],
            [['fecha_ingreso', 'created_at'], 'safe'],
            [['ci'], 'string', 'max' => 20],
            [['nombre'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'empleado_id' => Yii::t('app', 'Empleado ID'),
            'ci' => Yii::t('app', 'Ci'),
            'nombre' => Yii::t('app', 'Nombre'),
            'fecha_ingreso' => Yii::t('app', 'Fecha Ingreso'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[DetallesNominas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetallesNominas()
    {
        return $this->hasMany(DetallesNomina::class, ['empleado_id' => 'empleado_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::class, ['empleado_id' => 'empleado_id']);
    }

}
