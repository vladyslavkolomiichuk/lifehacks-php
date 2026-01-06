<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $article app\models\Article */

$this->title = $article->title;
?>

<div class="row">
  <div class="col-lg-8 col-md-10 mx-auto">

    <article class="article-single mb-5">
      <div class="mb-4 rounded-3 overflow-hidden shadow-sm">
        <img src="<?= $article->getImage(); ?>" alt="<?= Html::encode($article->title) ?>" class="w-100 object-fit-cover">
      </div>

      <header class="entry-header text-center">
        <div class="mb-2">
          <a href="<?= Url::to(['article/topic', 'id' => $article->topic->id]) ?>" class="text-decoration-none">
            <span class="bg-teal text-uppercase"><?= Html::encode($article->topic->name) ?></span>
          </a>
        </div>
        <h1 class="entry-title"><?= Html::encode($article->title) ?></h1>

        <div class="entry-meta mt-3">
          <span class="me-3"><i class="bi bi-person-circle me-1"></i> <?= Html::encode($article->user->name) ?></span>
          <span class="me-3"><i class="bi bi-calendar-event me-1"></i> <?= Yii::$app->formatter->asDate($article->date, 'long') ?></span>
          <span><i class="bi bi-eye-fill me-1"></i> <?= (int)$article->viewed ?> views</span>
        </div>
      </header>

      <div class="entry-content">
        <?= nl2br($article->description); ?>
      </div>

      <?php if (!empty($article->tag)): ?>
        <div class="tags-links">
          <?php foreach (explode(',', $article->tag) as $tag): ?>
            <?php $tag = trim($tag); ?>
            <?php if (!empty($tag)): ?>
              <a href="<?= Url::to(['article/search', 'q' => $tag]) ?>" class="tag-link">
                #<?= Html::encode($tag) ?>
              </a>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="article-footer">
        <div class="d-flex align-items-center">
          <span class="text-muted small me-3">Share:</span>
          <a href="#" class="btn-share btn-share-icon fs-5"
            data-title="<?= Html::encode($article->title) ?>"
            data-url="<?= Url::to(['article/view', 'id' => $article->id], true) ?>">
            <i class="bi bi-share-fill"></i>
          </a>
        </div>

        <div>
          <?php
          $isLiked = $article->isLikedByCurrentUser();
          $likeClass = $isLiked ? 'liked' : '';
          $heartIcon = $isLiked ? 'bi-heart-fill' : 'bi-heart';
          ?>
          <a href="<?= Url::to(['article/like', 'id' => $article->id]) ?>" class="btn-like <?= $likeClass ?>">
            <i class="bi <?= $heartIcon ?>"></i>
            <span><?= (int)$article->upvotes ?></span>Likes
          </a>
        </div>
      </div>
    </article>

    <div class="comments-area" id="comments-section">
      <h3 class="comments-title">
        Comments (<span id="comment-count"><?= count($comments) ?></span>)
      </h3>

      <?php if (!Yii::$app->user->isGuest): ?>
        <div class="leave-comment mb-5" id="comment-form-container">

          <div id="reply-block" class="alert alert-dark border-start border-4 border-info" style="display:none;">
            <span class="text-muted">Reply to: <b id="reply-to-user" class="text-white"></b></span>
            <button id="cancel-reply" class="btn btn-sm btn-outline-danger float-end">Cancel</button>
          </div>

          <?php $form = ActiveForm::begin([
            'action' => ['comment/create', 'id' => $article->id],
            'options' => ['class' => 'form-horizontal contact-form', 'role' => 'form']
          ]) ?>

          <div class="mb-3">
            <?= $form->field($commentForm, 'comment')->textarea([
              'class' => 'form-control dark-input',
              'id' => 'comment-textarea',
              'rows' => 4,
              'placeholder' => 'Write your helpful tip here...'
            ])->label(false) ?>

            <?= $form->field($commentForm, 'parentId')->hiddenInput(['class' => 'parent-id-input'])->label(false) ?>
          </div>

          <button type="submit" class="btn btn-purple">Post Comment</button>
          <?php ActiveForm::end(); ?>
        </div>
      <?php else: ?>
        <div class="alert alert-dark border border-secondary text-center mb-5" style="background-color: #1e1e1e;">
          <span style="color: #e0e0e0;">Please</span>
          <a href="<?= Url::to(['/auth/login']) ?>" class="fw-bold text-decoration-none" style="color: #bb86fc;">Login</a>
          <span style="color: #e0e0e0;">to leave a comment.</span>
        </div>
      <?php endif; ?>

      <div class="comment-list">
        <?php
        $rootComments = array_filter($comments, function ($c) {
          return $c->parent_id == null;
        });
        ?>

        <?php foreach ($rootComments as $comment): ?>
          <div class="d-flex mb-4 comment-item" data-id="<?= $comment->id ?>">
            <div class="flex-shrink-0 me-3">
              <img class="rounded-circle object-fit-cover"
                src="/uploads/<?= $comment->user->image ? $comment->user->image : 'default.jpg' ?>"
                alt="Avatar" style="width: 50px; height: 50px;">
            </div>
            <div class="flex-grow-1">
              <div class="media-body">
                <h5 class="mt-0 mb-1 text-white">
                  <?= Html::encode($comment->user->name); ?>
                  <small class="text-muted ms-2" style="font-size: 0.8rem;">
                    <?= Yii::$app->formatter->asRelativeTime($comment->date) ?>
                    <span class="edited-label fst-italic" style="display: <?= $comment->is_edited ? 'inline' : 'none' ?>">(edited)</span>
                  </small>
                </h5>

                <div class="comment-content text-light mb-2">
                  <p class="mb-0"><?= Html::encode($comment->text); ?></p>
                </div>

                <div class="comment-edit-box mb-2" style="display: none;">
                  <textarea class="form-control dark-input edit-textarea mb-2" rows="3"></textarea>
                  <button class="btn btn-sm btn-success save-edit"
                    data-id="<?= $comment->id ?>"
                    data-url="<?= Url::to(['comment/update', 'id' => $comment->id]) ?>">Save</button>
                  <button class="btn btn-sm btn-outline-secondary cancel-edit">Cancel</button>
                </div>

                <div class="comment-actions small">
                  <?php if (!Yii::$app->user->isGuest): ?>
                    <a href="#" class="btn-reply text-decoration-none me-3"
                      data-id="<?= $comment->id ?>"
                      data-user="<?= Html::encode($comment->user->name) ?>"
                      style="color: #bb86fc;">
                      <i class="bi bi-reply-fill"></i> Reply
                    </a>
                  <?php endif; ?>

                  <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id == $comment->user_id): ?>
                    <a href="#" class="btn-edit-inline text-decoration-none me-2 text-info">
                      <i class="bi bi-pencil-fill"></i> Edit
                    </a>
                    <a href="#" class="btn-delete-inline text-decoration-none text-danger"
                      data-id="<?= $comment->id ?>"
                      data-url="<?= Url::to(['comment/delete', 'id' => $comment->id]) ?>">
                      <i class="bi bi-trash-fill"></i> Delete
                    </a>
                  <?php endif; ?>
                </div>

                <?php foreach ($comment->children as $child): ?>
                  <div class="d-flex mt-3 pt-3 border-top border-secondary comment-item" data-id="<?= $child->id ?>">
                    <div class="flex-shrink-0 me-3">
                      <img class="rounded-circle object-fit-cover"
                        src="/uploads/<?= $child->user->image ? $child->user->image : 'default.jpg' ?>"
                        alt="Avatar" style="width: 40px; height: 40px;">
                    </div>
                    <div class="flex-grow-1">
                      <div class="media-body">
                        <h6 class="mt-0 mb-1 text-white">
                          <?= Html::encode($child->user->name); ?>
                          <small class="text-muted ms-2" style="font-size: 0.75rem;">
                            <?= Yii::$app->formatter->asRelativeTime($child->date) ?>
                            <span class="edited-label fst-italic" style="display: <?= $child->is_edited ? 'inline' : 'none' ?>">(edited)</span>
                          </small>
                        </h6>

                        <div class="comment-content text-light mb-2 small">
                          <p class="mb-0"><?= Html::encode($child->text); ?></p>
                        </div>

                        <div class="comment-edit-box mb-2" style="display: none;">
                          <textarea class="form-control dark-input edit-textarea mb-2" rows="2"></textarea>
                          <button class="btn btn-sm btn-success save-edit"
                            data-id="<?= $child->id ?>"
                            data-url="<?= Url::to(['comment/update', 'id' => $child->id]) ?>">Save</button>
                          <button class="btn btn-sm btn-outline-secondary cancel-edit">Cancel</button>
                        </div>

                        <div class="comment-actions small">
                          <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id == $child->user_id): ?>
                            <a href="#" class="btn-edit-inline text-decoration-none me-2 text-info">
                              <i class="bi bi-pencil-fill"></i> Edit
                            </a>
                            <a href="#" class="btn-delete-inline text-decoration-none text-danger"
                              data-id="<?= $child->id ?>"
                              data-url="<?= Url::to(['comment/delete', 'id' => $child->id]) ?>">
                              <i class="bi bi-trash-fill"></i> Delete
                            </a>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<?php
