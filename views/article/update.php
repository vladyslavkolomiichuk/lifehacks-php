<?php

use yii\helpers\Html;

$this->title = 'Update Article: ' . $model->title;
?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card" style="background-color: #1e1e1e; border: 1px solid #333;">
      <div class="card-header" style="border-bottom: 1px solid #333;">
        <h3 style="color: #fff; margin: 0;">Update Tip</h3>
      </div>
      <div class="card-body">
        <?= $this->render('_article_form', [
          'model' => $model,
          'topics' => $topics,
        ]) ?>
      </div>
    </div>
  </div>
</div>