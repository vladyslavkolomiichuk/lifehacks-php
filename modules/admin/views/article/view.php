<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
?>
<div class="article-view">

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
          [
            'attribute' => 'image',
            'format' => 'raw',
            'value' => function ($model) {
              return Html::img($model->getImage(), [
                'style' => 'width: 200px; border-radius: 8px; border: 1px solid #444; padding: 2px;'
              ]);
            }
          ],
          'title',
          'description:ntext',
          'date',
          [
            'attribute' => 'topic_id',
            'label' => 'Topic',
            'value' => function ($model) {
              return $model->topic->name;
            }
          ],
          [
            'attribute' => 'user_id',
            'label' => 'Author',
            'value' => function ($model) {
              return $model->user->name;
            }
          ],
          'viewed',
          'upvotes',
        ],
        'options' => ['class' => 'table table-dark table-bordered detail-view mb-0'],
      ]) ?>
    </div>
  </div>

</div>