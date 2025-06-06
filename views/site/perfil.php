<?php

/** @var yii\web\View $this */
/** @var app\models\Users $user */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Mi Perfil';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.icon-box {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.hover-shadow:hover {
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    transition: box-shadow 0.3s ease-in-out;
}
.border-soft-primary {
    border-color: rgba(13, 109, 253, 0.68) !important;
}
.border-soft-success {
    border-color: rgba(25, 135, 84, 0.69) !important;
}
.border-soft-danger {
    border-color: rgba(220, 53, 70, 0.72) !important;
}
.glow-primary {
    box-shadow: 0 0 20px 5px rgba(13, 109, 253, 0.2) !important;
}
.glow-success {
    box-shadow: 0 0 20px 5px rgba(25, 135, 84, 0.2) !important;
}
.glow-danger {
    box-shadow: 0 0 20px 5px rgba(220, 53, 70, 0.2) !important;
}
.card {
    transition: all 0.3s ease;
    border-radius: 0.55rem;
}
.tiempo-servicio {
    background-color: #f0f0f0;
    color: #212529;
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Estilo distintivo para subtarjetas */
.profile-info .card {
    background-color: #f8f9fa;
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.profile-info .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Estilos para modo oscuro */
html.dark-mode .profile-info .card,
body.dark-mode .profile-info .card {
    background-color: #2a2e32;
    border: 1px solid rgba(255,255,255,0.05);
}

html.dark-mode .profile-info .card:hover,
body.dark-mode .profile-info .card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

html.dark-mode .tiempo-servicio,
body.dark-mode .tiempo-servicio {
    background-color: #3a3f45;
    color: #f8f9fa;
}
</style>

<div class="site-perfil">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <!-- Tarjeta de Información del Perfil -->
        <div class="col-12">
            <div class="card mb-4 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
                <div class="card-body">
                    <div class="row">
                        <!-- Foto de perfil (columna izquierda) -->
                        <div class="col-md-3 d-flex justify-content-center align-items-start">
                            <?php if ($user->foto_perfil): ?>
                                <img src="<?= $user->foto_perfil ?>" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-4x text-secondary"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Información del perfil (columna derecha) -->
                        <div class="col-md-9">
                            <div class="profile-info">
                                <div class="row row-cols-1 row-cols-md-3 g-3">
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <h6 class="card-title fw-bold m-0">Nombre</h6>
                                                </div>
                                                <p class="card-text"><?= Html::encode($user->empleado->nombre) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                                        <i class="fas fa-id-card text-success"></i>
                                                    </div>
                                                    <h6 class="card-title fw-bold m-0">Documento</h6>
                                                </div>
                                                <p class="card-text"><?= Html::encode($user->empleado->ci) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="icon-box bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                                                        <i class="fas fa-envelope text-danger"></i>
                                                    </div>
                                                    <h6 class="card-title fw-bold m-0">Email</h6>
                                                </div>
                                                <p class="card-text"><?= Html::encode($user->email) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                                        <i class="fas fa-user-tag text-primary"></i>
                                                    </div>
                                                    <h6 class="card-title fw-bold m-0">Rol</h6>
                                                </div>
                                                <p class="card-text"><?= Html::encode($user->rol->descripcion) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                                        <i class="fas fa-clock text-success"></i>
                                                    </div>
                                                    <h6 class="card-title fw-bold m-0">Última Conexión</h6>
                                                </div>
                                                <p class="card-text"><?= $user->ultima_vez ? Yii::$app->formatter->asDatetime($user->ultima_vez) : 'Nunca' ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tarjeta de Configuración de Cuenta -->
        <div class="col-12">
            <div class="card mb-4 border-start border-soft-danger border-3 rounded-3 hover-shadow glow-danger">
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Autenticación de Dos Factores</h6>
                        
                        <?php $form = ActiveForm::begin(['id' => 'tfa-form']); ?>
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="tfa_toggle" value="1">
                            <input class="form-check-input" type="checkbox" role="switch" id="tfa-switch" 
                                   <?= $user->tfa_on ? 'checked' : '' ?> 
                                   onchange="document.getElementById('tfa-form').submit();">
                            <label class="form-check-label" for="tfa-switch">
                                <?= $user->tfa_on ? 'Activada' : 'Desactivada' ?>
                            </label>
                        </div>
                        <?php ActiveForm::end(); ?>
                        
                        <p class="small text-muted">
                            La autenticación de dos factores añade una capa extra de seguridad a tu cuenta 
                            requiriendo un código adicional cuando inicies sesión.
                        </p>
                    </div>
                    
                    <hr>
                    
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3 text-danger">Eliminar Cuenta</h6>
                        
                        <p class="small text-muted mb-3">
                            Al eliminar tu cuenta, tu información personal será marcada como eliminada y 
                            ya no podrás acceder al sistema con estas credenciales.
                        </p>
                        
                        <button type="button" class="btn btn-danger" id="btnEliminarCuenta">
                            <i class="fas fa-trash-alt me-2"></i> Eliminar mi cuenta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar Cuenta -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">Confirmación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" action="<?= \yii\helpers\Url::to(['site/perfil']) ?>" style="display:inline;">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <input type="hidden" name="delete_account" value="1">
                    <button type="submit" class="btn btn-danger">Confirmar Eliminación</button>
                </form>
            </div>
        </div>
    </div> 
</div>

<!-- Asegurarse de que Bootstrap JS esté cargado -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que se pueda encontrar el botón y el modal
    const btnEliminar = document.getElementById('btnEliminarCuenta');
    const modalEliminar = document.getElementById('deleteAccountModal');
    
    console.log('Botón encontrado:', btnEliminar !== null);
    console.log('Modal encontrado:', modalEliminar !== null);
    
    if (btnEliminar && modalEliminar) {
        // Verificar que Bootstrap esté disponible
        if (typeof bootstrap !== 'undefined') {
            console.log('Bootstrap encontrado:', bootstrap);
            
            // Inicializar el modal manualmente
            const modal = new bootstrap.Modal(modalEliminar);
            
            // Agregar evento al botón
            btnEliminar.addEventListener('click', function() {
                console.log('Botón de eliminar cuenta clickeado');
                modal.show();
            });
            
            console.log('Evento de clic configurado en botón');
        } else {
            console.error('ERROR: Bootstrap no está disponible');
            alert('Error: No se pudo cargar Bootstrap. Contacte al administrador.');
        }
    } else {
        console.error('ERROR: No se encontró el botón o el modal');
    }
});
</script> 