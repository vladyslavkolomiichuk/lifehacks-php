<?php

use yii\bootstrap5\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Article */

$this->title = 'Update Article: ' . $model->title;
?>
<div class="article-update">
  <h1 class="text-white mb-4"><?= Html::encode($this->title) ?></h1>
  <?= $this->render('_form', ['model' => $model]) ?>
</div>