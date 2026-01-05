<?php

use yii\bootstrap5\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Topic */

$this->title = 'Create Topic';
?>
<div class="topic-create">

  <h1 class="text-white mb-4">
    <?= Html::encode($this->title) ?>
  </h1>

  <?= $this->render('_form', [
    'model' => $model,
  ]) ?>
</div>