<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap5\ActiveForm */
/* @var $model app\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card" style="background-color: #1e1e1e; border: 1px solid #333;">
        <div class="card-header" style="border-bottom: 1px solid #333;">
          <h3 class="text-center" style="color: #03dac6;"><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="card-body">
          <p class="text-center" style="color: #ccc;">Please fill out the following fields to signup:</p>

          <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

          <?php
          $inputOptions = [
            'style' => 'background-color: #2d2d2d; border: 1px solid #444; color: #fff;',
            'class' => 'form-control'
          ];
          ?>

          <?= $form->field($model, 'name')->textInput($inputOptions)->label('Name', ['style' => 'color:#ccc']) ?>

          <?= $form->field($model, 'email')->textInput($inputOptions)->label('Email', ['style' => 'color:#ccc']) ?>

          <?= $form->field($model, 'password')->passwordInput($inputOptions)->label('Password', ['style' => 'color:#ccc']) ?>

          <div class="form-group text-center mt-4">
            <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'style' => 'background-color: #bb86fc; border: none; color: #000; font-weight: bold; width: 100%;']) ?>
          </div>

          <?php ActiveForm::end(); ?>
        </div>
        <div class="card-footer text-center" style="border-top: 1px solid #333;">
          <span style="color: #777;">Already have an account?</span>
          <?= Html::a('Login', ['site/login'], ['style' => 'color: #03dac6;']) ?>
        </div>
      </div>
    </div>
  </div>
</div>