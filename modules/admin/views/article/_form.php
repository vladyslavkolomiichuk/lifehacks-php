<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Topic;
use app\models\User;
?>

<div class="article-form card bg-dark border-secondary">
  <div class="card-body">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['style' => 'background:#2d2d2d; color:#fff; border:1px solid #444;']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6, 'style' => 'background:#2d2d2d; color:#fff; border:1px solid #444;']) ?>

    <?= $form->field($model, 'topic_id')->dropdownList(
      ArrayHelper::map(Topic::find()->all(), 'id', 'name'),
      ['style' => 'background:#2d2d2d; color:#fff; border:1px solid #444;']
    ) ?>

    <?= $form->field($model, 'user_id')->dropdownList(
      ArrayHelper::map(User::find()->all(), 'id', 'name'),
      ['style' => 'background:#2d2d2d; color:#fff; border:1px solid #444;']
    )->label('Author') ?>

    <?= $form->field($model, 'image')->fileInput() ?>

    <?= $form->field($model, 'tag')->textInput(['style' => 'background:#2d2d2d; color:#fff; border:1px solid #444;']) ?>

    <div class="form-group mt-3">
      <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>