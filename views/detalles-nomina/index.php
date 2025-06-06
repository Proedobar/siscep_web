<?php

use app\models\DetallesNomina;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\DetallesNominaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Mis Recibos de Pago');
$this->params['breadcrumbs'][] = $this->title;

// Obtener el ID del empleado del usuario actual
$empleado_id = Yii::$app->user->identity->empleado_id;
?>

<div class="detalles-nomina-index">
    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4"><?= Html::encode($this->title) ?></h1>
        <p class="lead">Consulta y descarga tus recibos de pago</p>
    </div>

    <!-- Sección de Filtros -->
    <div class="card mb-4 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
        <div class="card-header bg-transparent">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                    <i class="fas fa-filter text-primary"></i>
                </div>
                <h3 class="card-title mb-0"><?= Yii::t('app', 'Filtros de Búsqueda') ?></h3>
            </div>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['data-pjax' => true],
            ]); ?>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($searchModel, 'nomina_id')->dropDownList(
                        $searchModel->getNominasDisponibles(),
                        [
                            'prompt' => 'Todas mis Nóminas',
                            'class' => 'form-control form-control-lg'
                        ]
                    )->label("Nómina") ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($searchModel, 'mes', ['enableClientValidation' => true])->dropDownList([
                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
                        4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
                        10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                    ], [
                        'prompt' => 'Seleccione Mes *',
                        'required' => true,
                        'class' => 'form-control form-control-lg'
                    ])->label("Mes") ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($searchModel, 'anio', ['enableClientValidation' => true])->dropDownList(
                        array_combine(range(date('Y'), date('Y')-5), range(date('Y'), date('Y')-5)),
                        [
                            'prompt' => 'Seleccione Año *',
                            'required' => true,
                            'class' => 'form-control form-control-lg'
                        ]
                    )->label("Año") ?>
                </div>
            </div>

            <div class="form-group text-center mt-4">
                <?= Html::submitButton(Yii::t('app', 'Buscar'), ['class' => 'btn btn-primary btn-lg px-4 me-2']) ?>
                <?= Html::a(
                    Yii::t('app', 'Limpiar'),
                    ['detalles-nomina/index'],
                    ['class' => 'btn btn-outline-secondary btn-lg px-4']
                ) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Sección de Resultados -->
    <?php Pjax::begin(); ?>
    <div class="row">
        <?php foreach ($dataProvider->getModels() as $model): ?>
            <div class="col-md-6 mb-4">
                <div class="card border-start border-soft-success border-3 rounded-3 hover-shadow glow-success h-100">
                    <div class="card-header bg-transparent">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-file-invoice-dollar text-success"></i>
                            </div>
                            <h5 class="card-title mb-0">
                                <?= Html::encode($model->nomina->nomina ?? 'N/A') ?>
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><small class="text-muted">Cargo:</small><br><span class="fs-5"><?= Html::encode($model->cargo) ?></span></p>
                                <p class="mb-2"><small class="text-muted">Tipo de Cargo:</small><br><span class="fs-5"><?= Html::encode($model->tipo_cargo) ?></span></p>
                                <p class="mb-2"><small class="text-muted">Mes/Año:</small><br><span class="fs-5"><?= Html::encode($model->mes . '/' . $model->anio) ?></span></p>
                                <p class="mb-2"><small class="text-muted">Período:</small><br><span class="fs-5"><?= Html::encode($model->periodo == 1 ? 'Primera Quincena' : 'Segunda Quincena') ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <div class="asignaciones-box p-3 rounded-3 bg-light mb-3">
                                    <p class="mb-2"><small class="text-muted fw-bold">Asignaciones</small></p>
                                    <p class="mb-1">Sueldo: <?= Yii::$app->formatter->asCurrency($model->sueldo_quinc) ?></p>
                                    <p class="mb-1">Prima Hijos: <?= Yii::$app->formatter->asCurrency($model->prima_hijos) ?></p>
                                    <p class="mb-1">Prima Prof.: <?= Yii::$app->formatter->asCurrency($model->prima_prof) ?></p>
                                    <p class="mb-0">Cesta Tickets: <?= Yii::$app->formatter->asCurrency($model->cesta_tickets) ?></p>
                                </div>
                                
                                <div class="deducciones-box p-3 rounded-3 bg-light">
                                    <p class="mb-2"><small class="text-muted fw-bold">Deducciones</small></p>
                                    <p class="mb-1">IVSS: <?= Yii::$app->formatter->asCurrency($model->ivss) ?></p>
                                    <p class="mb-1">PIE: <?= Yii::$app->formatter->asCurrency($model->pie) ?></p>
                                    <p class="mb-0">FAOV: <?= Yii::$app->formatter->asCurrency($model->faov) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="total-box p-3 rounded-3 bg-success bg-opacity-10">
                                    <p class="h5 mb-0 text-success">
                                        <small class="text-muted">Total a Pagar:</small>
                                        <span class="float-end fw-bold"><?= Yii::$app->formatter->asCurrency($model->montopagar) ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent text-center border-top">
                        <?= Html::button(
                            '<i class="fas fa-file-alt me-2"></i>' . Yii::t('app', 'Vista Previa'),
                            [
                                'class' => 'btn btn-outline-success btn-lg',
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#reciboModal',
                                'data-detail-id' => $model->detail_id,
                                'onclick' => 'cargarDatosRecibo(' . $model->detail_id . ')'
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <div class="col-12">
            <?= \yii\widgets\LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'options' => ['class' => 'pagination justify-content-center']
            ]) ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>

