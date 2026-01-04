<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-view">
  <h1><?= Html::encode($this->title) ?></h1>
  <p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
      'class' => 'btn btn-danger',
      'data' => ['confirm' => 'Are you sure?', 'method' => 'post'],
    ]) ?>
  </p>

  <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
      'id',
      'title',
      'description:ntext',
      'date',
      [
        'attribute' => 'topic_id',
        'value' => $model->topic->name,
      ],
      [
        'attribute' => 'user_id',
        'value' => $model->user->name,
      ],
      'viewed',
      'upvotes',
    ],
    'options' => ['class' => 'table table-striped table-bordered table-dark detail-view'],
  ]) ?>
</div>