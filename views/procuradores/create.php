<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Procuradores $model */

$this->title = Yii::t('app', 'Create Procuradores');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Procuradores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="procuradores-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
