<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
?>
<div class="topic-view">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-white m-0">Topic: <?= Html::encode($this->title) ?></h1>
    <div>
      <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-secondary fw-bold me-2']) ?>
      <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary fw-bold me-2']) ?>
      <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger fw-bold',
        'data' => [
          'confirm' => 'Are you sure you want to delete this item?',
          'method' => 'post',
        ],
      ]) ?>
    </div>
  </div>

  <div class="card bg-dark border-secondary shadow-sm">
    <div class="card-body p-0">
      <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          'id',
          'name',
        ],
        'options' => ['class' => 'table table-dark table-bordered detail-view mb-0'],
      ]) ?>
    </div>
  </div>

</div>