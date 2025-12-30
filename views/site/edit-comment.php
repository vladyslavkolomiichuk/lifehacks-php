<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CommentForm */
/* @var $comment app\models\Comment */

$this->title = 'Edit Comment';
?>

<div class="site-edit-comment">
  <div class="row justify-content-center">
    <div class="col-md-6 col-md-offset-3">
      <div class="card" style="background-color: #1e1e1e; border: 1px solid #333; padding: 20px; border-radius: 5px;">
        <h3 style="color: #fff; margin-top: 0; border-bottom: 1px solid #333; padding-bottom: 15px;">Edit Comment</h3>

        <?php $form = ActiveForm::begin(); ?>

        <div class="form-group">
          <label style="color: #ccc;">Your Comment</label>
          <?= $form->field($model, 'comment')->textarea([
            'rows' => 5,
            'style' => 'background-color: #2d2d2d; border: 1px solid #444; color: #fff;'
          ])->label(false) ?>
        </div>

        <div class="form-group" style="margin-top: 20px;">
          <?= Html::submitButton('Save Changes', ['class' => 'btn btn-primary', 'style' => 'background-color: #bb86fc; border: none; color: #000; font-weight: bold;']) ?>

          <?= Html::a('Cancel', ['site/view', 'id' => $comment->article_id], ['class' => 'btn btn-default', 'style' => 'background: transparent; border: 1px solid #555; color: #ccc; margin-left: 10px;']) ?>
        </div>

        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>