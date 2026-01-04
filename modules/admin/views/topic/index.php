<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = 'Topics';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topic-index">
  <h1><?= Html::encode($this->title) ?></h1>
  <p><?= Html::a('Create Topic', ['create'], ['class' => 'btn btn-success']) ?></p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-hover table-dark'],
    'columns' => [
      'id',
      'name',
      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {update} {delete}',
        'buttons' => [
          'view' => function ($url, $model) {
            return Html::a('<i class="bi bi-eye-fill"></i>', $url, ['class' => 'btn btn-sm btn-info', 'title' => 'View']);
          },
          'update' => function ($url, $model) {
            return Html::a('<i class="bi bi-pencil-fill"></i>', $url, ['class' => 'btn btn-sm btn-primary', 'title' => 'Update']);
          },
          'delete' => function ($url, $model) {
            return Html::a('<i class="bi bi-trash-fill"></i>', $url, [
              'class' => 'btn btn-sm btn-danger',
              'data' => ['confirm' => 'Delete?', 'method' => 'post'],
              'title' => 'Delete'
            ]);
          },
        ],
      ],
    ],
  ]); ?>
</div>