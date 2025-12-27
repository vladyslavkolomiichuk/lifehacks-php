<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'User Cabinet';
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
      <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 1px solid #333; padding-bottom: 10px;">
        <h2 style="color: #03dac6; margin: 0;">My Articles (<?= count($articles) ?>)</h2>
        <?= Html::a('+ Create New', ['create-article'], ['class' => 'btn btn-success', 'style' => 'background-color: #03dac6; color: #000; font-weight: bold; border: none;']) ?>
      </div>

      <?php if (empty($articles)): ?>
      <?php else: ?>
        <div class="list-group">
          <?php foreach ($articles as $article): ?>
            <div class="list-group-item" style="background-color: #1e1e1e; border: 1px solid #333; margin-bottom: 10px;">
              <div class="row">
                <div class="col-md-2">
                  <img src="<?= $article->getImage() ?>" style="width: 100%; height: 60px; object-fit: cover;">
                </div>
                <div class="col-md-8">
                  <h4 class="list-group-item-heading">
                    <a href="<?= Url::to(['/site/view', 'id' => $article->id]) ?>" style="color: #fff;">
                      <?= Html::encode($article->title) ?>
                    </a>
                  </h4>
                  <p class="text-muted small">Date: <?= $article->date ?> | Views: <?= $article->viewed ?></p>
                </div>
                <div class="col-md-2 text-end">
                  <div class="btn-group-vertical btn-group-sm">
                    <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> Edit', ['update-article', 'id' => $article->id], ['class' => 'btn btn-primary', 'style' => 'background-color: #bb86fc; border:none; color: #000; margin-bottom: 2px;']) ?>

                    <?= Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', ['delete-article', 'id' => $article->id], [
                      'class' => 'btn btn-danger',
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