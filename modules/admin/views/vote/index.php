<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = 'Votes Manager';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vote-index">
  <h1><?= Html::encode($this->title) ?></h1>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-hover table-dark'], // Темна таблиця
    'columns' => [
      // Явне задання колонки ID з фіксованою шириною
      [
        'attribute' => 'id',
        'headerOptions' => ['style' => 'width:80px;'],
      ],

      // Виводимо ім'я користувача замість user_id
      [
        'attribute' => 'user_id',
        'value' => 'user.name',
        'label' => 'User Name'
      ],

      // Виводимо назву статті замість article_id
      [
        'attribute' => 'article_id',
        'value' => 'article.title',
        'label' => 'Article Title'
      ],

      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {delete}', // Додали view
        'buttons' => [
          'view' => function ($url, $model) {
            return Html::a('<i class="bi bi-eye-fill"></i>', $url, ['class' => 'btn btn-sm btn-info']);
          },
          'delete' => function ($url, $model) {
            return Html::a('<i class="bi bi-trash-fill"></i>', $url, ['class' => 'btn btn-sm btn-danger', 'data' => ['confirm' => 'Delete?', 'method' => 'post']]);
          },
        ],
      ],
    ],
  ]); ?>
</div>