<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Directores $model */

$this->title = Yii::t('app', 'Actualizar Director: {name}', [
    'name' => $model->nombre_director,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Directores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nombre_director, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Actualizar');
?>
<div class="directores-update">

    <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
