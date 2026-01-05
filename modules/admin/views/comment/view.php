<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = 'Comment #' . $model->id;
?>
<div class="comment-view">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-white m-0"><?= Html::encode($this->title) ?></h1>
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
            'value' => Html::a(
              $model->article->title,
              ['/admin/article/view', 'id' => $model->article_id],
              ['class' => 'text-decoration-none', 'style' => 'color: #03dac6; font-weight: bold;']
            ),
          ],
          'date',
        ],
        // Темна таблиця, клас detail-view для стилів шрифтів
        'options' => ['class' => 'table table-dark table-bordered detail-view mb-0'],
      ]) ?>
    </div>
  </div>

</div>