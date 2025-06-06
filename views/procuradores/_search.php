<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ProcuradoresSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="procuradores-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'resolucion') ?>

    <?= $form->field($model, 'fecha_resolucion') ?>

    <?= $form->field($model, 'gaceta') ?>

    <?php // echo $form->field($model, 'fecha_gaceta') ?>

    <?php // echo $form->field($model, 'activo') ?>

    <?php // echo $form->field($model, 'firma_base64') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
