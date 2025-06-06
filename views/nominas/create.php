<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Nominas $model */

$this->title = Yii::t('app', 'Cargar Nómina');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Nóminas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nominas-create">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
