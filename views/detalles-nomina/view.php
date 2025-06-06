<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\DetallesNomina $model */

$this->title = $model->detail_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Detalles Nominas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="detalles-nomina-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'detail_id' => $model->detail_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'detail_id' => $model->detail_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'detail_id',
            'nomina_id',
            'empleado_id',
            'cargo',
            'tipo_cargo',
            'sueldo_quinc',
            'prima_hijos',
            'prima_prof',
            'prima_antig',
            'ivss',
            'pie',
            'faov',
            'tesoreria_ss',
            'caja_ahorro',
            'aporte_suep',
            'cesta_tickets',
            'bono_vac',
            'total_a',
            'total_d',
            'montopagar',
            'mes',
            'anio',
            'periodo',
        ],
    ]) ?>

</div>
