<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
// use app\widgets\Alert; // Вимкнули старий віджет
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Html;
use yii\bootstrap5\Breadcrumbs;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            padding-top: 70px;
        }

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
        }

        .breadcrumb {
            background-color: transparent;
        }

        .breadcrumb-item.active {
            color: #999;
        }

        /* Стиль для Toasts (щоб текст був читабельний) */
        div:where(.swal2-container) h2:where(.swal2-title) {
            color: #fff !important;
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

        $menuItems = [
            ['label' => 'Home', 'url' => ['article/index']],
        ];

        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin == 1) {
            $menuItems[] = [
                'label' => '<i class="bi bi-speedometer2"></i> Admin Panel', // Додали іконку (спідометр)
                'url' => ['/admin/default/index'], // Посилання на модуль адмінки
                'encode' => false, // Дозволяємо HTML (для іконки)
                'linkOptions' => ['class' => 'nav-link', 'style' => 'color: #ffca28; font-weight: bold;'] // Жовтий колір
            ];
        }

        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Signup', 'url' => ['auth/signup']];
            $menuItems[] = ['label' => 'Login', 'url' => ['auth/login']];
        } else {
            $menuItems[] = ['label' => 'My Profile (' . Yii::$app->user->identity->name . ')', 'url' => ['profile/index']];

            $menuItems[] = '<li class="nav-item">'
                . Html::beginForm(['auth/logout'])
                . Html::submitButton(
                    'Logout',
                    ['class' => 'nav-link logout-btn', 'style' => 'color: #ff6b6b;']
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
                    'options' => ['class' => 'breadcrumb my-3'],
                ]) ?>
            <?php endif ?>

            <?= $content ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const shareButtons = document.querySelectorAll('.btn-share');

            shareButtons.forEach(button => {
                button.addEventListener('click', async (e) => {
                    e.preventDefault();

                    const title = button.getAttribute('data-title');
                    const url = button.getAttribute('data-url');
                    const text = 'Check out this helpful tip on LifeHacks!';

                    const showSuccess = () => {
                        const originalContent = button.innerHTML;
                        const originalColor = button.style.color;

                        button.innerHTML = '<i class="glyphicon glyphicon-ok"></i> Copied!';
                        button.style.color = '#03dac6';

                        setTimeout(() => {
                            button.innerHTML = originalContent;
                            button.style.color = originalColor;
                        }, 2000);
                    };

                    if (navigator.share) {
                        try {
                            await navigator.share({
                                title,
                                text,
                                url
                            });
                        } catch (err) {
                            console.log('Error sharing:', err);
                        }
                    } else {
                        if (navigator.clipboard && window.isSecureContext) {
                            try {
                                await navigator.clipboard.writeText(url);
                                showSuccess();
                            } catch (err) {
                                fallbackCopyTextToClipboard(url);
                            }
                        } else {
                            fallbackCopyTextToClipboard(url);
                        }
                    }

                    function fallbackCopyTextToClipboard(text) {
                        var textArea = document.createElement("textarea");
                        textArea.value = text;
                        textArea.style.position = "fixed";
                        textArea.style.left = "-9999px";
                        textArea.style.top = "0";
                        document.body.appendChild(textArea);
                        textArea.focus();
                        textArea.select();
                        try {
                            var successful = document.execCommand('copy');
                            if (successful) showSuccess();
                            else alert('Unable to copy link manually.');
                        } catch (err) {
                            alert('Could not copy link: ' + text);
                        }
                        document.body.removeChild(textArea);
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php
    // Отримуємо всі повідомлення з сесії
    $flashes = Yii::$app->session->getAllFlashes();
    if (!empty($flashes)) {
        // Базові налаштування
        $toastConfig = [
            'toast' => true,
            'position' => 'bottom-end',
            'showConfirmButton' => false,
            'timer' => 3000,
            'timerProgressBar' => true,
            'background' => '#333', // Темний фон
            'color' => '#fff',      // Білий текст
            'didOpen' => 'js:function(toast) {
                toast.addEventListener("mouseenter", Swal.stopTimer)
                toast.addEventListener("mouseleave", Swal.resumeTimer)
            }'
        ];

        foreach ($flashes as $type => $message) {
            // Конвертація типів Yii в типи SweetAlert
            $icon = 'info';
            if ($type === 'success') $icon = 'success';
            if ($type === 'danger' || $type === 'error') $icon = 'error';
            if ($type === 'warning') $icon = 'warning';

            // Формуємо налаштування для конкретного повідомлення
            $jsConfig = \yii\helpers\Json::encode(array_merge($toastConfig, [
                'icon' => $icon,
                'title' => $message
            ]));

            // Реєструємо JS код, який запуститься після завантаження сторінки
            $this->registerJs("Swal.fire($jsConfig);");
        }
    }
    ?>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>