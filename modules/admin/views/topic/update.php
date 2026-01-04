<?php

use yii\helpers\Html;

$this->title = 'Update Topic: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Topics', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="topic-update">
  <h1><?= Html::encode($this->title) ?></h1>
  <?= $this->render('_form', ['model' => $model]) ?>
</div>