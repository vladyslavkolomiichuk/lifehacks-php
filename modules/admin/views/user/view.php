<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
  <h1>User: <?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
      'class' => 'btn btn-danger',
      'data' => ['confirm' => 'Are you sure?', 'method' => 'post'],
    ]) ?>
    <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-secondary']) ?>
  </p>

  <div class="card bg-dark border-secondary">
    <div class="card-body">
      <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          'id',
          'name',
          'email:email',
          [
            'attribute' => 'isAdmin',
            'format' => 'raw',
            'value' => $model->isAdmin ? '<span class="badge bg-success">Admin</span>' : '<span class="badge bg-secondary">User</span>',
          ],
          [
            'attribute' => 'image',
            'format' => 'html',
            'value' => function ($model) {
              return $model->image ? Html::img('/uploads/' . $model->image, ['width' => 100, 'class' => 'rounded-circle']) : 'No Image';
            },
          ],
        ],
        'options' => ['class' => 'table table-striped table-bordered table-dark detail-view'],
      ]) ?>
    </div>
  </div>
</div>