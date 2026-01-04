<?php

use yii\bootstrap5\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Comment */

$this->title = 'Update Comment #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="comment-update">
  <h1><?= Html::encode($this->title) ?></h1>

  <div class="alert alert-info bg-dark border-info text-white">
    <strong>Author:</strong> <?= Html::encode($model->user->name) ?><br>
    <strong>Article:</strong> <?= Html::encode($model->article->title) ?>
  </div>

  <?= $this->render('_form', [
    'model' => $model,
  ]) ?>
</div>