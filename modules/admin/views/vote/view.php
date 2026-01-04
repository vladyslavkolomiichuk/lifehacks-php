<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = 'Vote #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Votes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vote-view">
  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
      'class' => 'btn btn-danger',
      'data' => ['confirm' => 'Are you sure?', 'method' => 'post'],
    ]) ?>
    <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-secondary']) ?>
  </p>

  <div class="card bg-dark border-secondary">
    <div class="card-body">
      <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          'id',
          [
            'attribute' => 'user_id',
            'value' => $model->user->name,
          ],
          [
            'attribute' => 'article_id',
            'value' => $model->article->title,
          ],
        ],
        'options' => ['class' => 'table table-striped table-bordered table-dark detail-view'],
      ]) ?>
    </div>
  </div>
</div>