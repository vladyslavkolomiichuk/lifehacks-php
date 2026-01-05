<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;

$this->title = 'Search results for: ' . Html::encode($q);
?>

<div class="site-search">
  <div class="row">

    <div class="col-md-8">
      <div class="category-header mb-4" style="border-bottom: 1px solid #333; padding-bottom: 10px;">
        <h1 style="color: #fff;">Search results for: <span style="color: #03dac6;">"<?= Html::encode($q) ?>"</span></h1>
      </div>

      <?php if (empty($articles)): ?>
        <div class="alert alert-warning" style="background-color: #333; border: 1px solid #555; color: #ccc;">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> No articles found matching your query.
        </div>
        <div style="margin-top: 20px;">
          <a href="<?= Url::to(['article/index']) ?>" class="btn btn-purple">Go Back Home</a>
        </div>
      <?php else: ?>

        <div class="row">
          <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
              <div class="col-md-12 mb-4">
                <article class="article-card shadow-sm mb-4">
                  <div class="card-img-top-wrapper">
                    <a href="<?= Url::toRoute(['article/view', 'id' => $article->id]); ?>">
                      <img src="<?= $article->getImage(); ?>" class="card-img-top" alt="<?= Html::encode($article->title) ?>">
                    </a>
                  </div>

                  <div class="card-body">
                    <div>
                      <a href="<?= Url::to(['article/topic', 'id' => $article->topic->id]) ?>" class="text-decoration-none">
                        <span class="bg-teal text-uppercase"><?= Html::encode($article->topic->name) ?></span>
                      </a>
                    </div>

                    <h2 class="article-title">
                      <a href="<?= Url::toRoute(['article/view', 'id' => $article->id]); ?>">
                        <?= Html::encode($article->title); ?>
                      </a>
                    </h2>

                    <div class="article-excerpt">
                      <p><?= mb_strimwidth(strip_tags($article->description), 0, 200, "..."); ?></p>
                    </div>

                    <div class="mb-3">
                      <a href="<?= Url::toRoute(['article/view', 'id' => $article->id]); ?>" class="btn-purple">Read More</a>
                    </div>

                    <div class="article-footer">
                      <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle me-1"></i>
                        <span class="me-3"><?= Html::encode($article->user->name); ?></span>
                        <i class="bi bi-calendar-event me-1"></i>
                        <span><?= Yii::$app->formatter->asDate($article->date, 'medium'); ?></span>
                      </div>

                      <div class="d-flex align-items-center">
                        <?php $isLiked = $article->isLikedByCurrentUser(); ?>
                        <i class="bi bi-heart-fill me-1" style="color: <?= $isLiked ? '#cf6679' : '#777' ?>;"></i>
                        <span><?= (int)$article->upvotes ?></span>
                      </div>
                    </div>
                  </div>
                </article>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-md-12">
              <div class="alert alert-dark border border-secondary text-center py-5">
                <h4>No articles found in this category.</h4>
                <p class="text-muted">Check back later for updates!</p>
                <a href="<?= Url::to(['article/index']) ?>" class="btn btn-purple mt-3">Go Back Home</a>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <div class="text-center mt-4 mb-5">
          <?= LinkPager::widget([
            'pagination' => $pagination,
            'options' => ['class' => 'pagination justify-content-center'],
            'linkContainerOptions' => ['class' => 'page-item'],
            'linkOptions' => ['class' => 'page-link'],
            'disabledListItemSubTagOptions' => ['class' => 'page-link'],
            'activePageCssClass' => 'active',
          ]) ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-md-4">

      <div class="widget">
        <h3 class="widget-title">Search</h3>
        <?= Html::beginForm(['/article/search'], 'get') ?>
        <div class="input-group">
          <input type="text" name="q" class="form-control dark-input" placeholder="Find a tip..." value="<?= isset($q) ? Html::encode($q) : '' ?>">
          <button class="btn" type="submit" style="background: #03dac6; color: #000; border: none; padding: 0 15px;">
            <i class="bi bi-search"></i>
          </button>
        </div>
        <?= Html::endForm() ?>
      </div>

      <div class="widget">
        <h3 class="widget-title">Categories</h3>
        <ul class="list-group list-group-flush">
          <?php foreach ($topics as $topic): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-bottom: 1px solid #2d2d2d; border-top: none; padding: 12px 0; color: #ccc;">
              <a href="<?= Url::to(['article/topic', 'id' => $topic->id]) ?>" class="text-decoration-none" style="color: #ccc;">
                <?= Html::encode($topic->name); ?>
              </a>
              <span class="badge rounded-pill" style="background-color: #03dac6; color: #000;">
                <?= $topic->getArticles()->count(); ?>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="widget">
        <h3 class="widget-title">Popular Posts</h3>
        <?php foreach ($popular as $article): ?>
          <div class="popular-post-item">
            <a href="<?= Url::to(['article/view', 'id' => $article->id]) ?>">
              <img class="popular-img" src="<?= $article->getImage(); ?>" alt="<?= Html::encode($article->title) ?>">
            </a>
            <div class="popular-info">
              <h5>
                <a href="<?= Url::to(['article/view', 'id' => $article->id]) ?>">
                  <?= Html::encode($article->title); ?>
                </a>
              </h5>
              <span style="color: #777; font-size: 0.8rem;">
                <i class="bi bi-calendar3 me-1"></i> <?= Yii::$app->formatter->asDate($article->date, 'medium'); ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>

  </div>
</div>