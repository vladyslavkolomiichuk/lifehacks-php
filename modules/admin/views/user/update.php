<?php

use yii\bootstrap5\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Update User: ' . $model->name;
?>
<div class="user-update">
  <h1 class="text-white mb-4">
    <?= Html::encode($this->title) ?>
  </h1>

  <?= $this->render('_form', [
    'model' => $model,
  ]) ?>
</div>