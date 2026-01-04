<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;
use app\models\Topic;
use app\models\User;
use yii\helpers\ArrayHelper;

$this->title = 'Articles Manager';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">
  <h1><?= Html::encode($this->title) ?></h1>
  <p><?= Html::a('Create Article', ['create'], ['class' => 'btn btn-success']) ?></p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-hover table-dark'],
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
      [
        'attribute' => 'image',
        'format' => 'html',
        'value' => function ($model) {
          return Html::img($model->getImage(), ['width' => 50]);
        }
      ],
      'title',
      [
        'attribute' => 'topic_id',
        'value' => 'topic.name',
        'filter' => ArrayHelper::map(Topic::find()->all(), 'id', 'name'),
      ],
      [
        'attribute' => 'user_id',
        'value' => 'user.name',
        'filter' => ArrayHelper::map(User::find()->all(), 'id', 'name'),
      ],
      'date',
      'viewed',
      ['class' => 'yii\grid\ActionColumn'],
    ],
  ]); ?>
</div>