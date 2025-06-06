<?php

use app\models\Logs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\LogsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Registros del Sistema';

// Obtener datos para las gráficas
$connection = Yii::$app->db;
$userActions = $connection->createCommand('
    SELECT action, COUNT(*) as count 
    FROM logs 
    GROUP BY action
')->queryAll();

$userLocations = $connection->createCommand('
    SELECT ubicacion, COUNT(*) as count 
    FROM logs 
    WHERE ubicacion IS NOT NULL 
    GROUP BY ubicacion
')->queryAll();

$dailyLogs = $connection->createCommand('
    SELECT DATE(fecha_hora) as fecha, COUNT(*) as count 
    FROM logs 
    GROUP BY DATE(fecha_hora) 
    ORDER BY fecha DESC 
    LIMIT 7
')->queryAll();

// Registrar los assets necesarios para las gráficas
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="logs-index">
    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Panel de Registros</h1>
        <p class="lead">Monitoreo y Análisis de Actividades del Sistema</p>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-clipboard-list text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Total Registros</h5>
                    </div>
                    <p class="card-text fs-5 text-muted"><?= $dataProvider->getTotalCount() ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-success border-3 rounded-3 hover-shadow glow-success">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-users text-success"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Usuarios Únicos</h5>
                    </div>
                    <p class="card-text fs-5 text-muted"><?= Logs::find()->select('user_id')->distinct()->count() ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-start border-soft-danger border-3 rounded-3 hover-shadow glow-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Ubicaciones</h5>
                    </div>
                    <p class="card-text fs-5 text-muted"><?= Logs::find()->select('ubicacion')->distinct()->count() ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100 border-start border-soft-warning border-3 rounded-3 hover-shadow glow-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-chart-pie text-warning"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Acciones de Usuario</h5>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="actionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100 border-start border-soft-purple border-3 rounded-3 hover-shadow glow-purple">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-purple bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-chart-bar text-purple"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Ubicaciones</h5>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="locationsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card h-100 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-chart-line text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold m-0">Actividad Diaria</h5>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="dailyChart"></canvas>
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

.border-soft-warning {
    border-color: rgba(255, 193, 7, 0.72) !important;
}

.border-soft-purple {
    border-color: rgba(111, 66, 193, 0.72) !important;
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

.glow-purple {
    box-shadow: 0 0 20px 5px rgba(111, 66, 193, 0.2) !important;
}

.text-purple {
    color: #6f42c1;
}

.bg-purple {
    background-color: #6f42c1;
}
</style>

<?php
$actionsData = json_encode($userActions);
$locationsData = json_encode($userLocations);
$dailyData = json_encode($dailyLogs);

$js = <<<JS
// Configuración común para todas las gráficas
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                boxWidth: 12,
                padding: 15,
                font: {
                    size: 12
                }
            }
        }
    }
};

// Gráfica de Acciones
new Chart(document.getElementById('actionsChart'), {
    type: 'pie',
    data: {
        labels: {$actionsData}.map(item => item.action),
        datasets: [{
            data: {$actionsData}.map(item => item.count),
            backgroundColor: [
                'rgba(255, 193, 7, 0.8)',
                'rgba(13, 109, 253, 0.8)',
                'rgba(25, 135, 84, 0.8)',
                'rgba(220, 53, 70, 0.8)',
                'rgba(111, 66, 193, 0.8)'
            ]
        }]
    },
    options: commonOptions
});

// Gráfica de Ubicaciones
new Chart(document.getElementById('locationsChart'), {
    type: 'bar',
    data: {
        labels: {$locationsData}.map(item => item.ubicacion),
        datasets: [{
            label: 'Número de registros',
            data: {$locationsData}.map(item => item.count),
            backgroundColor: 'rgba(111, 66, 193, 0.8)',
            borderColor: 'rgba(111, 66, 193, 1)',
            borderWidth: 1
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    font: {
                        size: 12
                    }
                }
            },
            x: {
                ticks: {
                    maxRotation: 45,
                    minRotation: 45,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// Gráfica de Actividad Diaria
new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: {$dailyData}.map(item => item.fecha),
        datasets: [{
            label: 'Registros por día',
            data: {$dailyData}.map(item => item.count),
            borderColor: 'rgba(13, 109, 253, 0.8)',
            backgroundColor: 'rgba(13, 109, 253, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    font: {
                        size: 12
                    }
                }
            },
            x: {
                ticks: {
                    maxRotation: 45,
                    minRotation: 45,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});
JS;

$this->registerJs($js);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
?>
