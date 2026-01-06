<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = 'Votes Manager';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vote-index">

  <h1 class="text-white mb-4"><?= Html::encode($this->title) ?></h1>

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

          [
            'attribute' => 'user_id',
            'value' => 'user.name',
            'label' => 'USER NAME',
            'headerOptions' => ['style' => 'width: 25%;'],
          ],

          [
            'attribute' => 'article_id',
            'value' => 'article.title',
            'label' => 'ARTICLE TITLE',
          ],

          [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'ACTIONS',
            'template' => '{view} {delete}',
            'contentOptions' => ['class' => 'action-column'],
            'buttons' => [
              'view' => function ($url, $model) {
                return Html::a('<i class="bi bi-eye-fill"></i>', $url, [
                  'class' => 'action-btn-custom btn-view-style',
                  'title' => 'View',
                ]);
              },
              'delete' => function ($url, $model) {
                return Html::a('<i class="bi bi-trash-fill"></i>', $url, [
                  'class' => 'action-btn-custom btn-delete-style',
                  'title' => 'Delete',
                  'data' => [
                    'confirm' => 'Delete vote?',
                    'method' => 'post',
                  ],
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