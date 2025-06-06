<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "directores".
 *
 * @property int $id
 * @property string $nombre_director
 * @property string $resolucion
 * @property string $fecha_resolucion
 * @property string $gaceta
 * @property string $fecha_gaceta
 * @property int $activo
 * @property string|null $firma_base64
 * @property int|null $activo_unique
 */
class Directores extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'directores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firma_base64', 'activo_unique'], 'default', 'value' => null],
            [['activo'], 'default', 'value' => 1],
            [['nombre_director', 'resolucion', 'fecha_resolucion', 'gaceta', 'fecha_gaceta'], 'required'],
            [['fecha_resolucion', 'fecha_gaceta'], 'safe'],
            [['activo', 'activo_unique'], 'integer'],
            [['firma_base64'], 'string'],
            [['nombre_director', 'resolucion', 'gaceta'], 'string', 'max' => 255],
            [['activo_unique'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nombre_director' => Yii::t('app', 'Nombre Director'),
            'resolucion' => Yii::t('app', 'Resolucion'),
            'fecha_resolucion' => Yii::t('app', 'Fecha Resolucion'),
            'gaceta' => Yii::t('app', 'Gaceta'),
            'fecha_gaceta' => Yii::t('app', 'Fecha Gaceta'),
            'activo' => Yii::t('app', 'Activo'),
            'firma_base64' => Yii::t('app', 'Firma Base64'),
            'activo_unique' => Yii::t('app', 'Activo Unique'),
        ];
    }

    /**
     * Convierte fechas del formato dd-MM-yyyy (formato español del datepicker) a yyyy-MM-dd (formato para la base de datos)
     * antes de guardar en la base de datos
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Convertir las fechas del formato dd-MM-yyyy a yyyy-MM-dd
            if (!empty($this->fecha_resolucion)) {
                $date = \DateTime::createFromFormat('d-m-Y', $this->fecha_resolucion);
                if ($date) {
                    $this->fecha_resolucion = $date->format('Y-m-d');
                }
            }
            
            if (!empty($this->fecha_gaceta)) {
                $date = \DateTime::createFromFormat('d-m-Y', $this->fecha_gaceta);
                if ($date) {
                    $this->fecha_gaceta = $date->format('Y-m-d');
                }
            }
            
            return true;
        }
        return false;
    }
    
    /**
     * Convierte las fechas del formato yyyy-MM-dd (formato de base de datos) a dd-MM-yyyy (formato español)
     * después de cargar desde la base de datos
     */
    public function afterFind()
    {
        parent::afterFind();
        
        // Convertir las fechas de la base de datos al formato español para el datepicker
        if (!empty($this->fecha_resolucion)) {
            $date = \DateTime::createFromFormat('Y-m-d', $this->fecha_resolucion);
            if ($date) {
                $this->fecha_resolucion = $date->format('d-m-Y');
            }
        }
        
        if (!empty($this->fecha_gaceta)) {
            $date = \DateTime::createFromFormat('Y-m-d', $this->fecha_gaceta);
            if ($date) {
                $this->fecha_gaceta = $date->format('d-m-Y');
            }
        }
    }
}
