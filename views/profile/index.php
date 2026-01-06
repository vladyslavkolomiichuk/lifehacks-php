<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'User Cabinet';

$totalArticles = count($articles);
$totalViews = 0;
$totalLikes = 0;

foreach ($articles as $article) {
  $totalViews += (int)$article->viewed;
  $totalLikes += (int)$article->upvotes;
}

$avgViews = $totalArticles > 0 ? round($totalViews / $totalArticles) : 0;
?>

<div class="profile-index">
  <div class="row">
    <div class="col-md-4 mb-4">
      <div class="widget text-center p-4">
        <div class="mb-4 d-inline-block position-relative">
          <img src="/uploads/<?= $user->image ? $user->image : 'default.jpg' ?>"
            alt="Avatar"
            class="rounded-circle object-fit-cover border border-3 border-info"
            style="width: 150px; height: 150px;">
        </div>

        <h3 class="text-white fw-bold mb-1"><?= Html::encode($user->name) ?></h3>

        <p class="mb-4" style="color: #ccc;"><?= Html::encode($user->email) ?></p>

        <?= Html::a('Edit Profile', ['update'], ['class' => 'btn btn-purple w-100']) ?>
      </div>
    </div>

    <div class="col-md-8">

      <div class="row mb-4 g-3">
        <div class="col-md-3 col-6">
          <div class="widget text-center p-3 h-100 mb-0 d-flex flex-column justify-content-center">
            <i class="bi bi-pencil-fill fs-2 text-purple mb-2" style="color: #bb86fc;"></i>
            <h3 class="text-white fw-bold m-0"><?= $totalArticles ?></h3>
            <small class="text-uppercase" style="color: #ccc; font-size: 0.7rem; letter-spacing: 1px;">Articles</small>
          </div>
        </div>

        <div class="col-md-3 col-6">
          <div class="widget text-center p-3 h-100 mb-0 d-flex flex-column justify-content-center">
            <i class="bi bi-eye-fill fs-2 text-info mb-2"></i>
            <h3 class="text-white fw-bold m-0"><?= $totalViews ?></h3>
            <small class="text-uppercase" style="color: #ccc; font-size: 0.7rem; letter-spacing: 1px;">Total Views</small>
          </div>
        </div>

        <div class="col-md-3 col-6">
          <div class="widget text-center p-3 h-100 mb-0 d-flex flex-column justify-content-center">
            <i class="bi bi-heart-fill fs-2 text-danger mb-2" style="color: #cf6679;"></i>
            <h3 class="text-white fw-bold m-0"><?= $totalLikes ?></h3>
            <small class="text-uppercase" style="color: #ccc; font-size: 0.7rem; letter-spacing: 1px;">Total Likes</small>
          </div>
        </div>

        <div class="col-md-3 col-6">
          <div class="widget text-center p-3 h-100 mb-0 d-flex flex-column justify-content-center">
            <i class="bi bi-bar-chart-fill fs-2 text-warning mb-2"></i>
            <h3 class="text-white fw-bold m-0"><?= $avgViews ?></h3>
            <small class="text-uppercase" style="color: #ccc; font-size: 0.7rem; letter-spacing: 1px;">Avg. Views</small>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
        <h2 class="h3 m-0" style="color: #03dac6;">My Articles (<?= count($articles) ?>)</h2>
        <?= Html::a('+ Create New', ['article/create'], ['class' => 'btn btn-sm fw-bold', 'style' => 'background-color: #03dac6; color: #000;']) ?>
      </div>

      <?php if (empty($articles)): ?>
        <div class="alert alert-dark border border-secondary text-center py-4">
          <i class="bi bi-journal-text fs-1 text-muted mb-3 d-block"></i>
          <p class="text-muted mb-0">You haven't created any articles yet. Start writing now!</p>
        </div>
      <?php else: ?>
        <div class="list-group">
          <?php foreach ($articles as $article): ?>
            <div class="widget p-3 mb-3 d-flex align-items-center">
              <div class="flex-shrink-0 me-3">
                <img src="<?= $article->getImage() ?>"
                  class="rounded object-fit-cover"
                  style="width: 80px; height: 60px;">
              </div>

              <div class="flex-grow-1 min-width-0">
                <h5 class="mb-1 text-truncate">
                  <a href="<?= Url::to(['article/view', 'id' => $article->id]) ?>" class="text-white text-decoration-none">
                    <?= Html::encode($article->title) ?>
                  </a>
                </h5>

                <div class="small" style="color: #ccc;">
                  <span class="me-3" title="Date">
                    <i class="bi bi-calendar3 me-1"></i> <?= Yii::$app->formatter->asDate($article->date, 'short') ?>
                  </span>
                  <span class="me-3" title="Views">
                    <i class="bi bi-eye-fill me-1"></i> <?= (int)$article->viewed ?>
                  </span>
                  <span title="Likes">
                    <i class="bi bi-heart-fill me-1"></i> <?= (int)$article->upvotes ?>
                  </span>
                </div>
              </div>

              <div class="flex-shrink-0 ms-3">
                <div class="btn-group">
                  <?= Html::a('<i class="bi bi-pencil-fill"></i>', ['article/update', 'id' => $article->id], [
                    'class' => 'btn btn-sm btn-outline-light',
                    'title' => 'Edit',
                    'style' => 'border-color: #444; color: #bb86fc;'
                  ]) ?>
                  <?= Html::a('<i class="bi bi-trash-fill"></i>', ['article/delete', 'id' => $article->id], [
                    'class' => 'btn btn-sm btn-outline-light',
                    'title' => 'Delete',
                    'style' => 'border-color: #444; color: #cf6679;',
                    'data' => [
                      'confirm' => 'Are you sure you want to delete this article?',
                      'method' => 'post',
                    ],
                  ]) ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>