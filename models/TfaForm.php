<?php

namespace app\models;

use Yii;
use yii\base\Model;

class TfaForm extends Model
{
    public $tfa_code;
    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['tfa_code'], 'required', 'message' => 'Por favor ingrese el código de verificación.'],
            [['tfa_code'], 'string', 'length' => 6, 'message' => 'El código debe tener 6 dígitos.'],
            [['tfa_code'], 'validateTfaCode'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'tfa_code' => 'Código de Verificación',
        ];
    }

    /**
     * Validates the TFA code.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateTfaCode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user) {
                $this->addError($attribute, 'Usuario no encontrado.');
                return;
            }

            // Verificar si el código ha expirado
            if (strtotime($user->tfa_vence) < time()) {
                $this->addError($attribute, 'El código ha expirado. Por favor solicite uno nuevo.');
                return;
            }

            // Verificar si el código coincide
            if ($user->tfa_code !== $this->tfa_code) {
                $this->addError($attribute, 'Código de verificación incorrecto.');
            }
        }
    }

    /**
     * Finds user by session data
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $userId = Yii::$app->session->get('tfa_user_id');
            if ($userId) {
                $this->_user = User::findOne(['user_id' => $userId, 'is_deleted' => 0]);
            }
        }

        return $this->_user;
    }

    /**
     * Completes the TFA verification process
     * @return bool whether the verification was successful
     */
    public function verify()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user) {
                // Limpiar el código TFA y su fecha de vencimiento
                $user->tfa_code = '000000';
                $user->tfa_vence = date('Y-m-d H:i:s');
                $user->save(false);

                // Iniciar sesión
                return Yii::$app->user->login($user, Yii::$app->session->get('tfa_rememberMe') ? 3600*24*30 : 0);
            }
        }
        return false;
    }
} 