<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Nav;       // Змінено з bootstrap на bootstrap5
use yii\bootstrap5\NavBar;    // Змінено з bootstrap на bootstrap5
use yii\bootstrap5\Html;      // Використовуємо Html з bootstrap5 для кращої сумісності
use yii\bootstrap5\Breadcrumbs; // Змінено на bootstrap5
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <style>
        body,
        html {
            background-color: #121212 !important;
            color: #e0e0e0;
            height: 100%;
        }

        .wrap {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .content-container {
            flex: 1;
            /* Притискає футер до низу */
            padding-top: 70px;
        }

        /* Адаптація стилів під Bootstrap 5 (navbar-dark замість navbar-inverse) */
        .navbar-dark {
            background-color: #1f1f1f !important;
            border-bottom: 1px solid #333;
        }

        .navbar-dark .navbar-brand {
            color: #fff !important;
        }

        .navbar-dark .nav-link {
            color: #ccc !important;
        }

        .navbar-dark .nav-link:hover {
            color: #fff !important;
        }

        /* Стилі для кнопок виходу */
        .logout-btn {
            background: none;
            border: none;
            color: #ccc;
            padding: 8px;
            text-decoration: none;
        }

        .logout-btn:hover {
            color: #fff;
        }

        .footer {
            background-color: #000;
            border-top: 1px solid #333;
            margin-top: auto;
            /* BS5 спосіб притиснути футер */
        }

        /* Виправлення хлібних крихт для темної теми */
        .breadcrumb {
            background-color: transparent;
        }

        .breadcrumb-item.active {
            color: #999;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header id="header">
        <?php
        NavBar::begin([
            'brandLabel' => 'LifeHacks Dark',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-expand-md navbar-dark fixed-top',
            ],
        ]);

        // Формуємо пункти меню
        $menuItems = [
            ['label' => 'Home', 'url' => ['/site/index']],
        ];

        if (Yii::$app->user->isGuest) {
            // Якщо гість: показуємо і Реєстрацію, і Вхід
            $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
            $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
        } else {
            $menuItems[] = ['label' => 'My Profile (' . Yii::$app->user->identity->name . ')', 'url' => ['/profile/index']];

            // Кнопка Вихід
            $menuItems[] = '<li class="nav-item">'
                . Html::beginForm(['/site/logout'])
                . Html::submitButton(
                    'Logout',
                    ['class' => 'nav-link logout-btn', 'style' => 'color: #ff6b6b;'] // Червоний колір для виходу
                )
                . Html::endForm()
                . '</li>';
        }

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav ms-auto'],
            'items' => $menuItems,
        ]);

        NavBar::end();
        ?>
    </header>

    <main id="main" class="flex-shrink-0 content-container" role="main">
        <div class="container">
            <?php if (!empty($this->params['breadcrumbs'])): ?>
                <?= Breadcrumbs::widget([
                    'links' => $this->params['breadcrumbs'],
                    'options' => ['class' => 'breadcrumb my-3'], // my-3 додає відступи
                ]) ?>
            <?php endif ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer id="footer" class="footer mt-auto py-3 text-muted">
        <div class="container">
            <div class="row text-muted">
                <div class="col-md-6 text-center text-md-start">&copy; LifeHacks App <?= date('Y') ?></div>
                <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>