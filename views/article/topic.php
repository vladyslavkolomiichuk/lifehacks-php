<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;

$this->title = 'Category: ' . $topic->name;
?>

<div class="site-topic">
  <div class="row">

    <div class="col-md-8">
      <div class="category-header" style="margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 10px;">
        <h1 style="color: #fff;">Category: <span style="color: #03dac6;"><?= $topic->name ?></span></h1>
      </div>

      <?php if (empty($articles)): ?>
        <div class="alert alert-warning" style="background-color: #333; border: none; color: #ccc;">
          No articles found in this category yet.
        </div>
      <?php else: ?>
        <?php foreach ($articles as $article): ?>
          <article class="post" style="background-color: #1e1e1e; border: 1px solid #333; margin-bottom: 30px; border-radius: 5px; overflow: hidden;">
            <div class="post-thumb">
              <a href="<?= Url::toRoute(['article/view', 'id' => $article->id]); ?>">
                <img src="<?= $article->getImage(); ?>" alt="<?= $article->title ?>" style="width:100%; object-fit: cover; height: 300px;">
              </a>
            </div>
            <div class="post-content" style="padding: 20px;">
              <header class="entry-header">
                <h1 class="entry-title" style="margin-top: 10px;">
                  <a href="<?= Url::toRoute(['article/view', 'id' => $article->id]); ?>" style="color: #fff; text-decoration: none;"><?= $article->title; ?></a>
                </h1>
              </header>
              <div class="entry-content" style="color: #ccc; margin: 15px 0;">
                <p><?= mb_strimwidth($article->description, 0, 200, "..."); ?></p>
              </div>
              <div class="btn-continue-reading">
                <a href="<?= Url::toRoute(['article/view', 'id' => $article->id]); ?>" class="btn btn-primary" style="background-color: #bb86fc; border: none; color: #000; font-weight: bold;">Read More</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>

        <div class="text-center">
          <?= LinkPager::widget([
            'pagination' => $pagination,
            'options' => ['class' => 'pagination justify-content-center'],
            'linkOptions' => ['class' => 'page-link', 'style' => 'background: #333; border-color: #444; color: #fff;'],
            'disabledListItemSubTagOptions' => ['class' => 'page-link', 'style' => 'background: #222; border-color: #444; color: #555;']
          ]) ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-md-4">
      <div class="widget" style="background-color: #1e1e1e; padding: 20px; border: 1px solid #333; margin-bottom: 30px; border-radius: 5px;">
        <h3 class="widget-title" style="color: #fff; border-bottom: 1px solid #333; padding-bottom: 10px;">Categories</h3>
        <ul class="list-group" style="list-style: none; padding: 0; margin-top: 15px;">
          <?php foreach ($topics as $t): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border: none; border-bottom: 1px solid #333; padding: 10px 0;">
              <a href="<?= Url::to(['article/topic', 'id' => $t->id]) ?>" style="color: <?= ($t->id == $topic->id) ? '#03dac6' : '#ccc' ?>; text-decoration: none; font-size: 16px;">
                <?= $t->name; ?>
              </a>
              <span class="badge bg-primary rounded-pill" style="background-color: #03dac6 !important; color: #000;"><?= $t->getArticles()->count(); ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="widget" style="background-color: #1e1e1e; padding: 20px; border: 1px solid #333; border-radius: 5px;">
        <h3 class="widget-title" style="color: #fff; border-bottom: 1px solid #333; padding-bottom: 10px;">Popular Posts</h3>
        <?php foreach ($popular as $article): ?>
          <div class="media" style="margin-top: 15px; border-bottom: 1px solid #333; padding-bottom: 10px;">
            <div class="media-left" style="float: left; margin-right: 15px;">
              <a href="<?= Url::to(['article/view', 'id' => $article->id]) ?>">
                <img class="media-object" src="<?= $article->getImage(); ?>" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
              </a>
            </div>
            <div class="media-body">
              <h5 class="media-heading" style="margin-top: 0;">
                <a href="<?= Url::to(['article/view', 'id' => $article->id]) ?>" style="color: #e0e0e0; font-size: 14px;"><?= $article->title; ?></a>
              </h5>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>