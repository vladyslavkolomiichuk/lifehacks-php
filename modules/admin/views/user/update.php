<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Update User: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">
  <h1><?= Html::encode($this->title) ?></h1>
  <div class="card mt-3">
    <div class="card-body">
      <?php $form = ActiveForm::begin(); ?>

      <?= $form->field($model, 'name')->textInput() ?>
      <?= $form->field($model, 'email')->textInput() ?>

      <?= $form->field($model, 'isAdmin')->checkbox([
        'label' => 'Grant Administrator Privileges'
      ]) ?>

      <div class="form-group mt-3">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>