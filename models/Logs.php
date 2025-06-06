<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property int $id
 * @property string $ip
 * @property string|null $ubicacion
 * @property string $fecha_hora
 * @property int|null $user_id
 * @property string $action
 * @property int $status_released
 * @property string $computer
 * @property string $osver
 * @property string $useragent
 *
 * @property Users $user
 */
class Logs extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ubicacion', 'user_id'], 'default', 'value' => null],
            [['ip', 'fecha_hora', 'action', 'status_released', 'computer', 'osver', 'useragent'], 'required'],
            [['fecha_hora'], 'safe'],
            [['user_id', 'status_released'], 'integer'],
            [['ip'], 'string', 'max' => 45],
            [['ubicacion'], 'string', 'max' => 100],
            [['action', 'computer', 'osver'], 'string', 'max' => 80],
            [['useragent'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'user_id']],
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
            'ubicacion' => Yii::t('app', 'Ubicacion'),
            'fecha_hora' => Yii::t('app', 'Fecha Hora'),
            'user_id' => Yii::t('app', 'User ID'),
            'action' => Yii::t('app', 'Action'),
            'status_released' => Yii::t('app', 'Status Released'),
            'computer' => Yii::t('app', 'Computer'),
            'osver' => Yii::t('app', 'Osver'),
            'useragent' => Yii::t('app', 'Useragent'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'user_id']);
    }

}
