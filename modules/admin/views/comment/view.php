<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = 'Comment #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comment-view">
  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
          'text:ntext',
          [
            'attribute' => 'user_id',
            'label' => 'Author',
            'value' => $model->user->name,
          ],
          [
            'attribute' => 'article_id',
            'label' => 'Article',
            'format' => 'raw',
            'value' => Html::a($model->article->title, ['/admin/article/view', 'id' => $model->article_id]),
          ],
          'date',
        ],
        'options' => ['class' => 'table table-striped table-bordered table-dark detail-view'],
      ]) ?>
    </div>
  </div>
</div>