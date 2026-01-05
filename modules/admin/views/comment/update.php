<?php

use yii\bootstrap5\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Comment */

$this->title = 'Update Comment #' . $model->id;
?>
<div class="comment-update">

  <h1 class="text-white mb-4">
    <?= Html::encode($this->title) ?>
  </h1>

  <div class="alert alert-dark border-secondary mb-4 d-flex flex-wrap align-items-center bg-opacity-10" style="background-color: rgba(255,255,255,0.05);">
    <div class="me-5 mb-2 mb-md-0">
      <small class="text-uppercase" style="color: #777; font-size: 0.7rem; letter-spacing: 1px;">Author</small>
      <div class="text-white fw-bold">
        <i class="bi bi-person-circle me-1 text-info"></i> <?= Html::encode($model->user->name) ?>
      </div>
    </div>

    <div>
      <small class="text-uppercase" style="color: #777; font-size: 0.7rem; letter-spacing: 1px;">Article</small>
      <div class="text-white fw-bold">
        <i class="bi bi-file-text me-1 text-success"></i> <?= Html::encode($model->article->title) ?>
      </div>
    </div>
  </div>

  <?= $this->render('_form', [
    'model' => $model,
  ]) ?>
</div>