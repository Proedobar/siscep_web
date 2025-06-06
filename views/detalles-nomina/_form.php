<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\DetallesNomina $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="detalles-nomina-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nomina_id')->textInput() ?>

    <?= $form->field($model, 'empleado_id')->textInput() ?>

    <?= $form->field($model, 'cargo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tipo_cargo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sueldo_quinc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'prima_hijos')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'prima_prof')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'prima_antig')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ivss')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pie')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'faov')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tesoreria_ss')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'caja_ahorro')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'aporte_suep')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cesta_tickets')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bono_vac')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'total_a')->textInput() ?>

    <?= $form->field($model, 'total_d')->textInput() ?>

    <?= $form->field($model, 'montopagar')->textInput() ?>

    <?= $form->field($model, 'mes')->textInput() ?>

    <?= $form->field($model, 'anio')->textInput() ?>

    <?= $form->field($model, 'periodo')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
