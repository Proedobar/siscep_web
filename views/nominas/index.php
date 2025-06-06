<?php

use app\models\Nominas;
use app\models\HistoricDetalles;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\HistoricDetallesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Gestión de Nominas');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nominas-index">

    <h1 class="text-center mb-4"><?= Html::encode($this->title) ?></h1>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        .card-header {
            padding: 1.4rem !important;
            text-align: center;
        }
        .card-header i {
            font-size: 1.4em;
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
        .border-soft-warning {
            border-color: rgba(255, 193, 7, 0.72) !important;
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
        .glow-warning {
            box-shadow: 0 0 20px 5px rgba(255, 193, 7, 0.2) !important;
        }
        .icon-box {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card h-100 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
                <div class="card-header">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-center">Cargar un formato</h5>
                    <p class="card-text text-center">Carga un archivo de Excel con los datos de los empleados.</p>
                    <div class="text-center mt-3">
                        <?= Html::a('Ir', ['create'], ['class' => 'btn btn-primary w-100']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card h-100 border-start border-soft-success border-3 rounded-3 hover-shadow glow-success">
                <div class="card-header">
                    <i class="bi bi-download"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-center">Descargar un formato</h5>
                    <p class="card-text text-center">Descarga el formato de Excel (con ayudante) para cargar los datos de los empleados.</p>
                    <div class="text-center mt-3">
                        <?= Html::a('Descargar', ['nominas/download'], ['class' => 'btn btn-success w-100']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <h2 class="mb-4 text-center">Historial de Operaciones</h2>

    <div class="row">
        <?php foreach ($dataProvider->getModels() as $model): ?>
            <?php
            $estadoClass = '';
            $estadoText = '';
            $estadoIcon = '';
            
            switch($model->estado) {
                case 0:
                    $estadoClass = 'danger';
                    $estadoText = 'Error';
                    $estadoIcon = 'bi-x-circle-fill';
                    break;
                case 1:
                    $estadoClass = 'success';
                    $estadoText = 'Hecho';
                    $estadoIcon = 'bi-check-circle-fill';
                    break;
                case 2:
                    $estadoClass = 'warning';
                    $estadoText = 'Revertido';
                    $estadoIcon = 'bi-arrow-counterclockwise';
                    break;
            }
            ?>
            <div class="col-12 mb-4">
                <div class="card h-100 border-start border-soft-<?= $estadoClass ?> border-3 rounded-3 hover-shadow glow-<?= $estadoClass ?>">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-<?= $estadoClass ?> bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi <?= $estadoIcon ?> text-<?= $estadoClass ?>"></i>
                            </div>
                            <h5 class="card-title fw-bold m-0">
                                <?php
                                $meses = [
                                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                ];
                                echo Html::encode($meses[$model->mes] ?? $model->mes . ' ' . $model->anio);
                                ?>
                            </h5>
                        </div>
                        <div class="card-text">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-2">
                                        <strong>Período:</strong> <?= Html::encode($model->periodo == 1 ? 'Primera Quincena' : 'Segunda Quincena') ?>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-2">
                                        <strong>Estado:</strong> 
                                        <span class="badge bg-<?= $estadoClass ?>">
                                            <?= Html::encode($estadoText) ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-0">
                                        <strong>Fecha:</strong> <?= Html::encode(date('d/m/Y H:i', strtotime($model->created_at))) ?>
                                    </p>
                                </div>
                            </div>
                            <?php if ($model->estado !== 2): ?>
                            <hr class="my-3">
                            <div class="d-flex justify-content-end">
                                <?= Html::a(
                                    '<div class="d-flex align-items-center">
                                        <div class="icon-box bg-warning bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-arrow-counterclockwise text-warning"></i>
                                        </div>
                                        <span>Revertir</span>
                                    </div>', 
                                    ['nominas/revert', 'id' => $model->id], 
                                    [
                                        'class' => 'text-decoration-none text-warning',
                                        'data' => [
                                            'confirm' => '¿Está seguro de que desea revertir esta operación? Esta acción no se puede deshacer.',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
