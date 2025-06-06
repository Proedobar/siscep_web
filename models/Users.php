<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $user_id
 * @property int $empleado_id
 * @property string|null $foto_perfil
 * @property string $email
 * @property string $password_hash
 * @property int|null $state
 * @property string|null $ultima_vez
 * @property int $rol_id
 * @property string $auth_key
 * @property string $verification_code
 * @property int $is_verified
 * @property int $tfa_on
 * @property string $tfa_code
 * @property string $tfa_vence
 * @property int $is_deleted
 *
 * @property Empleados $empleado
 * @property Logs[] $logs
 * @property Roles $rol
 */
class Users extends \yii\db\ActiveRecord implements IdentityInterface
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['foto_perfil', 'ultima_vez'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['empleado_id', 'email', 'password_hash', 'rol_id', 'auth_key', 'verification_code', 'tfa_code', 'tfa_vence'], 'required'],
            [['empleado_id', 'state', 'rol_id', 'is_verified', 'tfa_on', 'is_deleted'], 'integer'],
            [['ultima_vez', 'tfa_vence'], 'safe'],
            [['foto_perfil', 'password_hash', 'auth_key'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 100],
            [['verification_code', 'tfa_code'], 'string', 'max' => 6],
            [['empleado_id'], 'exist', 'skipOnError' => true, 'targetClass' => Empleados::class, 'targetAttribute' => ['empleado_id' => 'empleado_id']],
            [['rol_id'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::class, 'targetAttribute' => ['rol_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'empleado_id' => Yii::t('app', 'Empleado ID'),
            'foto_perfil' => Yii::t('app', 'Foto Perfil'),
            'email' => Yii::t('app', 'Email'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'state' => Yii::t('app', 'State'),
            'ultima_vez' => Yii::t('app', 'Ultima Vez'),
            'rol_id' => Yii::t('app', 'Rol ID'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'verification_code' => Yii::t('app', 'Verification Code'),
            'is_verified' => Yii::t('app', 'Is Verified'),
            'tfa_on' => Yii::t('app', 'Tfa On'),
            'tfa_code' => Yii::t('app', 'Tfa Code'),
            'tfa_vence' => Yii::t('app', 'Tfa Vence'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
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
     * Gets query for [[Logs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Logs::class, ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[Rol]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRol()
    {
        return $this->hasOne(Roles::class, ['id' => 'rol_id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id, 'is_deleted' => 0]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

}
