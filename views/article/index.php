<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $articles app\models\Article[] */
/* @var $pagination yii\data\Pagination */
/* @var $popular app\models\Article[] */
/* @var $topics app\models\Topic[] */

$this->title = 'LifeHacks - Articles';
?>

<style>
    .article-card {
        background-color: #252525;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        margin-bottom: 30px;
        border: none;
    }

    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3) !important;
    }

    .card-img-top-wrapper {
        height: 220px;
        overflow: hidden;
        position: relative;
    }

    .card-img-top {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .article-card:hover .card-img-top {
        transform: scale(1.05);
    }

    .card-body {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .bg-teal {
        background-color: #03dac6 !important;
        color: #000;
        font-weight: bold;
        font-size: 0.75rem;
        padding: 5px 10px;
        border-radius: 4px;
        display: inline-block;
    }

    .article-title {
        font-weight: 700;
        margin: 15px 0;
        font-size: 1.25rem;
    }

    .article-title a {
        color: #fff;
        text-decoration: none;
    }

    .article-title a:hover {
        color: #bb86fc;
    }

    .article-excerpt {
        color: #aaaaaa;
        font-size: 0.95rem;
        margin-bottom: 15px;
        flex-grow: 1;
    }

    .article-footer {
        border-top: 1px solid #333;
        padding-top: 15px;
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        color: #777;
        font-size: 0.85rem;
    }

    .tag-link {
        font-size: 0.75rem;
        color: #bb86fc;
        text-decoration: none;
        margin-right: 5px;
        background: rgba(187, 134, 252, 0.1);
        padding: 2px 8px;
        border-radius: 10px;
    }

    .tag-link:hover {
        background: rgba(187, 134, 252, 0.3);
        color: #fff;
    }
</style>

<div class="site-index">
    <div class="row">

        <div class="col-md-8">
            <div class="row">
                <?php foreach ($articles as $article): ?>
                    <div class="col-md-12 mb-4">
                        <article class="article-card shadow-sm">
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
                                        <?= Html::encode($article->title) ?>
                                    </a>
                                </h2>

                                <div class="article-excerpt">
                                    <p><?= mb_strimwidth(strip_tags($article->description), 0, 200, "..."); ?></p>
                                </div>

                                <?php if (!empty($article->tag)): ?>
                                    <div class="mb-3">
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
                                <div class="mb-3">
                                    <a href="<?= Url::toRoute(['article/view', 'id' => $article->id]); ?>" class="btn-purple">
                                        Read More
                                    </a>
                                </div>

                                <div class="article-footer">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-1"></i>
                                        <span class="me-3"><?= Html::encode($article->user->name) ?></span>
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <span><?= Yii::$app->formatter->asDate($article->date, 'medium') ?></span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <?php $isLiked = $article->isLikedByCurrentUser(); ?>
                                        <i class="bi bi-heart-fill me-1" style="color: <?= $isLiked ? '#cf6679' : '#777' ?>;"></i>
                                        <span><?= (int)$article->upvotes ?></span>

                                        <a href="#" class="btn-share ms-3 text-muted"
                                            data-title="<?= Html::encode($article->title) ?>"
                                            data-url="<?= Url::to(['article/view', 'id' => $article->id], true) ?>">
                                            <i class="bi bi-share-fill"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
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
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-bottom: 1px solid #2d2d2d; padding: 12px 0;">
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
                            <span class="popular-date">
                                <i class="bi bi-calendar3 me-1"></i> <?= Yii::$app->formatter->asDate($article->date, 'medium'); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>