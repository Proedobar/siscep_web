<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Users $model */

$this->title = $model->email;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Usuarios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="users-view">

    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <div class="d-flex justify-content-center">
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tarjeta para ID -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 theme-card">
                <div class="card-header py-3 bg-transparent">
                    <h5 class="mb-0 fw-bold text-center">IDENTIFICADOR</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <h3 class="mb-0"><?= Html::encode($model->user_id) ?></h3>
                </div>
            </div>
        </div>

        <!-- Tarjeta para Email -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 theme-card">
                <div class="card-header py-3 bg-transparent">
                    <h5 class="mb-0 fw-bold text-center">Correo Electrónico</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <h3 class="mb-0 text-center"><?= Html::encode($model->email) ?></h3>
                </div>
            </div>
        </div>

        <!-- Tarjeta para Estado -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 theme-card">
                <div class="card-header py-3 bg-transparent">
                    <h5 class="mb-0 fw-bold text-center">Activo</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <span class="badge fs-5 <?= $model->state ? 'badge-active' : 'badge-inactive' ?>">
                        <?= $model->state ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Tarjeta para ID Empleado -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 theme-card">
                <div class="card-header py-3 bg-transparent">
                    <h5 class="mb-0 fw-bold text-center">Nombre Completo</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <h3 class="mb-0"><?= Html::encode($model->empleado->nombre)?></h3>
                </div>
            </div>
        </div>

        <!-- Tarjeta para Rol -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 theme-card">
                <div class="card-header py-3 bg-transparent">
                    <h5 class="mb-0 fw-bold text-center">Rol de Usuario</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <h3 class="mb-0"><?= Html::encode($model->rol->descripcion) ?></h3>
                </div>
            </div>
        </div>

        <!-- Tarjeta para Última Vez -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 theme-card">
                <div class="card-header py-3 bg-transparent">
                    <h5 class="mb-0 fw-bold text-center">Último Acceso</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <h3 class="mb-0"><?= Html::encode($model->ultima_vez) ?></h3>
                </div>
            </div>
        </div>

        <?php if(!empty($model->foto_perfil)): ?>
        <!-- Tarjeta para Foto de Perfil -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 theme-card">
                <div class="card-header py-3 bg-transparent">
                    <h5 class="mb-0 fw-bold text-center">Foto de Perfil</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <img src="<?= Html::encode($model->foto_perfil) ?>" 
                         alt="Foto de Perfil" 
                         class="img-fluid rounded-circle" 
                         style="width: 150px; height: 150px; object-fit: cover;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 150px; height: 150px; display: none;">
                        <i class="fas fa-user fa-4x text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Tarjeta para Foto de Perfil (Default) -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 theme-card">
                <div class="card-header py-3 bg-transparent">
                    <h5 class="mb-0 fw-bold text-center">Foto de Perfil</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                        <i class="fas fa-user fa-4x text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Botones de acción en la parte inferior -->
    <div class="row justify-content-center mt-4 mb-5">
        <div class="col-md-8">
            <div class="d-flex justify-content-center gap-2">
                <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Volver', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                <?= Html::a('<i class="fas fa-edit me-1"></i> Editar', ['update', 'user_id' => $model->user_id], ['class' => 'btn btn-primary ms-2']) ?>
                <?= Html::a('<i class="fas fa-trash me-1"></i> Eliminar', ['delete', 'user_id' => $model->user_id], [
                    'class' => 'btn btn-danger ms-2',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para la tarjeta en tema claro */
.theme-card {
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    background-color: #fff;
    border-color: #dee2e6;
    border-radius: 1.2rem;
}

.theme-card:hover {
    box-shadow: 0 1px 5px rgba(0,0,0,0.1);
}

.theme-card .card-header {
    border-bottom: 1px solid rgba(0,0,0,0.08);
    padding-bottom: 10px;
}

.badge-active {
    background-color: rgba(25,135,84,0.1);
    color: #198754;
}

.badge-inactive {
    background-color: rgba(220,53,69,0.1);
    color: #dc3545;
}

/* Estilos para la tarjeta en tema oscuro */
body.dark-mode .theme-card,
html.dark-mode .theme-card {
    background-color: #2c3136;
    border-color: #495057;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

body.dark-mode .theme-card:hover,
html.dark-mode .theme-card:hover {
    box-shadow: 0 1px 5px rgba(0,0,0,0.3);
}

body.dark-mode .card-header,
html.dark-mode .card-header,
body.dark-mode .card-footer,
html.dark-mode .card-footer {
    border-color: rgba(255,255,255,0.08);
}

body.dark-mode .text-secondary,
html.dark-mode .text-secondary {
    color: #9aa0a6 !important;
}

body.dark-mode .badge-active,
html.dark-mode .badge-active {
    background-color: rgba(25,135,84,0.2);
    color: #25c094;
}

body.dark-mode .badge-inactive,
html.dark-mode .badge-inactive {
    background-color: rgba(220,53,69,0.2);
    color: #e86774;
}
</style>
