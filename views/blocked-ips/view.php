<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\BlockedIps $model */

$this->title = $model->ip;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Blocked Ips'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// Registrar los assets necesarios
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/sweetalert-handlers.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="blocked-ips-view">

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
                    <h3 class="mb-0"><?= Html::encode($model->id) ?></h3>
                </div>
            </div>
        </div>

        <!-- Tarjeta para IP -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 theme-card">
                <div class="card-header py-3 bg-transparent">
                    <h5 class="mb-0 fw-bold text-center">Dirección IP</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <h3 class="mb-0 text-center"><?= Html::encode($model->ip) ?></h3>
                </div>
            </div>
        </div>

        <!-- Tarjeta para Fecha y Hora de Bloqueo -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 theme-card">
                <div class="card-header py-3 bg-transparent">
                    <h5 class="mb-0 fw-bold text-center">Fecha y Hora de Bloqueo</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <h3 class="mb-0"><?= Html::encode($model->fecha_hora_bloqueo) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Botones de acción en la parte inferior -->
    <div class="row justify-content-center mt-4 mb-5">
        <div class="col-md-8">
            <div class="d-flex justify-content-center gap-2">
                <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Volver', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                <?= Html::a('<i class="fas fa-trash me-1"></i> Levantar Bloqueo', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger ms-2',
                    'onclick' => 'confirmarEliminacion("' . Url::to(['delete', 'id' => $model->id]) . '"); return false;'
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
</style>
