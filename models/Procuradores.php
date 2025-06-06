<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "procuradores".
 *
 * @property int $id
 * @property string $nombre
 * @property string $resolucion
 * @property string $fecha_resolucion
 * @property string $gaceta
 * @property string $fecha_gaceta
 * @property int $activo
 * @property string $firma_base64
 */
class Procuradores extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'procuradores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'resolucion', 'fecha_resolucion', 'gaceta', 'fecha_gaceta', 'activo', 'firma_base64'], 'required'],
            [['fecha_resolucion', 'fecha_gaceta'], 'safe'],
            [['activo'], 'integer'],
            [['firma_base64'], 'string'],
            [['nombre', 'resolucion', 'gaceta'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nombre' => Yii::t('app', 'Nombre'),
            'resolucion' => Yii::t('app', 'Resolucion'),
            'fecha_resolucion' => Yii::t('app', 'Fecha Resolucion'),
            'gaceta' => Yii::t('app', 'Gaceta'),
            'fecha_gaceta' => Yii::t('app', 'Fecha Gaceta'),
            'activo' => Yii::t('app', 'Activo'),
            'firma_base64' => Yii::t('app', 'Firma Base64'),
        ];
    }

}
