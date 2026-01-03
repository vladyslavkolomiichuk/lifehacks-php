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
          <?php $tag = trim($tag); ?>
          <?php if (!empty($tag)): ?>
            <a href="<?= Url::to(['article/search', 'q' => $tag]) ?>"
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
            data-url="<?= \yii\helpers\Url::to(['article/view', 'id' => $article->id], true) ?>"
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
      Comments (<span id="comment-count"><?= count($comments) ?></span>)
    </h3>

    <?php if (!Yii::$app->user->isGuest): ?>
      <div class="leave-comment" id="comment-form-container" style="margin-bottom: 30px;">
        <div id="reply-block" style="display:none; background: #2d2d2d; padding: 10px; border-left: 3px solid #03dac6; margin-bottom: 15px;">
          <span style="color: #ccc;">Reply to: <b id="reply-to-user" style="color: #fff;"></b></span>
          <button id="cancel-reply" class="btn btn-xs btn-danger pull-right" style="margin-top: -2px;">Cancel</button>
        </div>

        <?php $form = ActiveForm::begin([
          'action' => ['comment/create', 'id' => $article->id],
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
      $rootComments = array_filter($comments, function ($c) {
        return $c->parent_id == null;
      });
      ?>

      <?php foreach ($rootComments as $comment): ?>
        <div class="media comment-item" data-id="<?= $comment->id ?>" style="margin-top: 20px; border-bottom: 1px solid #333; padding-bottom: 15px;">
          <div class="media-left">
            <img class="media-object img-circle" src="/uploads/<?= $comment->user->image ? $comment->user->image : 'default.jpg' ?>" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
          </div>

          <div class="media-body">
            <h4 class="media-heading" style="color: #03dac6; margin-top: 0;">
              <?= \yii\helpers\Html::encode($comment->user->name); ?>
              <small class="pull-right" style="color: #777; font-size: 12px;">
                <?= $comment->date; ?>
                <span class="edited-label" style="font-style:italic; display: <?= $comment->is_edited ? 'inline' : 'none' ?>">(edited)</span>
              </small>
            </h4>

            <div class="comment-content" style="color: #ccc;">
              <p><?= \yii\helpers\Html::encode($comment->text); ?></p>
            </div>

            <div class="comment-edit-box" style="display: none; margin-top: 10px;">
              <textarea class="form-control edit-textarea" rows="3" style="background:#2d2d2d; color:#fff; border:1px solid #444; margin-bottom: 5px;"></textarea>

              <button class="btn btn-xs btn-success save-edit"
                data-id="<?= $comment->id ?>"
                data-url="<?= Url::to(['comment/update', 'id' => $comment->id]) ?>">Save</button>

              <button class="btn btn-xs btn-default cancel-edit">Cancel</button>
            </div>

            <div class="comment-actions" style="font-size: 12px; margin-top: 5px; margin-bottom: 10px;">
              <?php if (!Yii::$app->user->isGuest): ?>
                <a href="#" class="btn-reply" data-id="<?= $comment->id ?>" data-user="<?= \yii\helpers\Html::encode($comment->user->name) ?>" style="color: #bb86fc; text-decoration: none; margin-right: 15px;">
                  <i class="glyphicon glyphicon-share-alt"></i> Reply
                </a>
              <?php endif; ?>

              <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id == $comment->user_id): ?>
                <a href="#" class="btn-edit-inline" style="color: #03dac6; text-decoration: none; margin-right: 10px;">
                  <i class="glyphicon glyphicon-pencil"></i> Edit
                </a>

                <a href="#" class="btn-delete-inline"
                  data-id="<?= $comment->id ?>"
                  data-url="<?= Url::to(['comment/delete', 'id' => $comment->id]) ?>"
                  style="color: #cf6679; text-decoration: none;">
                  <i class="glyphicon glyphicon-trash"></i> Delete
                </a>
              <?php endif; ?>
            </div>

            <?php foreach ($comment->children as $child): ?>
              <div class="media comment-item" data-id="<?= $child->id ?>" style="margin-top: 15px; margin-left: 50px; padding-top: 15px; border-top: 1px solid #2d2d2d;">
                <div class="media-left">
                  <img class="media-object img-circle" src="/uploads/<?= $child->user->image ? $child->user->image : 'default.jpg' ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                </div>
                <div class="media-body">
                  <h5 class="media-heading" style="color: #fff; font-size: 14px; margin-top: 0;">
                    <?= \yii\helpers\Html::encode($child->user->name); ?>
                    <small class="pull-right" style="color: #777; font-size: 11px;">
                      <?= $child->date; ?>
                      <span class="edited-label" style="font-style:italic; display: <?= $child->is_edited ? 'inline' : 'none' ?>">(edited)</span>
                    </small>
                  </h5>

                  <div class="comment-content" style="color: #bbb; font-size: 13px;">
                    <p><?= \yii\helpers\Html::encode($child->text); ?></p>
                  </div>

                  <div class="comment-edit-box" style="display: none; margin-top: 10px;">
                    <textarea class="form-control edit-textarea" rows="2" style="background:#2d2d2d; color:#fff; border:1px solid #444; margin-bottom: 5px;"></textarea>

                    <button class="btn btn-xs btn-success save-edit"
                      data-id="<?= $child->id ?>"
                      data-url="<?= Url::to(['comment/update', 'id' => $child->id]) ?>">Save</button>

                    <button class="btn btn-xs btn-default cancel-edit">Cancel</button>
                  </div>

                  <div class="comment-actions" style="font-size: 11px;">
                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id == $child->user_id): ?>
                      <a href="#" class="btn-edit-inline" style="color: #03dac6; text-decoration: none; margin-right: 10px;">
                        <i class="glyphicon glyphicon-pencil"></i> Edit
                      </a>

                      <a href="#" class="btn-delete-inline"
                        data-id="<?= $child->id ?>"
                        data-url="<?= Url::to(['comment/delete', 'id' => $child->id]) ?>"
                        style="color: #cf6679; text-decoration: none;">
                        <i class="glyphicon glyphicon-trash"></i> Delete
                      </a>
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
</div>

