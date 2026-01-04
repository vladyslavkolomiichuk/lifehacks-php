<?php

use yii\helpers\Html;

$this->title = 'Create Topic';
$this->params['breadcrumbs'][] = ['label' => 'Topics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topic-create">
  <h1><?= Html::encode($this->title) ?></h1>
  <?= $this->render('_form', ['model' => $model]) ?>
</div>