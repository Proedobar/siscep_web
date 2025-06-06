<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nominas".
 *
 * @property int $nomina_id
 * @property string $nomina
 * @property string $created_at
 *
 * @property DetallesNomina[] $detallesNominas
 */
class Nominas extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const NOMINA_ALTO_NIVEL = 'ALTO_NIVEL';
    const NOMINA_EMPLEADO_FIJO = 'EMPLEADO_FIJO';
    const NOMINA_OBRERO_FIJO = 'OBRERO_FIJO';
    const NOMINA_CONTRATADOS = 'CONTRATADOS';
    const NOMINA_PENSIONADOS = 'PENSIONADOS';
    const NOMINA_JUBILADOS = 'JUBILADOS';
    const NOMINA_OBREROS_CONTRATADOS = 'OBREROS_CONTRATADOS';
    const NOMINA_ENCARGADOS = 'ENCARGADOS';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nominas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nomina'], 'required'],
            [['nomina'], 'string'],
            [['created_at'], 'safe'],
            ['nomina', 'in', 'range' => array_keys(self::optsNomina())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'nomina_id' => Yii::t('app', 'Nomina ID'),
            'nomina' => Yii::t('app', 'Nomina'),
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
        return $this->hasMany(DetallesNomina::class, ['nomina_id' => 'nomina_id']);
    }


    /**
     * column nomina ENUM value labels
     * @return string[]
     */
    public static function optsNomina()
    {
        return [
            self::NOMINA_ALTO_NIVEL => Yii::t('app', 'ALTO_NIVEL'),
            self::NOMINA_EMPLEADO_FIJO => Yii::t('app', 'EMPLEADO_FIJO'),
            self::NOMINA_OBRERO_FIJO => Yii::t('app', 'OBRERO_FIJO'),
            self::NOMINA_CONTRATADOS => Yii::t('app', 'CONTRATADOS'),
            self::NOMINA_PENSIONADOS => Yii::t('app', 'PENSIONADOS'),
            self::NOMINA_JUBILADOS => Yii::t('app', 'JUBILADOS'),
            self::NOMINA_OBREROS_CONTRATADOS => Yii::t('app', 'OBREROS_CONTRATADOS'),
            self::NOMINA_ENCARGADOS => Yii::t('app', 'ENCARGADOS'),
        ];
    }

    /**
     * @return string
     */
    public function displayNomina()
    {
        return self::optsNomina()[$this->nomina];
    }

    /**
     * @return bool
     */
    public function isNominaAltonivel()
    {
        return $this->nomina === self::NOMINA_ALTO_NIVEL;
    }

    public function setNominaToAltonivel()
    {
        $this->nomina = self::NOMINA_ALTO_NIVEL;
    }

    /**
     * @return bool
     */
    public function isNominaEmpleadofijo()
    {
        return $this->nomina === self::NOMINA_EMPLEADO_FIJO;
    }

    public function setNominaToEmpleadofijo()
    {
        $this->nomina = self::NOMINA_EMPLEADO_FIJO;
    }

    /**
     * @return bool
     */
    public function isNominaObrerofijo()
    {
        return $this->nomina === self::NOMINA_OBRERO_FIJO;
    }

    public function setNominaToObrerofijo()
    {
        $this->nomina = self::NOMINA_OBRERO_FIJO;
    }

    /**
     * @return bool
     */
    public function isNominaContratados()
    {
        return $this->nomina === self::NOMINA_CONTRATADOS;
    }

    public function setNominaToContratados()
    {
        $this->nomina = self::NOMINA_CONTRATADOS;
    }

    /**
     * @return bool
     */
    public function isNominaPensionados()
    {
        return $this->nomina === self::NOMINA_PENSIONADOS;
    }

    public function setNominaToPensionados()
    {
        $this->nomina = self::NOMINA_PENSIONADOS;
    }

    /**
     * @return bool
     */
    public function isNominaJubilados()
    {
        return $this->nomina === self::NOMINA_JUBILADOS;
    }

    public function setNominaToJubilados()
    {
        $this->nomina = self::NOMINA_JUBILADOS;
    }

    /**
     * @return bool
     */
    public function isNominaObreroscontratados()
    {
        return $this->nomina === self::NOMINA_OBREROS_CONTRATADOS;
    }

    public function setNominaToObreroscontratados()
    {
        $this->nomina = self::NOMINA_OBREROS_CONTRATADOS;
    }

    /**
     * @return bool
     */
    public function isNominaEncargados()
    {
        return $this->nomina === self::NOMINA_ENCARGADOS;
    }

    public function setNominaToEncargados()
    {
        $this->nomina = self::NOMINA_ENCARGADOS;
    }
}
