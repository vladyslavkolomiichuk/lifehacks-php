<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = 'Users Manager';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
  <h1><?= Html::encode($this->title) ?></h1>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-hover table-dark'],
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
      'id',
      'name',
      'email',
      [
        'attribute' => 'isAdmin',
        'format' => 'raw',
        'value' => function ($model) {
          return $model->isAdmin
            ? '<span class="badge bg-success">Admin</span>'
            : '<span class="badge bg-secondary">User</span>';
        },
        'filter' => [0 => 'User', 1 => 'Admin'],
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update} {delete}',
        'buttons' => [
          'update' => function ($url, $model) {
            return Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn btn-sm btn-primary']);
          },
          'delete' => function ($url, $model) {
            return Html::a('<i class="bi bi-trash"></i>', $url, [
              'class' => 'btn btn-sm btn-danger',
              'data' => ['confirm' => 'Delete this user?', 'method' => 'post'],
            ]);
          },
        ],
      ],
    ],
  ]); ?>
</div>