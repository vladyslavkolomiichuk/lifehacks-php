<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'User Cabinet';

// --- ЛОГІКА АНАЛІТИКИ (Calculation Logic) ---
$totalArticles = count($articles); // Кількість статей
$totalViews = 0;
$totalLikes = 0;

// Проходимо по всіх статтях і сумуємо перегляди та лайки
foreach ($articles as $article) {
  $totalViews += (int)$article->viewed;
  $totalLikes += (int)$article->upvotes;
}

// Рахуємо середнє (Avg), уникаючи ділення на нуль
$avgViews = $totalArticles > 0 ? round($totalViews / $totalArticles) : 0;
// ---------------------------------------------
?>

<div class="profile-index">
  <div class="row">
    <div class="col-md-4">
      <div class="card" style="background-color: #1e1e1e; border: 1px solid #333; padding: 20px; text-align: center;">
        <img src="/uploads/<?= $user->image ? $user->image : 'default.jpg' ?>"
          alt="Avatar"
          class="img-circle"
          style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #03dac6; border-radius: 50%; margin-bottom: 20px;">

        <h3 style="color: #fff;"><?= Html::encode($user->name) ?></h3>
        <p style="color: #777;"><?= Html::encode($user->login) ?></p>

        <div style="margin-top: 20px;">
          <?= Html::a('Edit Profile', ['update'], ['class' => 'btn btn-primary', 'style' => 'background-color: #bb86fc; border: none; color: #000; width: 100%; font-weight: bold;']) ?>
        </div>
      </div>
    </div>

    <div class="col-md-8">

      <div class="row" style="margin-bottom: 30px;">

        <div class="col-md-3 col-sm-6">
          <div class="stat-card" style="background: #1e1e1e; border: 1px solid #333; padding: 20px 10px; border-radius: 8px; text-align: center; height: 100%;">
            <i class="glyphicon glyphicon-pencil" style="font-size: 28px; color: #bb86fc; margin-bottom: 10px;"></i>
            <h3 style="margin: 5px 0; color: #fff; font-weight: bold;"><?= $totalArticles ?></h3>
            <p style="margin: 0; color: #777; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Articles</p>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="stat-card" style="background: #1e1e1e; border: 1px solid #333; padding: 20px 10px; border-radius: 8px; text-align: center; height: 100%;">
            <i class="glyphicon glyphicon-eye-open" style="font-size: 28px; color: #03dac6; margin-bottom: 10px;"></i>
            <h3 style="margin: 5px 0; color: #fff; font-weight: bold;"><?= $totalViews ?></h3>
            <p style="margin: 0; color: #777; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Total Views</p>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="stat-card" style="background: #1e1e1e; border: 1px solid #333; padding: 20px 10px; border-radius: 8px; text-align: center; height: 100%;">
            <i class="glyphicon glyphicon-heart" style="font-size: 28px; color: #cf6679; margin-bottom: 10px;"></i>
            <h3 style="margin: 5px 0; color: #fff; font-weight: bold;"><?= $totalLikes ?></h3>
            <p style="margin: 0; color: #777; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Total Likes</p>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="stat-card" style="background: #1e1e1e; border: 1px solid #333; padding: 20px 10px; border-radius: 8px; text-align: center; height: 100%;">
            <i class="glyphicon glyphicon-stats" style="font-size: 28px; color: #ffd700; margin-bottom: 10px;"></i>
            <h3 style="margin: 5px 0; color: #fff; font-weight: bold;"><?= $avgViews ?></h3>
            <p style="margin: 0; color: #777; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Avg. Views</p>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 1px solid #333; padding-bottom: 15px; margin-bottom: 20px;">
        <h2 style="color: #03dac6; margin: 0; font-size: 24px;">My Articles (<?= count($articles) ?>)</h2>
        <?= Html::a('+ Create New', ['create-article'], ['class' => 'btn btn-success', 'style' => 'background-color: #03dac6; color: #000; font-weight: bold; border: none; padding: 8px 20px;']) ?>
      </div>

      <?php if (empty($articles)): ?>
        <div class="alert alert-info" style="background: #2d2d2d; border: none; color: #ccc;">
          You haven't created any articles yet. Start writing now!
        </div>
      <?php else: ?>
        <div class="list-group">
          <?php foreach ($articles as $article): ?>
            <div class="list-group-item" style="background-color: #1e1e1e; border: 1px solid #333; margin-bottom: 15px; border-radius: 5px;">
              <div class="row align-items-center">
                <div class="col-md-2 col-xs-3">
                  <img src="<?= $article->getImage() ?>" style="width: 100%; height: 60px; object-fit: cover; border-radius: 4px;">
                </div>

                <div class="col-md-7 col-xs-9">
                  <h4 class="list-group-item-heading" style="margin-top: 5px; margin-bottom: 5px;">
                    <a href="<?= Url::to(['/site/view', 'id' => $article->id]) ?>" style="color: #fff; text-decoration: none;">
                      <?= Html::encode($article->title) ?>
                    </a>
                  </h4>
                  <p class="text-muted small" style="color: #777; margin: 0;">
                    <i class="glyphicon glyphicon-calendar"></i> <?= $article->date ?> &nbsp;|&nbsp;
                    <i class="glyphicon glyphicon-eye-open"></i> <?= (int)$article->viewed ?> &nbsp;|&nbsp;
                    <i class="glyphicon glyphicon-heart"></i> <?= (int)$article->upvotes ?>
                  </p>
                </div>

                <div class="col-md-3 col-xs-12 text-end" style="text-align: right;">
                  <div class="btn-group btn-group-sm">
                    <?= Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['update-article', 'id' => $article->id], [
                      'class' => 'btn btn-primary',
                      'title' => 'Edit',
                      'style' => 'background-color: #bb86fc; border:none; color: #000;'
                    ]) ?>

                    <?= Html::a('<i class="glyphicon glyphicon-trash"></i>', ['delete-article', 'id' => $article->id], [
                      'class' => 'btn btn-danger',
                      'title' => 'Delete',
                      'style' => 'background-color: #cf6679; border:none; color: #000;',
                      'data' => [
                        'confirm' => 'Are you sure you want to delete this article?',
                        'method' => 'post',
                      ],
                    ]) ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>