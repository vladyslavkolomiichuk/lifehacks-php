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

      <div class="social-share" style="margin-top: 15px; border-top: 1px solid #333; padding-top: 10px; font-size: 12px; color: #777;">
        <span class="pull-left">By <?= $article->user->name; ?> On <?= $article->date; ?></span>

        <span class="pull-right">
          <a href="#" class="btn-share"
            data-title="<?= \yii\helpers\Html::encode($article->title) ?>"
            data-url="<?= \yii\helpers\Url::to(['site/view', 'id' => $article->id], true) ?>"
            style="color: #ccc; margin-left: 10px; text-decoration: none;">
            <i class="glyphicon glyphicon-share"></i> Share
          </a>
        </span>

        <span style="margin-right: 15px; font-size: 16px; color: #777;">
          <i class="glyphicon glyphicon-eye-open"></i> <?= (int)$article->viewed ?>
        </span>
      </div>
    </div>
  </article>

  <div class="comments-area" style="background: #1e1e1e; padding: 20px; margin-top: 30px; border-radius: 5px; border: 1px solid #333;">
    <h3 style="color: #fff; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px;">
      Comments (<?= count($comments) ?>)
    </h3>

    <?php if (!Yii::$app->user->isGuest): ?>
      <div class="leave-comment" id="comment-form-container" style="margin-bottom: 30px;">

        <div id="reply-block" style="display:none; background: #2d2d2d; padding: 10px; border-left: 3px solid #03dac6; margin-bottom: 15px;">
          <span style="color: #ccc;">Reply to: <b id="reply-to-user" style="color: #fff;"></b></span>
          <button id="cancel-reply" class="btn btn-xs btn-danger pull-right" style="margin-top: -2px;">Cancel</button>
        </div>

        <?php $form = ActiveForm::begin([
          'action' => ['site/comment', 'id' => $article->id],
          'options' => ['class' => 'form-horizontal contact-form', 'role' => 'form']
        ]) ?>

        <div class="form-group">
          <div class="col-md-12">
            <?= $form->field($commentForm, 'comment')->textarea([
              'class' => 'form-control',
              'id' => 'comment-textarea',
              'rows' => 3,
              'placeholder' => 'Write your helpful tip here...',
              'style' => 'background:#2d2d2d; color:#fff; border:1px solid #444;'
            ])->label(false) ?>

            <?= $form->field($commentForm, 'parentId')->hiddenInput(['class' => 'parent-id-input'])->label(false) ?>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="background-color: #bb86fc; color: #000; border: none; font-weight: bold; margin-top: 10px;">Post Comment</button>
        <?php ActiveForm::end(); ?>
      </div>
    <?php else: ?>
      <div class="alert alert-warning" style="background: #333; border: 1px solid #555; color: #ccc;">
        Please <a href="<?= Url::to(['/auth/login']) ?>" style="color: #bb86fc;">Login</a> to leave a comment.
      </div>
    <?php endif; ?>

    <div class="comments-list">
      <?php
      // Фільтруємо тільки кореневі коментарі
      $rootComments = array_filter($comments, function ($c) {
        return $c->parent_id == null;
      });
      ?>

      <?php foreach ($rootComments as $comment): ?>
        <div class="media" style="margin-top: 20px; border-bottom: 1px solid #333; padding-bottom: 15px;">
          <div class="media-left">
            <img class="media-object img-circle" src="/uploads/<?= $comment->user->image ? $comment->user->image : 'default.jpg' ?>" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
          </div>

          <div class="media-body">
            <h4 class="media-heading" style="color: #03dac6; margin-top: 0;">
              <?= \yii\helpers\Html::encode($comment->user->name); ?>
              <small class="pull-right" style="color: #777; font-size: 12px;">
                <?= $comment->date; ?>
                <?php if ($comment->is_edited): ?><span style="font-style:italic;">(edited)</span><?php endif; ?>
              </small>
            </h4>

            <p style="color: #ccc;"><?= \yii\helpers\Html::encode($comment->text); ?></p>

            <div class="comment-actions" style="font-size: 12px; margin-top: 5px; margin-bottom: 10px;">

              <?php if (!Yii::$app->user->isGuest): ?>
                <a href="#" class="btn-reply" data-id="<?= $comment->id ?>" data-user="<?= \yii\helpers\Html::encode($comment->user->name) ?>" style="color: #bb86fc; text-decoration: none; margin-right: 15px;">
                  <i class="glyphicon glyphicon-share-alt"></i> Reply
                </a>
              <?php endif; ?>

              <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id == $comment->user_id): ?>
                <?= \yii\helpers\Html::a('<i class="glyphicon glyphicon-pencil"></i> Edit', ['site/update-comment', 'id' => $comment->id], [
                  'style' => 'color: #03dac6; text-decoration: none; margin-right: 10px;'
                ]) ?>

                <?= \yii\helpers\Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', ['site/delete-comment', 'id' => $comment->id], [
                  'style' => 'color: #cf6679; text-decoration: none;',
                  'data' => [
                    'confirm' => 'Are you sure you want to delete this comment? All replies will also be removed.',
                    'method' => 'post',
                  ],
                ]) ?>
              <?php endif; ?>
            </div>

            <?php foreach ($comment->children as $child): ?>
              <div class="media" style="margin-top: 15px; margin-left: 50px; padding-top: 15px; border-top: 1px solid #2d2d2d;">
                <div class="media-left">
                  <img class="media-object img-circle" src="/uploads/<?= $child->user->image ? $child->user->image : 'default.jpg' ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                </div>
                <div class="media-body">
                  <h5 class="media-heading" style="color: #fff; font-size: 14px; margin-top: 0;">
                    <?= \yii\helpers\Html::encode($child->user->name); ?>
                    <small class="pull-right" style="color: #777; font-size: 11px;">
                      <?= $child->date; ?>
                      <?php if ($child->is_edited): ?><span style="font-style:italic;">(edited)</span><?php endif; ?>
                    </small>
                  </h5>

                  <p style="color: #bbb; font-size: 13px;"><?= \yii\helpers\Html::encode($child->text); ?></p>

                  <div class="comment-actions" style="font-size: 11px;">
                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id == $child->user_id): ?>
                      <?= \yii\helpers\Html::a('<i class="glyphicon glyphicon-pencil"></i> Edit', ['site/update-comment', 'id' => $child->id], [
                        'style' => 'color: #03dac6; text-decoration: none; margin-right: 10px;'
                      ]) ?>

                      <?= \yii\helpers\Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', ['site/delete-comment', 'id' => $child->id], [
                        'style' => 'color: #cf6679; text-decoration: none;',
                        'data' => [
                          'confirm' => 'Delete this reply?',
                          'method' => 'post',
                        ],
                      ]) ?>
                    <?php endif; ?>
                  </div>

                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php
  $js = <<<JS
    // 1. Клік на кнопку Reply
    $('.btn-reply').on('click', function(e) {
        e.preventDefault();
        
        // Отримуємо ID коментаря та ім'я автора
        var commentId = $(this).data('id');
        var userName = $(this).data('user');
        
        // Записуємо ID в приховане поле форми
        $('.parent-id-input').val(commentId);
        
        // Показуємо блок "Reply to..."
        $('#reply-to-user').text(userName);
        $('#reply-block').slideDown();
        
        // Плавно скролимо до форми
        $('html, body').animate({
            scrollTop: $("#comment-form-container").offset().top - 100
        }, 500);
        
        // Ставимо фокус у текстове поле
        $('#comment-textarea').focus();
    });

    // 2. Клік на кнопку Cancel (Скасувати відповідь)
    $('#cancel-reply').on('click', function(e) {
        e.preventDefault();
        
        // Очищаємо приховане поле ID
        $('.parent-id-input').val('');
        
        // Ховаємо блок "Reply to..."
        $('#reply-block').slideUp();
    });
JS;

  $this->registerJs($js);
  ?>