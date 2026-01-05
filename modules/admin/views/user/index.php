<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = 'Users Manager';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

  <h1 class="text-white mb-4"><?= Html::encode($this->title) ?></h1>

  <div class="card bg-dark border-secondary shadow-sm">
    <div class="card-body p-0">
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n<div class='table-responsive'>{items}</div>\n{pager}",
        // Важливо: table-striped і table-hover тепер мають наші темні стилі
        'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],
        'columns' => [
          [
            'attribute' => 'id',
            'headerOptions' => ['style' => 'width:60px; text-align:center;'],
            'contentOptions' => ['style' => 'text-align:center; color: #777;'],
          ],
          [
            'attribute' => 'image',
            'label' => 'PHOTO',
            'format' => 'raw',
            'value' => function ($model) {
              // Використовуємо наш новий метод getThumb()
              return Html::img($model->getThumb(), [
                'style' => 'width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #333;'
              ]);
            },
            'filter' => false,
            'headerOptions' => ['style' => 'width:80px; text-align:center;'],
            'contentOptions' => ['style' => 'text-align:center;'],
          ],
          'name',
          'email:email',
          [
            'attribute' => 'isAdmin',
            'format' => 'raw',
            'filter' => [0 => 'User', 1 => 'Admin'],
            'value' => function ($model) {
              if ($model->isAdmin) {
                return '<span class="badge" style="background-color: #03dac6; color: #000;">Admin</span>';
              } else {
                return '<span class="badge" style="background-color: #444; color: #ccc;">User</span>';
              }
            },
            'headerOptions' => ['style' => 'width: 120px; text-align:center;'],
            'contentOptions' => ['style' => 'text-align:center;'],
          ],

          // ACTION COLUMN: Іконки залишили, додали лише класи стилів
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
                  'data' => ['confirm' => 'Delete user?', 'method' => 'post'],
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