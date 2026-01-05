<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $articles app\models\Article[] */
/* @var $pagination yii\data\Pagination */
/* @var $popular app\models\Article[] */
/* @var $topics app\models\Topic[] */
/* @var $topic app\models\Topic */

$this->title = $topic->name;
?>

<div class="site-topic">
  <div class="row">

    <div class="col-md-8">

      <div class="mb-4 pb-2 border-bottom border-secondary category-header">
        <h1 class="text-white">Category: <span style="color: #03dac6;"><?= Html::encode($topic->name) ?></span></h1>
      </div>

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

      <div class="d-flex justify-content-center mt-4 mb-5">
        <?= LinkPager::widget([
          'pagination' => $pagination,
          'options' => ['class' => 'pagination'],
          'linkContainerOptions' => ['class' => 'page-item'],
          'linkOptions' => ['class' => 'page-link'],
          'disabledListItemSubTagOptions' => ['class' => 'page-link'],
          'activePageCssClass' => 'active',
        ]) ?>
      </div>
    </div>

    <div class="col-md-4">

      <div class="widget">
        <h3 class="widget-title">Categories</h3>
        <ul class="list-group list-group-flush">
          <?php foreach ($topics as $t): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center category-item"
              style="background: transparent; border-bottom: 1px solid #2d2d2d; padding: 12px 0;">

              <a href="<?= Url::to(['article/topic', 'id' => $t->id]) ?>"
                class="text-decoration-none category-link <?= ($t->id == $topic->id) ? 'active' : '' ?>"
                class="text-decoration-none" style="color: #ccc;">
                <?= Html::encode($t->name); ?>
              </a>

              <span class="badge rounded-pill"
                style="background-color: <?= ($t->id == $topic->id) ? '#bb86fc' : '#03dac6' ?>; color: #000;">
                <?= $t->getArticles()->count(); ?>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="widget">
        <h3 class="widget-title">Popular Posts</h3>
        <?php foreach ($popular as $popArticle): ?>
          <div class="popular-post-item">
            <a href="<?= Url::to(['article/view', 'id' => $popArticle->id]) ?>">
              <img class="popular-img" src="<?= $popArticle->getImage(); ?>" alt="<?= Html::encode($popArticle->title) ?>">
            </a>
            <div class="popular-info">
              <h5>
                <a href="<?= Url::to(['article/view', 'id' => $popArticle->id]) ?>">
                  <?= Html::encode($popArticle->title); ?>
                </a>
              </h5>
              <span class="popular-date">
                <i class="bi bi-calendar3 me-1"></i> <?= Yii::$app->formatter->asDate($popArticle->date, 'medium'); ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</div>