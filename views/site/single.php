<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = $article->title;
?>

<div class="col-md-8 col-md-offset-2">
  <article class="post">
    <div class="post-thumb">
      <img src="<?= $article->getImage(); ?>" alt="<?= $article->title ?>" style="width:100%">
    </div>

    <div class="post-content">
      <header class="entry-header text-center">
        <h6>Category: <?= $article->topic->name; ?></h6>
        <h1 class="entry-title"><?= $article->title ?></h1>
      </header>

      <div class="entry-content">
        <?= nl2br($article->description); ?>
      </div>

      <div class="decoration" style="margin-top: 20px;">
        <?php foreach (explode(',', $article->tag) as $tag): ?>
          <?php $tag = trim($tag); // Прибираємо зайві пробіли 
          ?>
          <?php if (!empty($tag)): ?>
            <a href="<?= Url::to(['site/search', 'q' => $tag]) ?>"
              class="btn btn-default btn-xs"
              style="background:#333; color:#ccc; border:none; margin-right: 5px; margin-bottom: 5px;">
              #<?= Html::encode($tag) ?>
            </a>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div class="social-share" style="margin-top: 20px; border-top: 1px solid #333; padding-top: 15px;">
        <span class="social-share-title pull-left">By <?= $article->user->name; ?> On <?= $article->date; ?></span>
        <ul class="text-center pull-right list-inline">
          <li><a class="s-facebook" href="#"><i class="fa fa-facebook"></i> Share</a></li>
          <li><a class="s-twitter" href="#"><i class="fa fa-twitter"></i> Tweet</a></li>
        </ul>
      </div>
    </div>
  </article>

  <div class="comments-area" style="background: #1e1e1e; padding: 20px; margin-top: 30px; border-radius: 10px;">
    <h3 style="color: #fff; margin-bottom: 20px;">Comments (<?= count($comments) ?>)</h3>

    <?php if (!Yii::$app->user->isGuest): ?>
      <div class="leave-comment">
        <?php $form = ActiveForm::begin([
          'action' => ['site/comment', 'id' => $article->id],
          'options' => ['class' => 'form-horizontal contact-form', 'role' => 'form']
        ]) ?>

        <div class="form-group">
          <div class="col-md-12">
            <?= $form->field($commentForm, 'comment')->textarea(['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Write your helpful tip here...', 'style' => 'background:#333; color:#fff; border:none;'])->label(false) ?>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="background-color: #bb86fc; color: #000; border: none; font-weight: bold;">Post Comment</button>
        <?php ActiveForm::end(); ?>
      </div>
    <?php else: ?>
      <div class="alert alert-warning" style="background: #333; border: 1px solid #555; color: #e0e0e0;">
        Please <a href="<?= Url::to(['/auth/login']) ?>" style="color: #bb86fc;">Login</a> to leave a comment.
      </div>
    <?php endif; ?>

    <?php foreach ($comments as $comment): ?>
      <div class="comment-box" style="margin-top: 20px; border-bottom: 1px solid #333; padding-bottom: 15px;">
        <div class="media">
          <div class="media-left">
            <img class="media-object img-circle" src="/uploads/<?= $comment->user->image ? $comment->user->image : 'default.jpg' ?>" alt="" style="width: 50px; height: 50px;">
          </div>
          <div class="media-body">
            <h4 class="media-heading" style="color: #03dac6;"><?= $comment->user->name; ?>
              <small class="pull-right" style="color: #777;"><?= $comment->date; ?></small>
            </h4>
            <p style="color: #ccc;"><?= $comment->text; ?></p>

            <a href="#" class="btn btn-xs btn-default pull-right" style="background: transparent; border: 1px solid #555; color: #777;">Reply</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>