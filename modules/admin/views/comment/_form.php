<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Comment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-form card bg-dark border-secondary">
  <div class="card-body">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6, 'style' => 'background:#2d2d2d; color:#fff; border:1px solid #444;']) ?>

    <div class="form-group mt-3">
      <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
      <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
  </div>
</div>