$js = <<<JS
    $('.btn-reply').on('click', function(e) {
        e.preventDefault();
        var commentId = $(this).data('id');
        var userName = $(this).data('user');
        $('.parent-id-input').val(commentId);
        $('#reply-to-user').text(userName);
        $('#reply-block').slideDown();
        $('html, body').animate({ scrollTop: $("#comment-form-container").offset().top - 150 }, 500);
        $('#comment-textarea').focus();
    });

    $('#cancel-reply').on('click', function(e) {
        e.preventDefault();
        $('.parent-id-input').val('');
        $('#reply-block').slideUp();
    });

    $(document).on('click', '.btn-edit-inline', function(e) {
        e.preventDefault();
        var container = $(this).closest('.media-body');
        
        var contentBox = container.children('.comment-content');
        var editBox = container.children('.comment-edit-box');
        var textarea = editBox.find('.edit-textarea');
        
        var currentText = contentBox.text().trim();
        textarea.val(currentText);
        
        contentBox.hide();
        editBox.show();
        textarea.focus();
    });

    $(document).on('click', '.cancel-edit', function(e) {
        e.preventDefault();
        var container = $(this).closest('.media-body');
        
        container.children('.comment-edit-box').hide();
        container.children('.comment-content').show();
    });

    $(document).on('click', '.save-edit', function(e) {
        e.preventDefault();
        var btn = $(this);
        var container = btn.closest('.media-body');
        var textarea = container.find('.edit-textarea');
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
                    container.children('.comment-content').find('p').text(newText);
                    if(response.is_edited) {
                        container.find('.edited-label').first().show();
                    }
                    container.children('.comment-edit-box').hide();
                    container.children('.comment-content').show();
                    
                    if(typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                          toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000,
                          background: '#333', color: '#fff'
                        });
                        Toast.fire({ icon: 'success', title: 'Comment updated' });
                    }
                } else {
                    alert('Error saving comment: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Server error');
            }
        });
    });

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
                     if(typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                          toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000,
                          background: '#333', color: '#fff'
                        });
                        Toast.fire({ icon: 'success', title: 'Comment deleted' });
                    }
                } else {
                    alert('Error deleting comment.');
                }
            },
            error: function() {
                alert('Server error occurred.');
            }
        });
    });
JS;

$this->registerJs($js);
?>