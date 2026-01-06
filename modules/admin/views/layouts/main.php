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
</head>

<body class="d-flex flex-column h-100">
  <?php $this->beginBody() ?>

  <header>
    <?php
    NavBar::begin([
      'brandLabel' => 'LifeHacks Admin',
      'brandUrl' => ['/admin/default/index'],
      'options' => ['class' => 'navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary fixed-top shadow'],
    ]);

    echo Nav::widget([
      'options' => ['class' => 'navbar-nav ms-auto'],
      'items' => [
        ['label' => 'Dashboard', 'url' => ['/admin/default/index'], 'encode' => false],
        ['label' => 'Users', 'url' => ['/admin/user/index'], 'encode' => false],
        ['label' => 'Topics', 'url' => ['/admin/topic/index'], 'encode' => false],
        ['label' => 'Articles', 'url' => ['/admin/article/index'], 'encode' => false],
        ['label' => 'Comments', 'url' => ['/admin/comment/index'], 'encode' => false],
        ['label' => 'Votes', 'url' => ['/admin/vote/index'], 'encode' => false],

        [
          'label' => 'Back to Site',
          'url' => ['/site/index'],
          'encode' => false,
          'linkOptions' => [
            'target' => '_blank',
            'class' => 'fw-bold',
            'style' => 'color: #ffca28 !important; font-weight: bold;'
          ],
        ],
        [
          'label' => 'Logout',
          'url' => ['/auth/logout'],
          'encode' => false,
          'linkOptions' => [
            'data-method' => 'post',
            'style' => 'color: #cf6679 !important;'
          ],
        ],
      ],
    ]);
    NavBar::end();
    ?>
  </header>

  <main role="main" class="flex-shrink-0" style="padding-top: 80px;">
    <div class="container">
      <?= Breadcrumbs::widget([
        'homeLink' => ['label' => 'Admin', 'url' => ['/admin/default/index']],
        'options' => ['class' => 'breadcrumb bg-transparent p-0 mb-4'],
        'itemTemplate' => "<li class=\"breadcrumb-item\">{link}</li>\n",
        'activeItemTemplate' => "<li class=\"breadcrumb-item active\" aria-current=\"page\">{link}</li>\n",
      ]) ?>

      <?= $content ?>
    </div>
  </main>

  <footer class="footer mt-auto py-3 bg-dark border-top border-secondary text-center text-muted small">
    <div class="container">
      &copy; LifeHacks Admin Panel <?= date('Y') ?>
    </div>
  </footer>

  <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>