<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Edit Profile';
?>

<div class="profile-update">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card" style="background-color: #1e1e1e; border: 1px solid #333;">
        <div class="card-header" style="border-bottom: 1px solid #333;">
          <h3 style="color: #fff; margin: 0;">Update Profile</h3>
        </div>
        <div class="card-body">

          <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

          <?php
          $inputOptions = [
            'style' => 'background-color: #2d2d2d; border: 1px solid #444; color: #fff;',
            'class' => 'form-control'
          ];
          ?>

          <?= $form->field($user, 'name')->textInput($inputOptions)->label('Full Name', ['style' => 'color:#ccc']) ?>

          <?= $form->field($user, 'login')->textInput(['readonly' => true] + $inputOptions)->label('Email (Login cannot be changed)', ['style' => 'color:#ccc']) ?>

          <?= $form->field($user, 'password')->passwordInput(['placeholder' => 'Leave blank to keep current password'] + $inputOptions)->label('New Password', ['style' => 'color:#ccc']) ?>

          <?= $form->field($user, 'image')->fileInput(['style' => 'color: #ccc;'])->label('Avatar', ['style' => 'color:#ccc']) ?>

          <?php if ($user->image): ?>
            <div style="margin-bottom: 20px;">
              <p style="color: #777;">Current Avatar:</p>
              <img src="/uploads/<?= $user->image ?>" style="width: 100px; border-radius: 5px;">
            </div>
          <?php endif; ?>

          <div class="form-group mt-4">
            <?= Html::submitButton('Save Changes', ['class' => 'btn btn-success', 'style' => 'width: 100%; font-weight: bold; background-color: #03dac6; border: none; color: #000;']) ?>
            <div style="margin-top: 10px; text-align: center;">
              <?= Html::a('Cancel', ['index'], ['style' => 'color: #ccc;']) ?>
            </div>
          </div>

          <?php ActiveForm::end(); ?>

        </div>
      </div>
    </div>
  </div>
</div>