<!-- Modal de Vista Previa del Recibo -->
<div class="modal fade" id="reciboModal" tabindex="-1" aria-labelledby="reciboModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reciboModalLabel">Vista Previa del Recibo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card border-0">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Información del Empleado -->
                            <div class="col-md-6">
                                <div class="card h-100 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <h5 class="card-title fw-bold m-0">Información del Empleado</h5>
                                        </div>
                                        <div id="empleadoInfo">
                                            <p class="mb-2"><small class="text-muted">Nombre:</small><br>
                                                <span class="fs-5" id="nombreEmpleado"></span>
                                            </p>
                                            <p class="mb-2"><small class="text-muted">Cédula:</small><br>
                                                <span class="fs-5" id="cedulaEmpleado"></span>
                                            </p>
                                            <p class="mb-0"><small class="text-muted">Cargo:</small><br>
                                                <span class="fs-5" id="cargoEmpleado"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de la Nómina -->
                            <div class="col-md-6">
                                <div class="card h-100 border-start border-soft-success border-3 rounded-3 hover-shadow glow-success">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="fas fa-file-invoice text-success"></i>
                                            </div>
                                            <h5 class="card-title fw-bold m-0">Detalles de la Nómina</h5>
                                        </div>
                                        <div id="nominaInfo">
                                            <p class="mb-2"><small class="text-muted">Tipo de Nómina:</small><br>
                                                <span class="fs-5" id="tipoNomina"></span>
                                            </p>
                                            <p class="mb-2"><small class="text-muted">Período:</small><br>
                                                <span class="fs-5" id="periodoNomina"></span>
                                            </p>
                                            <p class="mb-0"><small class="text-muted">Mes/Año:</small><br>
                                                <span class="fs-5" id="mesAnioNomina"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Asignaciones -->
                            <div class="col-md-6">
                                <div class="card h-100 border-start border-soft-info border-3 rounded-3 hover-shadow glow-info">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="icon-box bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="fas fa-plus-circle text-info"></i>
                                            </div>
                                            <h5 class="card-title fw-bold m-0">Asignaciones</h5>
                                        </div>
                                        <div id="asignacionesInfo">
                                            <p class="mb-2"><small class="text-muted">Sueldo:</small><br>
                                                <span class="fs-5" id="sueldoEmpleado"></span>
                                            </p>
                                            <p class="mb-2"><small class="text-muted">Prima por Hijos:</small><br>
                                                <span class="fs-5" id="primaHijos"></span>
                                            </p>
                                            <p class="mb-2"><small class="text-muted">Prima Profesional:</small><br>
                                                <span class="fs-5" id="primaProfesional"></span>
                                            </p>
                                            <p class="mb-0"><small class="text-muted">Cesta Tickets:</small><br>
                                                <span class="fs-5" id="cestaTickets"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Deducciones -->
                            <div class="col-md-6">
                                <div class="card h-100 border-start border-soft-danger border-3 rounded-3 hover-shadow glow-danger">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="icon-box bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="fas fa-minus-circle text-danger"></i>
                                            </div>
                                            <h5 class="card-title fw-bold m-0">Deducciones</h5>
                                        </div>
                                        <div id="deduccionesInfo">
                                            <p class="mb-2"><small class="text-muted">IVSS:</small><br>
                                                <span class="fs-5" id="ivss"></span>
                                            </p>
                                            <p class="mb-2"><small class="text-muted">PIE:</small><br>
                                                <span class="fs-5" id="pie"></span>
                                            </p>
                                            <p class="mb-0"><small class="text-muted">FAOV:</small><br>
                                                <span class="fs-5" id="faov"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total a Pagar -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-start border-soft-success border-3 rounded-3 hover-shadow glow-success">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                                    <i class="fas fa-money-bill-wave text-success"></i>
                                                </div>
                                                <h5 class="card-title fw-bold m-0">Total a Pagar</h5>
                                            </div>
                                            <div class="total-amount">
                                                <span class="fs-4 fw-bold text-success" id="totalPagar"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnDescargarRecibo">
                    <i class="fas fa-download me-2"></i>Descargar Recibo
                </button>
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
.glow-primary {
    box-shadow: 0 0 20px 5px rgba(13, 109, 253, 0.2);
}
.glow-success {
    box-shadow: 0 0 20px 5px rgba(25, 135, 84, 0.2);
}
.asignaciones-box, .deducciones-box {
    background-color: rgba(248, 249, 250, 0.5) !important;
}
.total-box {
    border: 1px solid rgba(25, 135, 84, 0.2);
}
html.dark-mode .asignaciones-box,
html.dark-mode .deducciones-box,
body.dark-mode .asignaciones-box,
body.dark-mode .deducciones-box {
    background-color: rgba(33, 37, 41, 0.5) !important;
}

