<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Breadcrumbs;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
  <meta charset="<?= Yii::$app->charset ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php $this->registerCsrfMetaTags() ?>
  <title>Admin Panel - <?= Html::encode($this->title) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <?php $this->head() ?>
  <style>
    body {
      background-color: #121212;
      color: #e0e0e0;
    }

    .card {
      background-color: #1e1e1e;
      border: 1px solid #333;
    }

    .table {
      color: #e0e0e0;
    }

    .table-hover tbody tr:hover {
      color: #fff;
      background-color: #2d2d2d;
    }

    a {
      color: #03dac6;
      text-decoration: none;
    }

    a:hover {
      color: #fff;
    }

    .breadcrumb-item.active {
      color: #888;
    }
  </style>
</head>

<body class="d-flex flex-column h-100">
  <?php $this->beginBody() ?>

  <header>
    <?php
    NavBar::begin([
      'brandLabel' => 'LifeHacks Admin',
      'brandUrl' => ['/admin/default/index'],
      'options' => ['class' => 'navbar navbar-expand-md navbar-dark bg-dark border-bottom border-secondary fixed-top'],
    ]);

    echo Nav::widget([
      'options' => ['class' => 'navbar-nav ms-auto'],
      'items' => [
        ['label' => 'Dashboard', 'url' => ['/admin/default/index']],
        ['label' => 'Users', 'url' => ['/admin/user/index']],
        ['label' => 'Topics', 'url' => ['/admin/topic/index']],
        ['label' => 'Articles', 'url' => ['/admin/article/index']],
        ['label' => 'Comments', 'url' => ['/admin/comment/index']], // <--- NEW
        ['label' => 'Votes', 'url' => ['/admin/vote/index']],       // <--- NEW
        ['label' => 'Back to Site', 'url' => ['/site/index'], 'linkOptions' => ['target' => '_blank']],
        ['label' => 'Logout', 'url' => ['/auth/logout'], 'linkOptions' => ['data-method' => 'post', 'class' => 'text-danger']],
      ],
    ]);
    NavBar::end();
    ?>
  </header>

  <main role="main" class="flex-shrink-0" style="padding-top: 80px;">
    <div class="container">
      <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        'homeLink' => ['label' => 'Admin', 'url' => ['/admin/default/index']],
      ]) ?>
      <?= $content ?>
    </div>
  </main>

  <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>