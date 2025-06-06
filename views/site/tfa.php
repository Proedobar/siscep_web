<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\TfaForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\assets\AppAsset;

AppAsset::register($this);

$this->title = 'Verificación de Dos Factores';
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
    <style>
        .security-banner {
            background-color: #198754;
            color: white;
            padding: 0.75rem 0;
            text-align: center;
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        .security-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.1) 75%, transparent 75%, transparent);
            background-size: 20px 20px;
            opacity: 0.1;
        }
        .security-banner .security-icon {
            margin-right: 8px;
            font-size: 1.1rem;
        }
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 40px);
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .card-header {
            background-color: #198754;
            padding: 2rem 1rem;
            text-align: center;
        }
        .lock-icon-container {
            width: 80px;
            height: 80px;
            background-color: #fff;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .lock-icon {
            font-size: 2.5rem;
            color: #198754;
        }
        .form-side {
            padding: 2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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
            .security-banner {
                font-size: 0.8rem;
                padding: 0.5rem 0;
            }
            .login-card {
                height: auto;
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
            .lock-icon-container {
                width: 60px;
                height: 60px;
            }
            .lock-icon {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<!-- Security Banner -->
<div class="security-banner">
    <i class="bi bi-shield-check security-icon"></i>
    Zona segura de Autenticación y Verificación - SISCEP
</div>

<!-- Toast Container -->
<div class="toast-container">
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

<div class="login-container">
    <div class="card login-card">
        <div class="card-header">
            <div class="lock-icon-container">
                <i class="bi bi-shield-lock-fill lock-icon"></i>
            </div>
        </div>
        <div class="form-side">
            <div class="mb-0">
                <h2 class="login-title mb-0"><?= Html::encode($this->title) ?></h2>
                <small class="text-muted">Por favor ingrese el código de verificación enviado a su correo:</small>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'tfa-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'form-label'],
                    'inputOptions' => ['class' => 'form-control'],
                    'errorOptions' => ['class' => 'invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'tfa_code')->textInput([
                'autofocus' => true,
                'maxlength' => 6,
                'pattern' => '[0-9]*',
                'inputmode' => 'numeric',
                'style' => 'letter-spacing: 0.5em; text-align: center; font-size: 1.5em;'
            ])->label('Código de Verificación') ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('Verificar', ['class' => 'btn btn-primary w-100', 'name' => 'verify-button']) ?>
            </div>

            <div class="d-flex justify-content-center mt-3">
                <small><?= Html::a('Volver al Login', ['site/login'], ['class' => 'text-muted text-decoration-none']) ?></small>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar toasts
        var toastElList = document.querySelectorAll('.toast');
        if (toastElList.length > 0) {
            toastElList.forEach(function(toastEl) {
                var bsToast = new bootstrap.Toast(toastEl, {
                    animation: true,
                    autohide: true,
                    delay: 5000
                });
                bsToast.show();
            });
        }
        
        // Formatear input de código TFA
        const tfaInput = document.querySelector('#tfaform-tfa_code');
        if (tfaInput) {
            tfaInput.addEventListener('input', function(e) {
                // Solo permitir números
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Limitar a 6 dígitos
                if (this.value.length > 6) {
                    this.value = this.value.slice(0, 6);
                }
            });
        }
    });
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?> 