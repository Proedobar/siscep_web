<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Roles;

/** @var yii\web\View $this */
/** @var app\models\Users $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div style="margin-left: 15%; margin-right: 15%;">
    <div class="users-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'empleado_id')->hiddenInput()->label(false) ?>
        
        <?= $form->field($model, 'empleado_id')->textInput([
            'disabled' => true,
            'value' => $model->empleado ? $model->empleado->nombre : '',
            'placeholder' => Yii::t('app', 'Nombre del Empleado')
        ])->label(Yii::t('app', 'Nombre del Empleado')) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label('Correo electrónico:') ?>

        <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true, 'value' => ''])->label('Contraseña:') ?>

        <?= $form->field($model, 'rol_id')->dropDownList(
            ArrayHelper::map(Roles::find()->all(), 'id', 'descripcion'),
            ['prompt' => Yii::t('app', 'Seleccione un rol')]
        )->label('Rol:') ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Aplicar Cambios'), ['class' => 'btn btn-success w-100']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
