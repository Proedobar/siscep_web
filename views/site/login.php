<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\assets\AppAsset;

AppAsset::register($this);

$this->title = 'Iniciar Sesión';
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 905px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .login-card .row {
            min-height: 520px;
        }
        .login-card .col-md-5,
        .login-card .col-md-7 {
            display: flex;
            flex-direction: column;
        }
        .logo-side {
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            height: 100%;
            min-height: 520px;
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
            justify-content: space-between;
            min-height: 520px;
        }
        .login-title {
            margin-bottom: 1.5rem;
        }
        /* Estilos para los toast */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }
        .toast {
            min-width: 300px;
        }

        /* Estilos responsivos */
        @media (max-width: 767.98px) {
            .login-card {
                height: auto;
            }
            .login-card .row {
                flex-direction: column;
                min-height: auto;
            }
            .logo-side {
                padding: 1.5rem;
                min-height: 200px;
            }
            .form-side {
                padding: 1.5rem;
                min-height: auto;
            }
            .toast-container {
                right: 10px;
                left: 10px;
                bottom: 10px;
            }
            .toast {
                min-width: auto;
                width: 100%;
            }
        }

        /* Estilos específicos para desktop */
        @media (min-width: 768px) {
            .login-card .row {
                height: 520px;
            }
            .form-side {
                padding: 3rem 2rem;
            }
            .form-group {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<!-- Debug información para toasts -->
<div style="display:none;" id="debug-info">
    <?php 
    if (Yii::$app->session->hasFlash('success')) {
        echo "SUCCESS FLASH: " . Yii::$app->session->getFlash('success');
    } else {
        echo "No success flash encontrado";
    }
    ?>
</div>

<!-- Toast Container -->
<div class="toast-container">
    <?php if (Yii::$app->session->hasFlash('success') || Yii::$app->session->get('registration_success')): ?>
    <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <strong><i class="bi bi-check-circle-fill me-2"></i> ¡Éxito!</strong>
                <div><?= Yii::$app->session->getFlash('success') ?: 'Gracias por registrarse. Su cuenta ha sido verificada correctamente. Ahora puede iniciar sesión.' ?></div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    <?php 
    // Limpiar la variable de sesión después de usarla
    Yii::$app->session->remove('registration_success');
    endif; 
    ?>
    
    <?php if (Yii::$app->session->hasFlash('warning')): ?>
    <div class="toast align-items-center text-white bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i> Atención</strong>
                <div><?= Yii::$app->session->getFlash('warning') ?></div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <strong><i class="bi bi-exclamation-circle-fill me-2"></i> Error</strong>
                <div><?= Yii::$app->session->getFlash('error') ?></div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Inicializar toasts inmediatamente -->
<script>
    // Inicializar los toasts inmediatamente sin esperar a DOMContentLoaded
    window.addEventListener('load', function() {
        console.log('Inicializando toasts en carga de ventana...');
        var toastElList = document.querySelectorAll('.toast');
        console.log('Toasts encontrados (load):', toastElList.length);
        
        if (toastElList.length > 0) {
            toastElList.forEach(function(toastEl) {
                try {
                    var bsToast = new bootstrap.Toast(toastEl, {
                        animation: true,
                        autohide: true,
                        delay: 5000
                    });
                    bsToast.show();
                } catch (error) {
                    console.error('Error al inicializar toast (load):', error);
                }
            });
        }
    });
</script>

<div class="login-container">
    <div class="card login-card">
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
                        <h2 class="login-title mb-0"><?= Html::encode($this->title) ?></h2>
                        <small class="text-muted">Por favor complete los siguientes campos para iniciar sesión:</small>
                    </div>

                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'labelOptions' => ['class' => 'form-label'],
                            'inputOptions' => ['class' => 'form-control'],
                            'errorOptions' => ['class' => 'invalid-feedback'],
                        ],
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                    ]); ?>

                    <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Correo') ?>

                    <div class="form-group">
                        <?= $form->field($model, 'password', [
                            'inputTemplate' => '<div class="input-group">{input}<button type="button" id="togglePassword" class="btn btn-outline-secondary"><i class="bi bi-eye-slash"></i></button></div>'
                        ])->passwordInput(['id' => 'password-field'])->label('Contraseña') ?>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            console.log('Inicializando toasts...');
                            
                            // Mostrar la información de depuración en la consola
                            var debugInfo = document.getElementById('debug-info');
                            if (debugInfo) {
                                console.log('Información de depuración:', debugInfo.textContent);
                            }
                            
                            // Inicializar los toasts usando directamente el constructor de Bootstrap
                            var toastElList = document.querySelectorAll('.toast');
                            console.log('Toasts encontrados:', toastElList.length);
                            
                            if (toastElList.length > 0) {
                                toastElList.forEach(function(toastEl) {
                                    console.log('Inicializando toast:', toastEl);
                                    try {
                                        var bsToast = new bootstrap.Toast(toastEl, {
                                            animation: true,
                                            autohide: true,
                                            delay: 5000
                                        });
                                        console.log('Toast creado, mostrando...');
                                        bsToast.show();
                                    } catch (error) {
                                        console.error('Error al inicializar toast:', error);
                                    }
                                });
                            }
                            
                            // Manejo de mostrar/ocultar contraseña
                            const togglePassword = document.querySelector('#togglePassword');
                            const passwordField = document.querySelector('#password-field');
                            
                            if (togglePassword && passwordField) {
                                togglePassword.addEventListener('click', function() {
                                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                                    passwordField.setAttribute('type', type);
                                    
                                    // Cambiar el icono
                                    this.querySelector('i').classList.toggle('bi-eye');
                                    this.querySelector('i').classList.toggle('bi-eye-slash');
                                });
                            }
                        });
                    </script>

                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => "<div class=\"form-check\">{input} {label}</div>\n<div>{error}</div>",
                        'labelOptions' => ['class' => 'form-check-label'],
                        'inputOptions' => ['class' => 'form-check-input'],
                    ]) ?>

                    <div class="form-group mt-4">
                        <?= Html::submitButton('Entrar', ['class' => 'btn btn-primary w-100', 'name' => 'login-button', 'id' => 'login-button']) ?>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <small><?= Html::a('¿Olvidó su contraseña?', ['site/request-password-reset'], ['class' => 'text-muted text-decoration-none']) ?></small>
                        <small><?= Html::a('Regístrese', ['site/signup'], ['class' => 'text-muted text-decoration-none']) ?></small>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener el formulario y el botón
    const form = document.getElementById('login-form');
    const submitButton = document.getElementById('login-button');
    
    // Agregar evento submit
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Deshabilitar el botón de envío
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
        
        try {
            // Obtener los valores del formulario
            const formData = new FormData(form);
            
            // Enviar la solicitud
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const contentType = response.headers.get('content-type');
            const data = contentType && contentType.includes('application/json') 
                ? await response.json() 
                : await response.text();
            
            if (typeof data === 'object' && data.success) {
                // Si la respuesta es JSON y fue exitosa, redirigir
                window.location.href = data.redirect;
            } else {
                // Si la respuesta es HTML, buscar errores
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                const errorMessages = doc.querySelectorAll('.invalid-feedback');
                
                if (errorMessages.length > 0) {
                    // Si hay errores, mostrarlos con SweetAlert
                    await Swal.fire({
                        icon: 'error',
                        title: 'Error de autenticación',
                        html: Array.from(errorMessages).map(error => error.textContent).join('<br>'),
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            await Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ha ocurrido un error al procesar su solicitud',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
                allowEscapeKey: false
            });
        } finally {
            // Rehabilitar el botón de envío
            submitButton.disabled = false;
            submitButton.innerHTML = 'Entrar';
        }
        
        return false;
    });
});
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php 
$this->endPage();
?>
