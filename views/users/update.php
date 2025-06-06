<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Users $model */

$this->title = Yii::t('app', 'Actualizando Usuario: {name}', [
    'name' => $model->empleado->nombre,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Usuarios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->empleado->nombre, 'url' => ['view', 'user_id' => $model->user_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Actualizar');
?>
<div class="users-update">

    <h1 class="text-center">Actualizar Usuario</h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
