<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $model app\models\Article */
/* @var $topics app\models\Topic[] */

// Стилі для темних полів
$inputOptions = [
  'style' => 'background-color: #2d2d2d; border: 1px solid #444; color: #fff;',
  'class' => 'form-control'
];
?>

<div class="article-form">
  <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

  <?= $form->field($model, 'title')->textInput($inputOptions)->label('Title', ['style' => 'color:#ccc']) ?>

  <?= $form->field($model, 'topic_id')->dropDownList(
    ArrayHelper::map($topics, 'id', 'name'),
    ['style' => 'background-color: #2d2d2d; border: 1px solid #444; color: #fff;', 'class' => 'form-control']
  )->label('Category', ['style' => 'color:#ccc']) ?>

  <?= $form->field($model, 'description')->textarea(['rows' => 6] + $inputOptions)->label('Description / Text', ['style' => 'color:#ccc']) ?>

  <?= $form->field($model, 'tag')->textInput($inputOptions)->label('Tags (comma separated)', ['style' => 'color:#ccc']) ?>

  <?= $form->field($model, 'image')->fileInput(['style' => 'color: #ccc;'])->label('Image', ['style' => 'color:#ccc']) ?>

  <?php if (!$model->isNewRecord && $model->image): ?>
    <div style="margin-bottom: 20px;">
      <p style="color: #777;">Current Image:</p>
      <img src="/uploads/<?= $model->image ?>" style="width: 200px; border-radius: 5px;">
    </div>
  <?php endif; ?>

  <div class="form-group mt-4">
    <?= Html::submitButton(
      $model->isNewRecord ? 'Create Article' : 'Update Article',
      [
        'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
        'style' => 'width: 100%; font-weight: bold; border: none; color: #000; background-color: ' . ($model->isNewRecord ? '#03dac6' : '#bb86fc')
      ]
    ) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>