/* Estilos para el modal de recibo */
.border-soft-danger {
    border-color: rgba(220, 53, 69, 0.68) !important;
}

.glow-danger {
    box-shadow: 0 0 20px 5px rgba(220, 53, 69, 0.2);
}

.total-amount {
    background: rgba(25, 135, 84, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 8px;
}

/* Ajustes para modo oscuro */
html.dark-mode .total-amount,
body.dark-mode .total-amount {
    background: rgba(25, 135, 84, 0.2);
}

/* Estilos para el modal con scroll */
.modal-dialog.modal-xl {
    max-height: 90vh;
    margin: 1rem auto;
}

.modal-content {
    height: 90vh;
    display: flex;
    flex-direction: column;
}

.modal-header {
    flex-shrink: 0;
}

.modal-body {
    flex-grow: 1;
    overflow-y: auto;
    min-height: 0;
    padding: 0;
}

.modal-footer {
    flex-shrink: 0;
}

/* Personalización del scrollbar del modal */
.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: transparent;
    margin: 5px 0;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Ajustes para modo oscuro */
html.dark-mode .modal-body::-webkit-scrollbar-thumb,
body.dark-mode .modal-body::-webkit-scrollbar-thumb {
    background: #4a4a4a;
}

html.dark-mode .modal-body::-webkit-scrollbar-thumb:hover,
body.dark-mode .modal-body::-webkit-scrollbar-thumb:hover {
    background: #5a5a5a;
}

/* Ajuste del padding interno del contenido */
.modal-body .card {
    margin: 1rem;
}
</style>

<script>
function cargarDatosRecibo(detailId) {
    // Mostrar un indicador de carga
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    document.body.appendChild(loadingOverlay);

    // Limpiar cualquier backdrop existente
    const existingBackdrops = document.querySelectorAll('.modal-backdrop');
    existingBackdrops.forEach(backdrop => backdrop.remove());

    // Realizar la petición AJAX usando la URL correcta de Yii2
    fetch('<?= \yii\helpers\Url::to(['get-recibo-data']) ?>?id=' + detailId)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error del servidor: ${response.status} ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.message || 'Error al procesar los datos del recibo');
            }

            // Actualizar la información en el modal
            document.getElementById('nombreEmpleado').textContent = data.empleado.nombre;
            document.getElementById('cedulaEmpleado').textContent = 'V-' + data.empleado.ci;
            document.getElementById('cargoEmpleado').textContent = data.cargo;
            
            document.getElementById('tipoNomina').textContent = data.nomina.nomina;
            document.getElementById('periodoNomina').textContent = data.periodo === 1 ? 'Primera Quincena' : 'Segunda Quincena';
            document.getElementById('mesAnioNomina').textContent = `${data.mes}/${data.anio}`;
            
            // Formatear montos
            const formatter = new Intl.NumberFormat('es-VE', {
                style: 'currency',
                currency: 'VES',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            
            document.getElementById('sueldoEmpleado').textContent = formatter.format(data.sueldo_quinc);
            document.getElementById('primaHijos').textContent = formatter.format(data.prima_hijos);
            document.getElementById('primaProfesional').textContent = formatter.format(data.prima_prof);
            document.getElementById('cestaTickets').textContent = formatter.format(data.cesta_tickets);
            
            document.getElementById('ivss').textContent = formatter.format(data.ivss);
            document.getElementById('pie').textContent = formatter.format(data.pie);
            document.getElementById('faov').textContent = formatter.format(data.faov);
            
            document.getElementById('totalPagar').textContent = formatter.format(data.montopagar);
            
            // Configurar el botón de descarga
            const btnDescargar = document.getElementById('btnDescargarRecibo');
            btnDescargar.onclick = () => {
                window.location.href = '<?= \yii\helpers\Url::to(['descargar-recibo']) ?>&detail_id=' + detailId;
            };

            // Mostrar el modal
            const modalElement = document.getElementById('reciboModal');
            const reciboModal = new bootstrap.Modal(modalElement);
            
            // Agregar evento para limpiar el backdrop cuando se cierre el modal
            modalElement.addEventListener('hidden.bs.modal', function () {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });

            reciboModal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del recibo: ' + error.message);
            // Cerrar el modal si está abierto
            const modalElement = document.getElementById('reciboModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        })
        .finally(() => {
            // Remover el indicador de carga
            document.body.removeChild(loadingOverlay);
        });
}

// Estilos para el overlay de carga
const style = document.createElement('style');
style.textContent = `
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-overlay::after {
    content: '';
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
`;
document.head.appendChild(style);
</script>
