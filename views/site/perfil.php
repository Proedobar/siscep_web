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

/* Estilos para los indicadores de progreso */
.progress-indicators {
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    position: relative;
    max-width: 600px;
    margin: 0 auto;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: #e9ecef;
    transform: translateY(-50%);
    z-index: 1;
}

.progress-step {
    position: relative;
    z-index: 2;
    background: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #6c757d;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.progress-step.active {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2);
}

.progress-step.completed {
    background: #198754;
    border-color: #198754;
    color: white;
}

.progress-step-label {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-top: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6c757d;
    white-space: nowrap;
    transition: all 0.3s ease;
}

.progress-step.active .progress-step-label {
    color: #0d6efd;
    font-weight: 600;
}

.progress-step.completed .progress-step-label {
    color: #198754;
    font-weight: 600;
}

.progress-line {
    position: absolute;
    top: 50%;
    left: 0;
    height: 2px;
    background: #198754;
    transform: translateY(-50%);
    z-index: 1;
    transition: width 0.3s ease;
}

/* Estilos para el área de carga */
.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-area:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.upload-placeholder {
    color: #6c757d;
}

/* Estilos para el contenedor del cropper */
.cropper-container {
    max-height: 400px;
    overflow: hidden;
}

/* Estilos para las fases */
.phase {
    display: none;
}

