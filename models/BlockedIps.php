<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "blocked_ips".
 *
 * @property int $id
 * @property string $ip
 * @property string $fecha_hora_bloqueo
 */
class BlockedIps extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blocked_ips';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip', 'fecha_hora_bloqueo'], 'required'],
            [['fecha_hora_bloqueo'], 'safe'],
            [['ip'], 'string', 'max' => 45],
            [['ip'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ip' => Yii::t('app', 'Ip'),
            'fecha_hora_bloqueo' => Yii::t('app', 'Fecha Hora Bloqueo'),
        ];
    }

}
