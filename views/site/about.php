<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Acerca de | SISCEP';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-about">
    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Acerca del Sistema</h1>
    </div>

    <div class="row">
        <!-- Tarjeta principal de información -->
        <div class="col-12 mb-4">
            <div class="card h-100 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-info-circle text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Sistema de Constancias y Emisión de Pagos</h5>
                    </div>
                    <p class="card-text fs-5 text-muted">
                        Sistema de Constancias y Emisión de Pagos de la Procuraduría General del Estado Barinas. 
                        Herramienta diseñada para gestionar de manera eficiente los procesos relacionados con la emisión 
                        de constancias laborales y recibos de pago para los empleados.
                    </p>
                </div>
            </div>
        </div>

        <!-- Información Técnica -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-code-branch text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Versión</h5>
                    </div>
                    <p class="card-text fs-5 text-muted">2.0.0</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-success border-3 rounded-3 hover-shadow glow-success">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-user-tie text-success"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Desarrollador</h5>
                    </div>
                    <p class="card-text fs-5 text-muted">Programador Petit Diego</p>
                    <div class="mt-2 badge border tiempo-servicio">Bajo la supervisión de la Ing. Amira</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-danger border-3 rounded-3 hover-shadow glow-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-building text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Departamento</h5>
                    </div>
                    <p class="card-text fs-5 text-muted">Dirección de Informática y Comunicación Corporativa</p>
                </div>
            </div>
        </div>

        <!-- Funcionalidades -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Gestión de empleados</h5>
                    </div>
                    <p class="card-text fs-5 text-muted">Administración de datos laborales</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-success border-3 rounded-3 hover-shadow glow-success">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-file-alt text-success"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Generación de constancias</h5>
                    </div>
                    <p class="card-text fs-5 text-muted">Documentos laborales</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-danger border-3 rounded-3 hover-shadow glow-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-receipt text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Emisión de recibos</h5>
                    </div>
                    <p class="card-text fs-5 text-muted">Comprobantes de pago</p>
                </div>
            </div>
        </div>

        <!-- Soporte -->
        <div class="col-12">
            <div class="card h-100 border-start border-soft-info border-3 rounded-3 hover-shadow glow-info">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-headset text-info"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Soporte</h5>
                    </div>
                    <p class="card-text fs-5 text-muted">Para obtener información detallada sobre el uso del sistema, consulte el manual de usuario. (Por ahora no disponible)</p>
                    <div class="text-center mt-4">
                        <?= Html::a('<i class="fas fa-download me-2"></i>Manual de Usuario', ['site/construccion'], ['class' => 'btn btn-info btn-lg']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

.border-soft-info {
    border-color: rgba(13, 202, 240, 0.72) !important;
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

.glow-info {
    box-shadow: 0 0 20px 5px rgba(13, 202, 240, 0.2) !important;
}

.tiempo-servicio {
    background-color: #f0f0f0;
    color: #212529;
    transition: background-color 0.3s ease, color 0.3s ease;
}

html.dark-mode .tiempo-servicio,
body.dark-mode .tiempo-servicio {
    background-color: #3a3f45;
    color: #f8f9fa;
}

.logo-container img {
    transition: transform 0.3s ease;
}

.logo-container img:hover {
    transform: scale(1.05);
}
</style>
