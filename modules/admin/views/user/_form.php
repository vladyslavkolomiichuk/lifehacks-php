<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form card bg-dark border-secondary shadow-sm">
  <div class="card-body">
    <?php $form = ActiveForm::begin([
      'options' => ['class' => 'dark-form', 'enctype' => 'multipart/form-data']
    ]); ?>

    <div class="row">
      <div class="col-md-6 mb-3">
        <?= $form->field($model, 'name')->textInput(['placeholder' => 'John Doe']) ?>
      </div>
      <div class="col-md-6 mb-3">
        <?= $form->field($model, 'email')->textInput(['placeholder' => 'example@mail.com']) ?>
      </div>

      <div class="col-md-6 mb-3">
        <?= $form->field($model, 'password')->passwordInput([
          'placeholder' => $model->isNewRecord ? 'Enter password' : 'Leave blank to keep current password',
          'value' => '' // Щоб не підставляло хеш при редагуванні
        ]) ?>
      </div>
      <div class="col-md-6 mb-3">
        <?= $form->field($model, 'isAdmin')->dropDownList(
          [0 => 'User', 1 => 'Admin'],
          ['prompt' => 'Select Role...']
        )->label('Role') ?>
      </div>

      <div class="col-md-12 mb-3">
        <?= $form->field($model, 'image')->fileInput() ?>
      </div>
    </div>

    <div class="form-group mt-4 pt-3 border-top border-secondary">
      <?= Html::submitButton($model->isNewRecord ? '<i class="bi bi-person-plus"></i> Create' : '<i class="bi bi-check-lg"></i> Save', [
        'class' => 'btn btn-success fw-bold px-4'
      ]) ?>
      <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>
  </div>
</div>