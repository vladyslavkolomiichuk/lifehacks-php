<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = 'Comments Manager';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comment-index">
  <h1><?= Html::encode($this->title) ?></h1>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-hover table-dark'],
    'columns' => [
      'id',
      [
        'attribute' => 'text',
        'value' => function ($model) {
          return mb_strimwidth($model->text, 0, 50, '...');
        }
      ],
      [
        'attribute' => 'article_id',
        'value' => 'article.title',
        'label' => 'Article'
      ],
      [
        'attribute' => 'user_id',
        'value' => 'user.name',
        'label' => 'Author'
      ],
      'date',
      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {update} {delete}', // Додали {view}
        'buttons' => [
          'view' => function ($url, $model) {
            return Html::a('<i class="bi bi-eye-fill"></i>', $url, ['class' => 'btn btn-sm btn-info']);
          },
          'update' => function ($url, $model) {
            return Html::a('<i class="bi bi-pencil-fill"></i>', $url, ['class' => 'btn btn-sm btn-primary']);
          },
          'delete' => function ($url, $model) {
            return Html::a('<i class="bi bi-trash-fill"></i>', $url, ['class' => 'btn btn-sm btn-danger', 'data' => ['confirm' => 'Delete?', 'method' => 'post']]);
          },
        ],
      ],
    ],
  ]); ?>
</div>