<?php

use yii\bootstrap5\Html;

$this->title = 'Create Article';
?>
<div class="article-create">
  <h1 class="text-white mb-4"><?= Html::encode($this->title) ?></h1>
  <?= $this->render('_form', ['model' => $model]) ?>
</div>