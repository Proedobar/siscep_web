<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\DetallesNominaSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="detalles-nomina-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'detail_id') ?>

    <?= $form->field($model, 'nomina_id') ?>

    <?= $form->field($model, 'empleado_id') ?>

    <?= $form->field($model, 'cargo') ?>

    <?= $form->field($model, 'tipo_cargo') ?>

    <?php // echo $form->field($model, 'sueldo_quinc') ?>

    <?php // echo $form->field($model, 'prima_hijos') ?>

    <?php // echo $form->field($model, 'prima_prof') ?>

    <?php // echo $form->field($model, 'prima_antig') ?>

    <?php // echo $form->field($model, 'ivss') ?>

    <?php // echo $form->field($model, 'pie') ?>

    <?php // echo $form->field($model, 'faov') ?>

    <?php // echo $form->field($model, 'tesoreria_ss') ?>

    <?php // echo $form->field($model, 'caja_ahorro') ?>

    <?php // echo $form->field($model, 'aporte_suep') ?>

    <?php // echo $form->field($model, 'cesta_tickets') ?>

    <?php // echo $form->field($model, 'bono_vac') ?>

    <?php // echo $form->field($model, 'total_a') ?>

    <?php // echo $form->field($model, 'total_d') ?>

    <?php // echo $form->field($model, 'montopagar') ?>

    <?php // echo $form->field($model, 'mes') ?>

    <?php // echo $form->field($model, 'anio') ?>

    <?php // echo $form->field($model, 'periodo') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
