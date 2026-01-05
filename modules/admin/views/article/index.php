<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;
use app\models\Topic;
use app\models\User;
use yii\helpers\ArrayHelper;

$this->title = 'Articles Manager';
?>
<div class="article-index">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-white m-0"><?= Html::encode($this->title) ?></h1>
    <?= Html::a('Create Article', ['create'], ['class' => 'btn btn-success fw-bold']) ?>
  </div>

  <div class="card bg-dark border-secondary shadow-sm">
    <div class="card-body p-0">
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n<div class='table-responsive'>{items}</div>\n{pager}",
        // Наші темні стилі
        'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],
        'columns' => [
          // ID замість SerialColumn (часто корисніше в адмінці)
          [
            'attribute' => 'id',
            'headerOptions' => ['style' => 'width:60px; text-align:center;'],
            'contentOptions' => ['style' => 'text-align:center; color: #777;'],
          ],

          // Зображення з гарним оформленням
          [
            'attribute' => 'image',
            'label' => 'IMAGE',
            'format' => 'raw',
            'value' => function ($model) {
              return Html::img($model->getThumb(), [
                'style' => 'width: 50px; height: 35px; border-radius: 4px; object-fit: cover; border: 1px solid #444;'
              ]);
            },
            'filter' => false,
            'headerOptions' => ['style' => 'width:80px; text-align:center;'],
            'contentOptions' => ['style' => 'text-align:center;'],
          ],

          'title',

          // Категорія (Topic)
          [
            'attribute' => 'topic_id',
            'value' => 'topic.name',
            'label' => 'TOPIC',
            'filter' => ArrayHelper::map(Topic::find()->all(), 'id', 'name'),
          ],

          // Автор (User)
          [
            'attribute' => 'user_id',
            'value' => 'user.name',
            'label' => 'AUTHOR',
            'filter' => ArrayHelper::map(User::find()->all(), 'id', 'name'),
          ],

          // Дата
          [
            'attribute' => 'date',
            'label' => 'DATE',
            'headerOptions' => ['style' => 'width:120px;'],
          ],

          // Перегляди
          [
            'attribute' => 'viewed',
            'label' => 'VIEWS',
            'headerOptions' => ['style' => 'width:80px; text-align:center;'],
            'contentOptions' => ['style' => 'text-align:center;'],
          ],

          // Колонка дій
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
                  'data' => [
                    'confirm' => 'Delete article?',
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