<?php

namespace app\models;

use Yii;
use yii\base\Model;

class PasswordResetRequestForm extends Model
{
    public $email;
    public $verification_code;
    public $password;
    public $password_confirm;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required', 'message' => 'Por favor ingrese su correo electrónico'],
            ['email', 'email', 'message' => 'Por favor ingrese un correo electrónico válido'],
            ['email', 'exist',
                'targetClass' => '\app\models\Users',
                'filter' => ['is_deleted' => 0],
                'message' => 'Su usuario no ha sido encontrado en nuestra base de datos, o ha sido eliminado. Por favor, regístrese.'
            ],
            ['verification_code', 'required', 'message' => 'Por favor ingrese el código de verificación'],
            ['verification_code', 'string', 'max' => 6],
            ['password', 'required', 'message' => 'Por favor ingrese una nueva contraseña'],
            ['password', 'string', 'min' => 6, 'message' => 'La contraseña debe tener al menos 6 caracteres'],
            ['password_confirm', 'required', 'message' => 'Por favor confirme su contraseña'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'Las contraseñas no coinciden'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Correo electrónico',
            'verification_code' => 'Código de verificación',
            'password' => 'Nueva contraseña',
            'password_confirm' => 'Confirmar contraseña',
        ];
    }

    /**
     * Envía un código de verificación al correo electrónico del usuario
     *
     * @return bool si el código fue enviado exitosamente
     */
    public function sendEmail()
    {
        $user = Users::findOne([
            'email' => $this->email,
            'is_deleted' => 0
        ]);

        if (!$user) {
            return false;
        }

        // Generar un nuevo código de verificación
        $user->verification_code = Yii::$app->security->generateRandomString(6);
        if (!$user->save()) {
            return false;
        }

        return Yii::$app->mailer->compose()
            ->setTo($this->email)
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
            ->setSubject('Recuperación de contraseña - ' . Yii::$app->name)
            ->setHtmlBody("
                <h2>Recuperación de contraseña</h2>
                <p>Hola {$user->empleado->nombre},</p>
                <p>Hemos recibido una solicitud para recuperar su contraseña. Su código de verificación es:</p>
                <h1 style='text-align: center; font-size: 24px; letter-spacing: 5px;'>{$user->verification_code}</h1>
                <p>Si no solicitó este cambio, por favor ignore este mensaje.</p>
                <p>Saludos,<br>El equipo de " . Yii::$app->name . "</p>
            ")
            ->send();
    }

    /**
     * Verifica el código de verificación
     *
     * @return bool si el código es válido
     */
    public function verifyCode()
    {
        $user = Users::findOne([
            'email' => $this->email,
            'verification_code' => $this->verification_code,
            'is_deleted' => 0
        ]);

        if (!$user) {
            return false;
        }

        return true;
    }

    /**
     * Cambia la contraseña del usuario
     *
     * @return bool si la contraseña fue cambiada exitosamente
     */
    public function resetPassword()
    {
        $user = Users::findOne([
            'email' => $this->email,
            'verification_code' => $this->verification_code,
            'is_deleted' => 0
        ]);

        if (!$user) {
            return false;
        }

        $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        $user->verification_code = null;

        return $user->save();
    }
} 