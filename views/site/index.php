<?php

/** @var yii\web\View $this */
use yii\helpers\Html;
use app\models\Users;
use app\models\DetallesNomina;

$this->title = 'SISCEP';

// Obtener usuario actual
$user = Yii::$app->user->identity;
$empleado = null;
$detalle = null;
$cargo = '';
$ci = '';
$fecha_ingreso = '';
$tiempo_servicio = '';

if (!Yii::$app->user->isGuest && $user) {
    // Obtener el empleado relacionado con el usuario
    $empleado = $user->empleado;
    
    if ($empleado) {
        // Determinar periodo actual (1 para primera quincena, 2 para segunda)
        $mes_actual = (int)date('m');
        $anio_actual = (int)date('Y');
        $dia_actual = (int)date('d');
        $periodo = ($dia_actual <= 15) ? 1 : 2;
        
        // Obtener el detalle de nómina más reciente
        $detalle = DetallesNomina::find()
            ->where(['empleado_id' => $empleado->empleado_id])
            ->andWhere(['mes' => $mes_actual])
            ->andWhere(['anio' => $anio_actual])
            ->andWhere(['periodo' => $periodo])
            ->one();
        
        // Si no hay detalle para el periodo actual, buscar el más reciente
        if (!$detalle) {
            $detalle = DetallesNomina::find()
                ->where(['empleado_id' => $empleado->empleado_id])
                ->orderBy(['anio' => SORT_DESC, 'mes' => SORT_DESC, 'periodo' => SORT_DESC])
                ->one();
        }
        
        // Obtener datos del empleado
        $ci = $empleado->ci;
        $fecha_ingreso = $empleado->fecha_ingreso;
        
        // Calcular tiempo de servicio
        if ($fecha_ingreso) {
            $fecha_inicio = new \DateTime($fecha_ingreso);
            $fecha_actual = new \DateTime();
            $diferencia = $fecha_actual->diff($fecha_inicio);
            $tiempo_servicio = $diferencia->y . ' años, ' . $diferencia->m . ' meses, ' . $diferencia->d . ' días';
        }
        
        // Obtener cargo del detalle de nómina
        if ($detalle) {
            $cargo = $detalle->cargo;
        }
    }
}
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">¡Bienvenido de nuevo!</h1>
        <?php if ($empleado): ?>
        <p class="lead"><?= Html::encode($empleado->nombre) ?></p>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-briefcase text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Cargo</h5>
                    </div>
                    <p class="card-text fs-5 text-muted"><?= Html::encode($cargo ?: 'No disponible') ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-success border-3 rounded-3 hover-shadow glow-success">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-id-card text-success"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Documento de Identidad</h5>
                    </div>
                    <p class="card-text fs-5 text-muted"><?= Html::encode($ci ?: 'No disponible') ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-danger border-3 rounded-3 hover-shadow glow-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-calendar-alt text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Fecha de Ingreso</h5>
                    </div>
                    <p class="card-text fs-5 text-muted"><?= Html::encode($fecha_ingreso ? date('d/m/Y', strtotime($fecha_ingreso)) : 'No disponible') ?></p>
                    <?php if ($tiempo_servicio): ?>
                    <div class="mt-2 badge border tiempo-servicio"><?= Html::encode($tiempo_servicio) ?></div>
                    <?php endif; ?>
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
.glow-primary {
    box-shadow: 0 0 20px 5px rgba(13, 109, 253, 0.2) !important;
}
.glow-success {
    box-shadow: 0 0 20px 5px rgba(25, 135, 84, 0.2) !important;
}
.glow-danger {
    box-shadow: 0 0 20px 5px rgba(220, 53, 70, 0.2) !important;
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
</style>
