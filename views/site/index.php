<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;

$this->title = 'LifeHacks - Useful Tips';
?>

<div class="site-index">
    <div class="row">

        <div class="col-md-8">
            <?php foreach ($articles as $article): ?>
                <article class="post" style="background-color: #1e1e1e; border: 1px solid #333; margin-bottom: 30px; border-radius: 5px; overflow: hidden;">
                    <div class="post-thumb">
                        <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]); ?>">
                            <img src="<?= $article->getImage(); ?>" alt="<?= $article->title ?>" style="width:100%; object-fit: cover; height: 300px;">
                        </a>
                    </div>

                    <div class="post-content" style="padding: 20px;">
                        <header class="entry-header">
                            <h6 style="color: #03dac6; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">
                                Category: <a href="<?= Url::to(['site/topic', 'id' => $article->topic->id]) ?>" style="color: #03dac6;"><?= $article->topic->name; ?></a>
                            </h6>
                            <h1 class="entry-title" style="margin-top: 10px;">
                                <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]); ?>" style="color: #fff; text-decoration: none;"><?= $article->title; ?></a>
                            </h1>
                        </header>

                        <div class="entry-content" style="color: #ccc; margin: 15px 0;">
                            <p>
                                <?= mb_strimwidth($article->description, 0, 200, "..."); ?>
                            </p>
                        </div>

                        <div class="btn-continue-reading">
                            <a href="<?= Url::toRoute(['site/view', 'id' => $article->id]); ?>" class="btn btn-primary" style="background-color: #bb86fc; border: none; color: #000; font-weight: bold;">Read More</a>
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
                        </div>

                        <div class="btn-group">
                            <?php
                            $isLiked = $article->isLikedByCurrentUser();
                            $likeColor = $isLiked ? '#cf6679' : '#777'; // Червоний якщо лайкнув, сірий якщо ні
                            $iconClass = $isLiked ? 'glyphicon-heart' : 'glyphicon-heart-empty';
                            ?>
                            <a href="<?= Url::to(['site/like', 'id' => $article->id]) ?>" class="btn btn-default" style="border: 1px solid #444; background: #2d2d2d; color: <?= $likeColor ?>;">
                                <i class="glyphicon <?= $iconClass ?>"></i> <?= $article->upvotes ?> Likes
                            </a>
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
        </div>

        <div class="col-md-4">

            <div class="widget" style="background-color: #1e1e1e; padding: 20px; border: 1px solid #333; margin-bottom: 30px; border-radius: 5px;">
                <h3 class="widget-title" style="color: #fff; border-bottom: 1px solid #333; padding-bottom: 10px; margin-top: 0;">Search</h3>

                <form method="get" action="<?= Url::to(['site/search']) ?>" style="margin-top: 15px;">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Find a tip..." style="background: #2d2d2d; border: 1px solid #444; color: #fff;">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit" style="background: #03dac6; color: #000; border: none; font-weight: bold;">Go</button>
                        </span>
                    </div>
                </form>
            </div>

            <div class="widget" style="background-color: #1e1e1e; padding: 20px; border: 1px solid #333; margin-bottom: 30px; border-radius: 5px;">
                <h3 class="widget-title" style="color: #fff; border-bottom: 1px solid #333; padding-bottom: 10px; margin-top: 0;">Categories</h3>
                <ul class="list-group" style="list-style: none; padding: 0; margin-top: 15px;">
                    <?php foreach ($topics as $topic): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border: none; border-bottom: 1px solid #333; padding: 10px 0;">
                            <a href="<?= Url::to(['site/topic', 'id' => $topic->id]) ?>" style="color: #ccc; text-decoration: none; font-size: 16px;">
                                <?= $topic->name; ?>
                            </a>
                            <span class="badge bg-primary rounded-pill" style="background-color: #03dac6 !important; color: #000;"><?= $topic->getArticles()->count(); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="widget" style="background-color: #1e1e1e; padding: 20px; border: 1px solid #333; border-radius: 5px;">
                <h3 class="widget-title" style="color: #fff; border-bottom: 1px solid #333; padding-bottom: 10px; margin-top: 0;">Popular Posts</h3>
                <?php foreach ($popular as $article): ?>
                    <div class="media" style="margin-top: 15px; border-bottom: 1px solid #333; padding-bottom: 10px;">
                        <div class="media-left" style="float: left; margin-right: 15px;">
                            <a href="<?= Url::to(['site/view', 'id' => $article->id]) ?>">
                                <img class="media-object" src="<?= $article->getImage(); ?>" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                            </a>
                        </div>
                        <div class="media-body">
                            <h5 class="media-heading" style="margin-top: 0;">
                                <a href="<?= Url::to(['site/view', 'id' => $article->id]) ?>" style="color: #e0e0e0; font-size: 14px;"><?= $article->title; ?></a>
                            </h5>
                            <span style="color: #777; font-size: 12px;"><?= $article->date; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>