<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\LogsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ip') ?>

    <?= $form->field($model, 'ubicacion') ?>

    <?= $form->field($model, 'fecha_hora') ?>

    <?= $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'action') ?>

    <?php // echo $form->field($model, 'status_released') ?>

    <?php // echo $form->field($model, 'computer') ?>

    <?php // echo $form->field($model, 'osver') ?>

    <?php // echo $form->field($model, 'useragent') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
