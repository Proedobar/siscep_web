<?php

use app\models\Directores;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\DirectoresSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Directores');
$this->params['breadcrumbs'][] = $this->title;

// Registrar los assets necesarios
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/sweetalert-handlers.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="directores-index">

    <h1 class="text-center mb-4"><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'pjax-container']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <!-- Tarjeta para crear nuevo director -->
        <div class="col-lg-4 col-md-6 mb-3">
            <?= Html::a(
                '<div class="card h-100 theme-card create-card">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <i class="fas fa-plus create-icon"></i>
                    </div>
                </div>',
                ['create'],
                ['class' => 'text-decoration-none create-link']
            ) ?>
        </div>
        
        <?php foreach ($dataProvider->getModels() as $model): ?>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 theme-card">
                    <div class="card-header py-3 bg-transparent">
                        <h5 class="mb-0 fw-bold text-center director-name"><?= Html::encode($model->nombre_director) ?></h5>
                    </div>
                    <div class="card-body py-2">
                        <div class="mb-1">
                            <small class="text-secondary">Estado:</small>
                            <span class="ms-1 badge <?= $model->activo ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $model->activo ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0">
                        <div class="d-flex justify-content-end gap-1">
                            <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-sm btn-link theme-link']) ?>
                            <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-link theme-link']) ?>
                            <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-link theme-link-danger',
                                'onclick' => 'confirmarEliminacion("' . Url::to(['delete', 'id' => $model->id]) . '"); return false;'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination-container d-flex justify-content-center mt-4">
        <?= \yii\widgets\LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'options' => ['class' => 'pagination'],
            'linkOptions' => ['class' => 'page-link'],
            'linkContainerOptions' => ['class' => 'page-item'],
            'disabledListItemSubTagOptions' => ['class' => 'page-link'],
        ]) ?>
    </div>

    <?php Pjax::end(); ?>

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

.director-name {
    font-size: 1.15rem;
    color: #495057;
    overflow: hidden;
    text-overflow: ellipsis;
}

.director-id {
    font-size: 0.8rem;
}

/* Estilos para la tarjeta de creación */
.create-card {
    border: 2px dashed rgba(13, 110, 253, 0.3);
    background-color: rgba(13, 110, 253, 0.03);
    min-height: 200px;
    transition: all 0.3s ease;
}

.create-card:hover {
    border-color: rgba(13, 110, 253, 0.8);
    background-color: rgba(13, 110, 253, 0.12);
}

.create-icon {
    font-size: 3rem;
    color: rgba(13, 110, 253, 0.5);
    transition: all 0.3s ease;
}

.create-link:hover .create-icon {
    color: rgba(13, 110, 253, 0.9);
    transform: scale(1.1);
}

.theme-link {
    padding: 0.25rem 0.5rem;
    color: #555;
    text-decoration: none;
}

.theme-link:hover {
    background: rgba(0,0,0,0.03);
    border-radius: 3px;
    color: #212529;
}

.theme-link-danger {
    padding: 0.25rem 0.5rem;
    color: #dc3545;
    text-decoration: none;
}

.theme-link-danger:hover {
    background: rgba(220,53,69,0.1);
    border-radius: 3px;
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

body.dark-mode .create-card,
html.dark-mode .create-card {
    border: 2px dashed rgba(108, 152, 255, 0.3);
    background-color: rgba(108, 152, 255, 0.05);
}

body.dark-mode .create-card:hover,
html.dark-mode .create-card:hover {
    border-color: rgba(108, 152, 255, 0.8);
    background-color: rgba(50, 60, 80, 0.5);
}

body.dark-mode .create-icon,
html.dark-mode .create-icon {
    color: rgba(108, 152, 255, 0.6);
}

body.dark-mode .create-link:hover .create-icon,
html.dark-mode .create-link:hover .create-icon {
    color: rgba(108, 152, 255, 1);
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

body.dark-mode .director-name,
html.dark-mode .director-name {
    color: #e9ecef;
}

body.dark-mode .text-muted,
html.dark-mode .text-muted {
    color: #adb5bd !important;
}

body.dark-mode .text-secondary,
html.dark-mode .text-secondary {
    color: #9aa0a6 !important;
}

body.dark-mode .theme-link,
html.dark-mode .theme-link {
    color: #adb5bd;
}

body.dark-mode .theme-link:hover,
html.dark-mode .theme-link:hover {
    background: rgba(255,255,255,0.05);
    color: #f8f9fa;
}

body.dark-mode .theme-link-danger,
html.dark-mode .theme-link-danger {
    color: #e86774;
}

body.dark-mode .theme-link-danger:hover,
html.dark-mode .theme-link-danger:hover {
    background: rgba(232,103,116,0.1);
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

/* Estilos para el paginador en tema oscuro */
body.dark-mode .page-link,
html.dark-mode .page-link {
    background-color: #2c3136;
    border-color: #495057;
    color: #adb5bd;
}

body.dark-mode .page-link:hover,
html.dark-mode .page-link:hover {
    background-color: #343a40;
    color: #f8f9fa;
}

body.dark-mode .page-item.active .page-link,
html.dark-mode .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}

body.dark-mode .page-item.disabled .page-link,
html.dark-mode .page-item.disabled .page-link {
    background-color: #343a40;
    border-color: #495057;
    color: #6c757d;
}
</style>