.phase.active {
    display: block;
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
                        <div class="col-md-3 d-flex flex-column align-items-center">
                            <?php if ($user->foto_perfil): ?>
                                <img src="/siscep/web<?= $user->foto_perfil ?>" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-4x text-secondary"></i>
                                </div>
                            <?php endif; ?>
                            <button class="btn btn-primary mt-3" id="btnEditarPerfil">
                                <i class="fas fa-edit me-2"></i>Editar
                            </button>
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
                        
                        <?php $form = ActiveForm::begin([
                            'id' => 'tfa-form',
                            'enableAjaxValidation' => true,
                            'options' => ['class' => 'tfa-form']
                        ]); ?>
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="tfa_toggle" value="1">
                            <input class="form-check-input" type="checkbox" role="switch" id="tfa-switch" 
                                   name="tfa_enabled" value="1"
                                   <?= $user->tfa_on ? 'checked' : '' ?> 
                                   onchange="toggleTFA(this);">
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

<!-- Modal de Edición de Perfil -->
<div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarPerfilModalLabel">Editar Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="card border-0">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="card h-100 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="fas fa-camera text-primary"></i>
                                            </div>
                                            <h5 class="card-title fw-bold m-0">Foto de Perfil</h5>
                                        </div>
                                        <div class="d-flex flex-column gap-3">
                                            <button type="button" class="btn btn-primary" id="btnCambiarFoto">
                                                <i class="fas fa-upload me-2"></i>Cambiar foto de perfil
                                            </button>
                                            <button type="button" class="btn btn-danger" id="btnEliminarFoto">
                                                <i class="fas fa-trash-alt me-2"></i>Eliminar foto de perfil
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarPerfil">
                    <span class="btn-text">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </span>
                    <span class="spinner d-none">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Guardando...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cambio de Foto de Perfil -->
<div class="modal fade" id="cambiarFotoModal" tabindex="-1" aria-labelledby="cambiarFotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cambiarFotoModalLabel">Cambiar Foto de Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Indicadores de progreso -->
                <div class="progress-indicators">
                    <div class="progress-steps">
                        <div class="progress-step active" data-step="1">
                            1
                            <span class="progress-step-label">Cargar</span>
                        </div>
                        <div class="progress-step" data-step="2">
                            2
                            <span class="progress-step-label">Recortar</span>
                        </div>
                        <div class="progress-step" data-step="3">
                            3
                            <span class="progress-step-label">Confirmar</span>
                        </div>
                        <div class="progress-line"></div>
                    </div>
                </div>

                <!-- Contenido de las fases -->
                <div class="phase-content">
                    <!-- Fase 1: Cargar Imagen -->
                    <div class="phase active" id="phase1">
                        <div class="text-center">
                            <div class="upload-area mb-4">
                                <input type="file" id="fotoInput" accept="image/*" class="d-none">
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                    <h5>Arrastra y suelta una imagen aquí</h5>
                                    <p>o</p>
                                    <button class="btn btn-primary" id="btnSelectFile">Seleccionar archivo</button>
                                </div>
                                <div id="fileInfo" class="mt-3" style="display: none;">
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="fas fa-file-image me-2"></i>
                                        <div>
                                            <strong>Archivo seleccionado:</strong>
                                            <span id="fileName"></span>
                                            <div class="small text-muted" id="fileSize"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB</p>
                        </div>
                    </div>

                    <!-- Fase 2: Recortar -->
                    <div class="phase" id="phase2">
                        <div class="cropper-container">
                            <img id="cropperImage" src="" alt="Imagen a recortar">
                        </div>
                    </div>

                    <!-- Fase 3: Confirmar -->
                    <div class="phase" id="phase3">
                        <div class="text-center">
                            <div class="preview-container mb-4">
                                <img id="previewImage" src="" alt="Vista previa" class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;">
                            </div>
                            <p>¿Te gusta cómo se ve tu nueva foto de perfil?</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnCancelarCambio">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSiguiente" disabled>Siguiente</button>
                <button type="button" class="btn btn-success" id="btnConfirmarCambio" style="display: none;">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Asegurarse de que Bootstrap JS esté cargado -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<script>
function toggleTFA(checkbox) {
    const form = document.getElementById('tfa-form');
    const formData = new FormData(form);
    
    // Mostrar loading
    Swal.fire({
        title: 'Procesando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: data.message,
                confirmButtonText: 'Aceptar'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: 'Ocurrió un error al procesar la solicitud.',
            confirmButtonText: 'Aceptar'
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Verificar que se pueda encontrar el botón y el modal
    const btnEliminar = document.getElementById('btnEliminarCuenta');
    const modalEliminar = document.getElementById('deleteAccountModal');
    
    if (btnEliminar && modalEliminar) {
        // Verificar que Bootstrap esté disponible
        if (typeof bootstrap !== 'undefined') {
            // Inicializar el modal manualmente
            const modal = new bootstrap.Modal(modalEliminar);
            
            // Agregar evento al botón
            btnEliminar.addEventListener('click', function() {
                modal.show();
            });
        } else {
            console.error('ERROR: Bootstrap no está disponible');
            alert('Error: No se pudo cargar Bootstrap. Contacte al administrador.');
        }
    }

    // Inicializar el modal
    const editarPerfilModal = new bootstrap.Modal(document.getElementById('editarPerfilModal'));
    const btnEditarPerfil = document.getElementById('btnEditarPerfil');
    const btnGuardarPerfil = document.getElementById('btnGuardarPerfil');
    const btnCambiarFoto = document.getElementById('btnCambiarFoto');
    const btnEliminarFoto = document.getElementById('btnEliminarFoto');
    
    // Manejar el clic en el botón de editar
    btnEditarPerfil.addEventListener('click', function() {
        editarPerfilModal.show();
    });
    
    // Variables para el cambio de foto
    const cambiarFotoModal = new bootstrap.Modal(document.getElementById('cambiarFotoModal'));
    const fotoInput = document.getElementById('fotoInput');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const btnSelectFile = document.getElementById('btnSelectFile');
    const btnSiguiente = document.getElementById('btnSiguiente');
    const btnConfirmarCambio = document.getElementById('btnConfirmarCambio');
    const btnCancelarCambio = document.getElementById('btnCancelarCambio');
    let cropper = null;
    let currentPhase = 1;
    
    // Manejar el clic en el botón de cambiar foto
    btnCambiarFoto.addEventListener('click', function() {
        cambiarFotoModal.show();
    });

    // Manejar el clic en el botón de eliminar foto
    btnEliminarFoto.addEventListener('click', function() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas eliminar tu foto de perfil?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar spinner
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Enviar solicitud al servidor
                fetch('<?= \yii\helpers\Url::to(['/site/eliminar-foto-perfil']) ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            // Cerrar modal y recargar página
                            editarPerfilModal.hide();
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Ocurrió un error al eliminar la foto',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la solicitud',
                        confirmButtonText: 'Aceptar'
                    });
                });
            }
        });
    });

    // Manejar la selección de archivo
    btnSelectFile.addEventListener('click', function() {
        fotoInput.click();
    });

    // Manejar el drag & drop
    uploadPlaceholder.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#0d6efd';
    });

    uploadPlaceholder.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = '#dee2e6';
    });

    uploadPlaceholder.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '#dee2e6';
        
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            handleImageFile(file);
        }
    });

    // Manejar la selección de archivo
    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            handleImageFile(file);
        }
    });

    // Función para manejar el archivo de imagen
    function handleImageFile(file) {
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El archivo es demasiado grande. El tamaño máximo permitido es 5MB.',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        // Mostrar información del archivo
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        
        fileName.textContent = file.name;
        fileSize.textContent = `Tamaño: ${(file.size / 1024 / 1024).toFixed(2)} MB`;
        fileInfo.style.display = 'block';

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('cropperImage').src = e.target.result;
            document.getElementById('previewImage').src = e.target.result;
            btnSiguiente.disabled = false;
        };
        reader.readAsDataURL(file);
    }

    // Manejar el botón siguiente
    btnSiguiente.addEventListener('click', function() {
        if (currentPhase === 1) {
            // Inicializar cropper
            const image = document.getElementById('cropperImage');
            if (cropper) {
                cropper.destroy();
            }
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        } else if (currentPhase === 2) {
            // Obtener la imagen recortada
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300
            });
            document.getElementById('previewImage').src = canvas.toDataURL('image/jpeg');
        }

        // Actualizar indicadores
        const currentStep = document.querySelector(`.progress-step[data-step="${currentPhase}"]`);
        const nextStep = document.querySelector(`.progress-step[data-step="${currentPhase + 1}"]`);
        const progressLine = document.querySelector('.progress-line');
        
        currentStep.classList.remove('active');
        currentStep.classList.add('completed');
        nextStep.classList.add('active');
        
        // Actualizar línea de progreso
        const stepWidth = 100 / 2; // 2 espacios entre 3 pasos
        progressLine.style.width = `${stepWidth * currentPhase}%`;
        
        // Cambiar fase
        document.querySelector(`#phase${currentPhase}`).classList.remove('active');
        document.querySelector(`#phase${currentPhase + 1}`).classList.add('active');
        
        currentPhase++;

        // Actualizar botones
        if (currentPhase === 3) {
            btnSiguiente.style.display = 'none';
            btnConfirmarCambio.style.display = 'block';
        }
    });

    // Manejar el botón de confirmar
    btnConfirmarCambio.addEventListener('click', function() {
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300
        });
        
        const imageData = canvas.toDataURL('image/jpeg');

        // Mostrar spinner
        Swal.fire({
            title: 'Procesando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Crear FormData para enviar la imagen
        const formData = new FormData();
        formData.append('imagen', imageData);

        // Enviar la imagen al servidor
        fetch('<?= \yii\helpers\Url::to(['/site/subir-foto-perfil']) ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Tu foto de perfil ha sido actualizada',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Cerrar modales y recargar página
                    cambiarFotoModal.hide();
                    editarPerfilModal.hide();
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Ocurrió un error al actualizar la foto',
                    confirmButtonText: 'Aceptar'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al procesar la solicitud',
                confirmButtonText: 'Aceptar'
            });
        });
    });

    // Manejar el botón de cancelar
    btnCancelarCambio.addEventListener('click', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        currentPhase = 1;
        document.querySelectorAll('.progress-step').forEach(step => {
            step.classList.remove('active', 'completed');
        });
        document.querySelector('.progress-step[data-step="1"]').classList.add('active');
        document.querySelectorAll('.phase').forEach(phase => {
            phase.classList.remove('active');
        });
        document.querySelector('#phase1').classList.add('active');
        btnSiguiente.style.display = 'block';
        btnConfirmarCambio.style.display = 'none';
        btnSiguiente.disabled = true;
        cambiarFotoModal.hide();
    });
});
</script> 