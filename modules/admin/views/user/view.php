<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
?>
<div class="user-view">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-white m-0">User: <?= Html::encode($this->title) ?></h1>
    <div>
      <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-secondary fw-bold me-2']) ?>
      <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary fw-bold me-2']) ?>
      <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger fw-bold',
        'data' => [
          'confirm' => 'Are you sure you want to delete this user?',
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
            'label' => 'Photo',
            'format' => 'raw',
            'value' => function ($model) {
              $img = $model->image ? '/uploads/' . $model->image : '/uploads/default.jpg';
              return Html::img($img, [
                'style' => 'width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #333;'
              ]);
            },
          ],
          'name',
          'email:email',
          [
            'attribute' => 'isAdmin',
            'format' => 'raw',
            'value' => $model->isAdmin
              ? '<span class="badge" style="background-color: #03dac6; color: #000;">Admin</span>'
              : '<span class="badge" style="background-color: #444; color: #ccc;">User</span>',
          ],
        ],
        'options' => ['class' => 'table table-dark table-bordered detail-view mb-0'],
      ]) ?>
    </div>
  </div>

</div>