<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = 'Topics';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topic-index">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-white m-0"><?= Html::encode($this->title) ?></h1>
    <?= Html::a('Create Topic', ['create'], ['class' => 'btn btn-success fw-bold']) ?>
  </div>

  <div class="card bg-dark border-secondary shadow-sm">
    <div class="card-body p-0">
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n<div class='table-responsive'>{items}</div>\n{pager}",
        'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],
        'columns' => [
          [
            'attribute' => 'id',
            'headerOptions' => ['style' => 'width:60px; text-align:center;'],
            'contentOptions' => ['style' => 'text-align:center; color: #777;'],
          ],
          'name',

          [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'ACTIONS',
            'template' => '{view} {update} {delete}',
            'contentOptions' => ['class' => 'action-column'],
            'buttons' => [
              'view' => function ($url) {
                return Html::a('<i class="bi bi-eye-fill"></i>', $url, [
                  'class' => 'action-btn-custom btn-view-style',
                  'title' => 'View',
                ]);
              },
              'update' => function ($url) {
                return Html::a('<i class="bi bi-pencil-fill"></i>', $url, [
                  'class' => 'action-btn-custom btn-update-style',
                  'title' => 'Update',
                ]);
              },
              'delete' => function ($url) {
                return Html::a('<i class="bi bi-trash-fill"></i>', $url, [
                  'class' => 'action-btn-custom btn-delete-style',
                  'title' => 'Delete',
                  'data' => ['confirm' => 'Delete topic?', 'method' => 'post'],
                ]);
              },
            ],
          ],
        ],
        'pager' => [
          'class' => \yii\bootstrap5\LinkPager::class,
          'options' => ['class' => 'pagination justify-content-center mt-3'],
        ],
      ]); ?>
    </div>
  </div>
</div>