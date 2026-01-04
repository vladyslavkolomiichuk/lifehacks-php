<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
?>
<div class="topic-form card bg-dark border-secondary">
  <div class="card-body">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['style' => 'background:#2d2d2d; color:#fff; border:1px solid #444;']) ?>
    <div class="form-group mt-3">
      <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>