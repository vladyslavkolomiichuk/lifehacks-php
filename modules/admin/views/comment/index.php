<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = 'Comments Manager';
?>
<div class="comment-index">

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
            'attribute' => 'text',
            'label' => 'COMMENT TEXT',
            'value' => function ($model) {
              return mb_strimwidth($model->text, 0, 50, '...');
            },
          ],

          [
            'attribute' => 'article_id',
            'value' => 'article.title',
            'label' => 'ARTICLE',
          ],

          [
            'attribute' => 'user_id',
            'value' => 'user.name',
            'label' => 'AUTHOR',
            'headerOptions' => ['style' => 'width: 15%;'],
          ],

          [
            'attribute' => 'date',
            'label' => 'DATE',
            'headerOptions' => ['style' => 'width: 150px;'],
          ],

          [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'ACTIONS',
            'template' => '{view} {update} {delete}',
            'contentOptions' => ['class' => 'action-column'],
            'buttons' => [
              'view' => function ($url, $model) {
                return Html::a('<i class="bi bi-eye-fill"></i>', $url, [
                  'class' => 'action-btn-custom btn-view-style',
                  'title' => 'View',
                ]);
              },
              'update' => function ($url, $model) {
                return Html::a('<i class="bi bi-pencil-fill"></i>', $url, [
                  'class' => 'action-btn-custom btn-update-style',
                  'title' => 'Update',
                ]);
              },
              'delete' => function ($url, $model) {
                return Html::a('<i class="bi bi-trash-fill"></i>', $url, [
                  'class' => 'action-btn-custom btn-delete-style',
                  'title' => 'Delete',
                  'data' => [
                    'confirm' => 'Delete comment?',
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