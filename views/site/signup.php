<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\assets\AppAsset;

AppAsset::register($this);

$this->title = 'Registro de Usuario';
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .signup-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .signup-card {
            width: 100%;
            max-width: 905px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
            height: auto;
            min-height: 520px;
        }
        .signup-card .row {
            height: 100%;
        }
        .signup-card .col-md-5,
        .signup-card .col-md-7 {
            height: auto;
            min-height: 300px;
        }
        .logo-side {
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            height: 100%;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 1rem;
            padding: 1rem;
        }
        .logo-container img {
            max-width: 150px;
            height: auto;
        }
        .form-side {
            padding: 2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            overflow-y: auto;
            max-height: none;
        }
        .signup-title {
            margin-bottom: 1.5rem;
        }
        .progress-container {
            padding: 1.5rem 0;
            margin-bottom: 1rem;
        }
        .step-points {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 0 10px 15px;
            height: 30px;
            align-items: center;
        }
        .progress-bar {
            position: absolute;
            height: 6px;
            background-color: #e9ecef;
            left: 0;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1;
            border-radius: 3px;
        }
        .progress-filled {
            background-color: #0d6efd;
            height: 100%;
            width: 0%;
            border-radius: 3px;
            transition: all 0.4s ease;
        }
        .step-point {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #e9ecef;
            border: 2px solid #fff;
            z-index: 2;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }
        .step-point::before {
            content: attr(data-step);
            font-size: 12px;
            font-weight: bold;
            color: #6c757d;
        }
        .step-point.active {
            background-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
        }
        .step-point.active::before {
            color: white;
        }
        .step-point.completed {
            background-color: #198754;
        }
        .step-point.completed::before {
            color: white;
        }
        .step-labels {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 0 10px;
        }
        .step-label {
            font-size: 0.875rem;
            color: #6c757d;
            position: relative;
            text-align: center;
            width: 33.33%;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .step-label.active {
            color: #0d6efd;
            font-weight: bold;
        }
        .step-label.completed {
            color: #198754;
        }
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }

        /* Estilos responsivos para móviles */
        @media (max-width: 767.98px) {
            .signup-card {
                height: auto;
                max-height: none;
            }
            .signup-card .row {
                flex-direction: column;
            }
            .logo-side {
                padding: 1.5rem;
                min-height: 200px;
            }
            .form-side {
                padding: 1.5rem;
                max-height: none;
                overflow-y: visible;
            }
            .step-label {
                font-size: 0.75rem;
            }
            .progress-container {
                padding: 1rem 0;
            }
            .alert {
                margin-bottom: 1rem;
                padding: 0.75rem;
            }
            .form-group {
                margin-bottom: 1rem;
            }
            .btn {
                padding: 0.5rem 1rem;
            }
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="signup-container">
    <div class="card signup-card">
        <div class="row g-0">
            <!-- Lado izquierdo - Logo -->
            <div class="col-md-5">
                <div class="logo-side">
                    <div class="logo-container">
                        <img src="<?= Yii::getAlias('@web/logo.png') ?>" alt="Logo" class="img-fluid">
                    </div>
                </div>
            </div>
            <!-- Lado derecho - Formulario -->
            <div class="col-md-7">
                <div class="form-side">
                    <div class="mb-0">
                        <h2 class="signup-title mb-0"><?= Html::encode($this->title) ?></h2>
                        <small class="text-muted">Complete los siguientes pasos para registrarse:</small>
                    </div>

                    <div class="progress-container">
                        <div class="step-points">
                            <div class="step-point active" data-step="1"></div>
                            <div class="step-point" data-step="2"></div>
                            <div class="step-point" data-step="3"></div>
                            <div class="progress-bar">
                                <div class="progress-filled" id="progress-bar-filled"></div>
                            </div>
                        </div>
                        <div class="step-labels">
                            <div class="step-label active" data-step="1">Verificación</div>
                            <div class="step-label" data-step="2">Datos</div>
                            <div class="step-label" data-step="3">Confirmación</div>
                        </div>
                    </div>

                    <?php $form = ActiveForm::begin([
                        'id' => 'signup-form',
                        'options' => ['class' => 'needs-validation'],
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'labelOptions' => ['class' => 'form-label'],
                            'inputOptions' => ['class' => 'form-control'],
                            'errorOptions' => ['class' => 'invalid-feedback'],
                        ],
                    ]); ?>

                    <!-- Paso 1: Verificación de CI -->
                    <div class="form-step active" id="step1">
                        <div class="alert alert-info">
                            Ingrese su número de documento de identidad para verificar que está registrado como empleado.
                        </div>

                        <?= $form->field($model, 'ci')->textInput(['autofocus' => true]) ?>

                        <div class="d-grid gap-2 mt-4">
                            <button type="button" class="btn btn-primary next-step" data-current="1" data-next="2">Verificar</button>
                            <a href="<?= \yii\helpers\Url::to(['site/login']) ?>" class="btn btn-outline-secondary">Volver a Login</a>
                        </div>
                    </div>

                    <!-- Paso 2: Datos de la cuenta -->
                    <div class="form-step" id="step2">
                        <div class="alert alert-success mb-3" id="welcome-message">
                            ¡Verificación exitosa! Complete sus datos para crear su cuenta.
                        </div>

                        <?= $form->field($model, 'email')->input('email') ?>

                        <div class="form-group">
                            <?= $form->field($model, 'password', [
                                'inputTemplate' => '<div class="input-group">{input}<button type="button" id="togglePassword" class="btn btn-outline-secondary"><i class="bi bi-eye-slash"></i></button></div>'
                            ])->passwordInput(['id' => 'password-field']) ?>
                        </div>

                        <?= $form->field($model, 'password_confirm', [
                            'inputTemplate' => '<div class="input-group">{input}<button type="button" id="togglePasswordConfirm" class="btn btn-outline-secondary"><i class="bi bi-eye-slash"></i></button></div>'
                        ])->passwordInput(['id' => 'password-confirm-field']) ?>

                        <div class="d-grid gap-2 mt-4">
                            <button type="button" class="btn btn-primary next-step" data-current="2" data-next="3">Continuar</button>
                            <button type="button" class="btn btn-outline-secondary prev-step" data-current="2" data-prev="1">Atrás</button>
                        </div>
                    </div>

                    <!-- Paso 3: Verificación de código -->
                    <div class="form-step" id="step3">
                        <div class="alert alert-info mb-3">
                            Hemos registrado su cuenta y enviado un código de verificación a su correo electrónico. Ingréselo a continuación:
                        </div>

                        <?= $form->field($model, 'verification_code')->textInput(['maxlength' => 6, 'placeholder' => '', 'id' => 'verification-code-input']) ?>

                        <div class="d-grid gap-2 mt-4">
                            <button type="button" id="verify-button" class="btn btn-success">Completar Registro</button>
                            <button type="button" class="btn btn-outline-secondary prev-step" data-current="3" data-prev="2">Atrás</button>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejo de mostrar/ocultar contraseña
    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('#password-field');
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    }

    const togglePasswordConfirm = document.querySelector('#togglePasswordConfirm');
    const passwordConfirmField = document.querySelector('#password-confirm-field');
    if (togglePasswordConfirm && passwordConfirmField) {
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirmField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmField.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    }

    // Función para actualizar el estado de los indicadores
    function updateStepIndicators(currentStep) {
        const progressBar = document.getElementById('progress-bar-filled');
        const stepPoints = document.querySelectorAll('.step-point');
        const stepLabels = document.querySelectorAll('.step-label');
        
        // Actualizar la barra de progreso
        if (currentStep === 1) {
            progressBar.style.width = '0%';
        } else if (currentStep === 2) {
            progressBar.style.width = '50%';
        } else if (currentStep === 3) {
            progressBar.style.width = '100%';
        }
        
        // Actualizar los puntos y las etiquetas
        stepPoints.forEach((point, index) => {
            const step = index + 1;
            point.classList.remove('active', 'completed');
            stepLabels[index].classList.remove('active', 'completed');
            
            if (step < currentStep) {
                point.classList.add('completed');
                stepLabels[index].classList.add('completed');
            } else if (step === currentStep) {
                point.classList.add('active');
                stepLabels[index].classList.add('active');
            }
        });
    }

    // Navegación entre pasos
    const nextButtons = document.querySelectorAll('.next-step');
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            // En el primer paso, verificar CI antes de continuar
            if (this.dataset.current === '1') {
                const ciInput = document.getElementById('signupform-ci');
                if (ciInput && ciInput.value.trim() !== '') {
                    // Verificar CI - la navegación se maneja en la función verifyCI
                    verifyCI(ciInput.value);
                } else {
                    // Mostrar error si el CI está vacío
                    alert('Por favor ingrese su número de documento de identidad');
                }
                return; // No continuar con la navegación automática
            }
            
            // En el segundo paso, validar campos antes de continuar
            if (this.dataset.current === '2') {
                const ciInput = document.getElementById('signupform-ci');
                const emailInput = document.getElementById('signupform-email');
                const passwordInput = document.getElementById('password-field');
                const passwordConfirmInput = document.getElementById('password-confirm-field');
                
                if (!emailInput.value.trim()) {
                    alert('Por favor ingrese su correo electrónico');
                    return;
                }
                
                if (!passwordInput.value.trim()) {
                    alert('Por favor ingrese una contraseña');
                    return;
                }
                
                if (passwordInput.value.length < 6) {
                    alert('La contraseña debe tener al menos 6 caracteres');
                    return;
                }
                
                if (passwordInput.value !== passwordConfirmInput.value) {
                    alert('Las contraseñas no coinciden');
                    return;
                }
                
                // Registrar usuario y enviar código de verificación
                registerUser(ciInput.value, emailInput.value, passwordInput.value);
                return; // No continuar con la navegación automática
            }
        });
    });

    const prevButtons = document.querySelectorAll('.prev-step');
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentStep = document.getElementById('step' + this.dataset.current);
            const prevStep = document.getElementById('step' + this.dataset.prev);
            
            // Actualizar indicadores de pasos
            updateStepIndicators(parseInt(this.dataset.prev));
            
            // Cambiar al paso anterior
            currentStep.classList.remove('active');
            prevStep.classList.add('active');
        });
    });

    // Función para verificar CI mediante AJAX
    function verifyCI(ci) {
        // Mostrar un indicador de carga
        document.querySelector(`[data-current="1"]`).disabled = true;
        document.querySelector(`[data-current="1"]`).textContent = 'Verificando...';
        
        fetch('<?= \yii\helpers\Url::to(['site/verify-ci']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ ci: ci })
        })
        .then(response => response.json())
        .then(data => {
            document.querySelector(`[data-current="1"]`).disabled = false;
            document.querySelector(`[data-current="1"]`).textContent = 'Verificar';
            
            if (!data.success) {
                alert(data.message || 'El número de documento proporcionado no está registrado en nuestro sistema.');
                return;
            }
            
            // Actualizar el mensaje de bienvenida con el nombre del empleado
            if (data.nombre) {
                let welcomeMessage = `¡Bienvenido ${data.nombre}!`;
                
                // Si es una cuenta eliminada que se va a restaurar
                if (data.is_deleted) {
                    welcomeMessage = `¡Bienvenido de nuevo ${data.nombre}! Detectamos que tenías una cuenta previa eliminada. Al completar este proceso, tu cuenta será restaurada.`;
                }
                
                document.getElementById('welcome-message').innerHTML = welcomeMessage;
            }
            
            // Si la verificación fue exitosa, avanzar al siguiente paso
            const currentStep = document.getElementById('step1');
            const nextStep = document.getElementById('step2');
            
            // Actualizar indicadores de pasos
            updateStepIndicators(2);
            
            // Cambiar al siguiente paso
            currentStep.classList.remove('active');
            nextStep.classList.add('active');
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector(`[data-current="1"]`).disabled = false;
            document.querySelector(`[data-current="1"]`).textContent = 'Verificar';
            alert('Hubo un error al verificar el documento. Inténtelo de nuevo.');
        });
    }

    // Función para registrar usuario y enviar código de verificación
    function registerUser(ci, email, password) {
        // Mostrar un indicador de carga
        document.querySelector(`[data-current="2"]`).disabled = true;
        document.querySelector(`[data-current="2"]`).textContent = 'Registrando...';
        
        fetch('<?= \yii\helpers\Url::to(['site/register-user']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                ci: ci,
                email: email,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            document.querySelector(`[data-current="2"]`).disabled = false;
            document.querySelector(`[data-current="2"]`).textContent = 'Continuar';
            
            if (!data.success) {
                alert(data.message || 'Hubo un error al registrar al usuario');
                return;
            }
            
            // Si hay una advertencia, mostrarla
            if (data.warning) {
                alert(data.message);
            }
            
            // Si el registro fue exitoso, avanzar al siguiente paso
            const currentStep = document.getElementById('step2');
            const nextStep = document.getElementById('step3');
            
            // Actualizar indicadores de pasos
            updateStepIndicators(3);
            
            // Cambiar al siguiente paso
            currentStep.classList.remove('active');
            nextStep.classList.add('active');
            
            // Actualizar el mensaje del paso 3
            document.querySelector('#step3 .alert').textContent = 
                'Hemos registrado su cuenta y enviado un código de verificación a su correo electrónico. Por favor ingréselo a continuación:';
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector(`[data-current="2"]`).disabled = false;
            document.querySelector(`[data-current="2"]`).textContent = 'Continuar';
            alert('Hubo un error al registrar su cuenta. Por favor inténtelo de nuevo.');
        });
    }

    // Reemplazar el manejador de envío de formulario con una verificación directa
    document.getElementById('verify-button').addEventListener('click', function() {
        const codeInput = document.getElementById('verification-code-input');
        if (!codeInput || !codeInput.value.trim()) {
            alert('Por favor ingrese el código de verificación');
            return;
        }
        
        // Deshabilitar botón durante la verificación
        this.disabled = true;
        this.textContent = 'Verificando...';
        
        // Llamada AJAX para verificar el código
        fetch('<?= \yii\helpers\Url::to(['site/verify-user']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                code: codeInput.value.trim() 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                this.disabled = false;
                this.textContent = 'Completar Registro';
                alert(data.message || 'El código de verificación no es válido');
                return;
            }
            
            // En caso de éxito, redirigir al login con parámetro para mostrar toast
            let loginUrl = '<?= \yii\helpers\Url::to(['site/login'], true) ?>';
            
            // Limpiar la sesión local del navegador para evitar estados antiguos
            sessionStorage.clear();
            
            // Redirigir forzando recarga completa para procesar el mensaje flash
            window.location.href = loginUrl;
        })
        .catch(error => {
            console.error('Error:', error);
            this.disabled = false;
            this.textContent = 'Completar Registro';
            alert('Hubo un error al verificar el código. Por favor inténtelo de nuevo.');
        });
    });
});
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php 
$this->endPage();
?> 