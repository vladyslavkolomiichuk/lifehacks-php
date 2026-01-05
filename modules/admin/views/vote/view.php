<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = 'Vote #' . $model->id;
?>
<div class="vote-view">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-white m-0"><?= Html::encode($this->title) ?></h1>
    <div>
      <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-secondary fw-bold me-2']) ?>
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
          [
            'attribute' => 'user_id',
            'label' => 'User',
            'value' => $model->user->name,
          ],
          [
            'attribute' => 'article_id',
            'label' => 'Article',
            'format' => 'raw',
            // Посилання на перегляд статті
            'value' => Html::a(
              $model->article->title,
              ['/admin/article/view', 'id' => $model->article_id],
              ['class' => 'text-decoration-none', 'style' => 'color: #03dac6; font-weight: bold;']
            ),
          ],
        ],
        // Використовуємо table-dark та наш клас detail-view
        'options' => ['class' => 'table table-dark table-bordered detail-view mb-0'],
      ]) ?>
    </div>
  </div>

</div>