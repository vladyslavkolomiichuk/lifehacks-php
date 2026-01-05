<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Comment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-form card bg-dark border-secondary shadow-sm">
  <div class="card-body">
    <?php $form = ActiveForm::begin([
      'options' => ['class' => 'dark-form'] // Підключаємо наші глобальні стилі
    ]); ?>

    <?= $form->field($model, 'text')->textarea([
      'rows' => 6,
      'placeholder' => 'Edit the comment content here...'
    ]) ?>

    <div class="form-group mt-4 pt-3 border-top border-secondary">
      <?= Html::submitButton('<i class="bi bi-check-lg"></i> Save', [
        'class' => 'btn btn-success fw-bold px-4'
      ]) ?>
      <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>
  </div>
</div>