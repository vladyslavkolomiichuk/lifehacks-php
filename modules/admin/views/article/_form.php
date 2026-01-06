<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Topic;
use app\models\User;
?>

<div class="article-form card bg-dark border-secondary shadow-sm">
  <div class="card-body">
    <?php $form = ActiveForm::begin([
      'options' => ['enctype' => 'multipart/form-data', 'class' => 'dark-form']
    ]); ?>

    <div class="row">
      <div class="col-md-12 mb-3">
        <?= $form->field($model, 'title')->textInput(['placeholder' => 'Enter article title...']) ?>
      </div>

      <div class="col-md-12 mb-3">
        <?= $form->field($model, 'description')->textarea(['rows' => 6, 'placeholder' => 'Article content...']) ?>
      </div>

      <div class="col-md-6 mb-3">
        <?= $form->field($model, 'topic_id')->dropDownList(
          ArrayHelper::map(Topic::find()->all(), 'id', 'name'),
          ['prompt' => 'Select Topic...']
        ) ?>
      </div>

      <div class="col-md-6 mb-3">
        <?= $form->field($model, 'user_id')->dropDownList(
          ArrayHelper::map(User::find()->all(), 'id', 'name'),
          ['prompt' => 'Select Author...']
        )->label('Author') ?>
      </div>

      <div class="col-md-6 mb-3">
        <?= $form->field($model, 'image')->fileInput() ?>
      </div>

      <div class="col-md-6 mb-3">
        <?= $form->field($model, 'tag')->textInput(['placeholder' => 'e.g. php, yii2, coding']) ?>
      </div>
    </div>

    <div class="form-group mt-4 pt-3 border-top border-secondary">
      <?= Html::submitButton($model->isNewRecord ? '<i class="bi bi-plus-lg"></i> Create' : '<i class="bi bi-check-lg"></i> Save', [
        'class' => 'btn btn-success fw-bold px-4'
      ]) ?>
      <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>
  </div>
</div>