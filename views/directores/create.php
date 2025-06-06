<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Directores $model */

$this->title = Yii::t('app', 'Añadir un Director');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Directores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="directores-create">

    <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
