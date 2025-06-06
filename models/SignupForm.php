<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $ci;
    public $email;
    public $password;
    public $password_confirm;
    public $verification_code;
    
    private $_empleado;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['ci', 'required', 'message' => 'Por favor ingrese su número de documento de identidad'],
            ['ci', 'string', 'max' => 20],
            ['ci', 'validateCi'],
            
            ['email', 'required', 'message' => 'Por favor ingrese su correo electrónico'],
            ['email', 'email', 'message' => 'Ingrese un correo electrónico válido'],
            ['email', 'string', 'max' => 100],
            ['email', 'unique', 'targetClass' => Users::class, 'targetAttribute' => 'email', 'message' => 'Este correo ya está registrado'],
            
            ['password', 'required', 'message' => 'Por favor ingrese una contraseña'],
            ['password', 'string', 'min' => 6, 'message' => 'La contraseña debe tener al menos 6 caracteres'],
            
            ['password_confirm', 'required', 'message' => 'Por favor confirme su contraseña'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'Las contraseñas no coinciden'],
            
            ['verification_code', 'required', 'message' => 'Por favor ingrese el código de verificación'],
            ['verification_code', 'string', 'max' => 6],
            ['verification_code', 'validateVerificationCode'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ci' => 'Número de Documento de Identidad',
            'email' => 'Correo Electrónico',
            'password' => 'Contraseña',
            'password_confirm' => 'Confirmar Contraseña',
            'verification_code' => 'Código de Verificación',
        ];
    }

    /**
     * Validates the CI.
     * This method validates if the CI exists in the employees table.
     *
     * @param string $attribute the attribute currently being validated
     */
    public function validateCi($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $empleado = Empleados::findOne(['ci' => $this->$attribute]);
            
            if (!$empleado) {
                $this->addError($attribute, 'El número de documento proporcionado no está registrado en nuestro sistema.');
            } else {
                // Verificar si ya existe un usuario con este empleado_id
                $user = Users::findOne(['empleado_id' => $empleado->empleado_id]);
                if ($user && $user->is_deleted != 1) {
                    $this->addError($attribute, 'Ya existe una cuenta asociada a este documento de identidad.');
                }
                
                $this->_empleado = $empleado;
            }
        }
    }

    /**
     * Validates the verification code.
     *
     * @param string $attribute the attribute currently being validated
     */
    public function validateVerificationCode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $code = Yii::$app->session->get('verification_code');
            $email = Yii::$app->session->get('verification_email');
            
            if ($code !== $this->$attribute || $email !== $this->email) {
                $this->addError($attribute, 'El código de verificación es incorrecto o ha expirado.');
            }
        }
    }

    /**
     * Generate and send verification code.
     *
     * @return boolean whether the code was generated and sent successfully
     */
    public function sendVerificationCode()
    {
        // Generar código aleatorio de 6 dígitos
        $code = sprintf("%06d", mt_rand(1, 999999));
        
        // Guardar en sesión (en producción, considerar almacenar en base de datos con tiempo de expiración)
        Yii::$app->session->set('verification_code', $code);
        Yii::$app->session->set('verification_email', $this->email);
        
        // Enviar correo con el código
        $sent = Yii::$app->mailer->compose()
            ->setTo($this->email)
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
            ->setSubject('Código de Verificación - ' . Yii::$app->name)
            ->setTextBody('Su código de verificación es: ' . $code)
            ->setHtmlBody('<p>Su código de verificación es: <strong>' . $code . '</strong></p>')
            ->send();
            
        return $sent;
    }

    /**
     * Verify if CI exists in database.
     *
     * @return boolean whether the CI exists
     */
    public function verifyCi()
    {
        $empleado = Empleados::findOne(['ci' => $this->ci]);
        return $empleado !== null;
    }

    /**
     * Signs user up.
     *
     * @return Users|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        if (!$this->_empleado) {
            $this->_empleado = Empleados::findOne(['ci' => $this->ci]);
            if (!$this->_empleado) {
                return null;
            }
        }
        
        // Comprobar si existe un usuario eliminado con este empleado_id
        $existingUser = Users::findOne(['empleado_id' => $this->_empleado->empleado_id]);
        
        if ($existingUser && $existingUser->is_deleted == 1) {
            // Restaurar usuario eliminado
            $existingUser->email = $this->email;
            $existingUser->password_hash = Yii::$app->security->generatePasswordHash($this->password);
            $existingUser->state = 1; // Activo
            $existingUser->is_deleted = 0; // Restaurar usuario
            $existingUser->auth_key = Yii::$app->security->generateRandomString();
            $existingUser->verification_code = Yii::$app->session->get('verification_code');
            $existingUser->is_verified = 0; // Requerir verificación nuevamente
            
            if ($existingUser->save()) {
                // Si el código proporcionado coincide con el guardado, marcar como verificado
                if ($this->verification_code === $existingUser->verification_code) {
                    $existingUser->is_verified = 1;
                    $existingUser->save(false);
                }
                return $existingUser;
            }
            
            return null;
        }
        
        // Obtener el código de verificación enviado
        $code = Yii::$app->session->get('verification_code');
        
        $user = new Users();
        $user->empleado_id = $this->_empleado->empleado_id;
        $user->email = $this->email;
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        $user->state = 1; // Activo
        $user->rol_id = 2; // Rol por defecto (ajustar según corresponda)
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->verification_code = $code;
        $user->is_verified = 0; // Inicialmente no verificado
        $user->tfa_on = 0; // Por defecto desactivado
        $user->tfa_code = '000000'; // Código inicial inactivo
        $user->tfa_vence = date('Y-m-d H:i:s'); // Fecha actual (ya vencido)
        $user->is_deleted = 0; // No eliminado
        
        if ($user->save()) {
            // Si el código proporcionado coincide con el guardado, marcar como verificado
            if ($this->verification_code === $code) {
                $user->is_verified = 1;
                $user->save(false); // Guardamos sin validación porque ya fue validado
            }
            return $user;
        }
        
        return null;
    }
} 