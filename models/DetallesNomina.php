<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalles_nomina".
 *
 * @property int $detail_id
 * @property int $nomina_id
 * @property int $empleado_id
 * @property string $cargo
 * @property string $tipo_cargo
 * @property float $sueldo_quinc
 * @property float $prima_hijos
 * @property float $prima_prof
 * @property float $prima_antig
 * @property float $ivss
 * @property float $pie
 * @property float $faov
 * @property float $tesoreria_ss
 * @property float $caja_ahorro
 * @property float $aporte_suep
 * @property float $cesta_tickets
 * @property float $bono_vac
 * @property float $total_a
 * @property float $total_d
 * @property float $montopagar
 * @property int $mes
 * @property int $anio
 * @property int $periodo
 *
 * @property Empleados $empleado
 * @property Nominas $nomina
 */
class DetallesNomina extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detalles_nomina';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nomina_id', 'empleado_id', 'cargo', 'tipo_cargo', 'sueldo_quinc', 'prima_hijos', 'prima_prof', 'prima_antig', 'ivss', 'pie', 'faov', 'tesoreria_ss', 'caja_ahorro', 'aporte_suep', 'cesta_tickets', 'bono_vac', 'total_a', 'total_d', 'montopagar', 'mes', 'anio', 'periodo'], 'required'],
            [['nomina_id', 'empleado_id', 'mes', 'anio', 'periodo'], 'integer'],
            [['sueldo_quinc', 'prima_hijos', 'prima_prof', 'prima_antig', 'ivss', 'pie', 'faov', 'tesoreria_ss', 'caja_ahorro', 'aporte_suep', 'cesta_tickets', 'bono_vac', 'total_a', 'total_d', 'montopagar'], 'number'],
            [['cargo'], 'string', 'max' => 100],
            [['tipo_cargo'], 'string', 'max' => 255],
            [['empleado_id'], 'exist', 'skipOnError' => true, 'targetClass' => Empleados::class, 'targetAttribute' => ['empleado_id' => 'empleado_id']],
            [['nomina_id'], 'exist', 'skipOnError' => true, 'targetClass' => Nominas::class, 'targetAttribute' => ['nomina_id' => 'nomina_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'detail_id' => Yii::t('app', 'Detail ID'),
            'nomina_id' => Yii::t('app', 'Nomina ID'),
            'empleado_id' => Yii::t('app', 'Empleado ID'),
            'cargo' => Yii::t('app', 'Cargo'),
            'tipo_cargo' => Yii::t('app', 'Tipo Cargo'),
            'sueldo_quinc' => Yii::t('app', 'Sueldo Quinc'),
            'prima_hijos' => Yii::t('app', 'Prima Hijos'),
            'prima_prof' => Yii::t('app', 'Prima Prof'),
            'prima_antig' => Yii::t('app', 'Prima Antig'),
            'ivss' => Yii::t('app', 'Ivss'),
            'pie' => Yii::t('app', 'Pie'),
            'faov' => Yii::t('app', 'Faov'),
            'tesoreria_ss' => Yii::t('app', 'Tesoreria Ss'),
            'caja_ahorro' => Yii::t('app', 'Caja Ahorro'),
            'aporte_suep' => Yii::t('app', 'Aporte Suep'),
            'cesta_tickets' => Yii::t('app', 'Cesta Tickets'),
            'bono_vac' => Yii::t('app', 'Bono Vac'),
            'total_a' => Yii::t('app', 'Total A'),
            'total_d' => Yii::t('app', 'Total D'),
            'montopagar' => Yii::t('app', 'Montopagar'),
            'mes' => Yii::t('app', 'Mes'),
            'anio' => Yii::t('app', 'Anio'),
            'periodo' => Yii::t('app', 'Periodo'),
        ];
    }

    /**
     * Gets query for [[Empleado]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleado()
    {
        return $this->hasOne(Empleados::class, ['empleado_id' => 'empleado_id']);
    }

    /**
     * Gets query for [[Nomina]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNomina()
    {
        return $this->hasOne(Nominas::class, ['nomina_id' => 'nomina_id']);
    }

}
