<?php

use yii\bootstrap5\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Topic */

$this->title = 'Update Topic: ' . $model->name;
?>
<div class="topic-update">

  <h1 class="text-white mb-4">
    <?= Html::encode($this->title) ?>
  </h1>

  <?= $this->render('_form', [
    'model' => $model,
  ]) ?>
</div>