<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Topics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topic-view">
  <h1>Topic: <?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
      'class' => 'btn btn-danger',
      'data' => [
        'confirm' => 'Are you sure you want to delete this item?',
        'method' => 'post',
      ],
    ]) ?>
    <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-secondary']) ?>
  </p>

  <div class="card bg-dark border-secondary">
    <div class="card-body">
      <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          'id',
          'name',
        ],
        'options' => ['class' => 'table table-striped table-bordered table-dark detail-view'],
      ]) ?>
    </div>
  </div>
</div>