<?php
// === JAVASCRIPT ДЛЯ AJAX ===
$js = <<<JS
    // 1. Reply logic
    $('.btn-reply').on('click', function(e) {
        e.preventDefault();
        var commentId = $(this).data('id');
        var userName = $(this).data('user');
        $('.parent-id-input').val(commentId);
        $('#reply-to-user').text(userName);
        $('#reply-block').slideDown();
        $('html, body').animate({ scrollTop: $("#comment-form-container").offset().top - 100 }, 500);
        $('#comment-textarea').focus();
    });

    $('#cancel-reply').on('click', function(e) {
        e.preventDefault();
        $('.parent-id-input').val('');
        $('#reply-block').slideUp();
    });

    // 2. INLINE EDIT (Редагування)
    $(document).on('click', '.btn-edit-inline', function(e) {
        e.preventDefault();
        var container = $(this).closest('.media-body');
        
        // ВИПРАВЛЕННЯ: Використовуємо .children(), щоб не чіпати вкладені коментарі
        var contentBox = container.children('.comment-content');
        var editBox = container.children('.comment-edit-box');
        var textarea = editBox.find('.edit-textarea');
        
        var currentText = contentBox.text().trim();
        textarea.val(currentText);
        
        contentBox.hide();
        editBox.show();
        textarea.focus();
    });

    // 3. CANCEL EDIT (Скасувати)
    $(document).on('click', '.cancel-edit', function(e) {
        e.preventDefault();
        var container = $(this).closest('.media-body');
        
        // ВИПРАВЛЕННЯ: Тільки прямі діти
        container.children('.comment-edit-box').hide();
        container.children('.comment-content').show();
    });

    // 4. SAVE EDIT (Зберегти AJAX)
    $(document).on('click', '.save-edit', function(e) {
        e.preventDefault();
        var btn = $(this);
        var commentId = btn.data('id');
        var container = btn.closest('.media-body');
        var textarea = container.find('.edit-textarea'); // Тут find ок, бо ми шукаємо всередині форми
        var newText = textarea.val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        
        var url = btn.data('url');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                'CommentForm[comment]': newText,
                '_csrf': csrfToken
            },
            success: function(response) {
                if(response.success) {
                    // Оновлюємо текст (шукаємо p тільки в прямому блоці контенту)
                    container.children('.comment-content').find('p').text(newText);
                    
                    if(response.is_edited) {
                        // Шукаємо заголовок (h4) тільки серед прямих дітей, і в ньому span
                        container.children('h4').find('.edited-label').show();
                    }
                    
                    // Ховаємо форму, показуємо текст (тільки для поточного блоку)
                    container.children('.comment-edit-box').hide();
                    container.children('.comment-content').show();
                } else {
                    alert('Error saving comment: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Server error: ' + xhr.status + ' ' + xhr.statusText);
            }
        });
    });

    // 5. DELETE COMMENT (Видалити AJAX)
    $(document).on('click', '.btn-delete-inline', function(e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to delete this comment?')) return;

        var btn = $(this);
        var commentBlock = btn.closest('.comment-item');
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        var url = btn.data('url');

        $.ajax({
            url: url,
            type: 'POST',
            data: { '_csrf': csrfToken },
            success: function(response) {
                if(response.success) {
                    commentBlock.fadeOut(300, function() { $(this).remove(); });
                    var count = parseInt($('#comment-count').text());
                    $('#comment-count').text(Math.max(0, count - 1));
                } else {
                    alert('Error deleting comment.');
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Server error: ' + xhr.status + ' ' + xhr.statusText);
            }
        });
    });
JS;

$this->registerJs($js);
?>