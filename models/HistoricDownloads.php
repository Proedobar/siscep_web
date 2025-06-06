<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "historic_downloads".
 *
 * @property int $id
 * @property string $file_generado
 * @property string $checksum
 * @property string $fecha_hora
 * @property int $user_id
 */
class HistoricDownloads extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'historic_downloads';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file_generado', 'checksum', 'user_id'], 'required'],
            [['fecha_hora'], 'safe'],
            [['user_id'], 'integer'],
            [['file_generado'], 'string', 'max' => 255],
            [['checksum'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'file_generado' => Yii::t('app', 'File Generado'),
            'checksum' => Yii::t('app', 'Checksum'),
            'fecha_hora' => Yii::t('app', 'Fecha Hora'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

}
