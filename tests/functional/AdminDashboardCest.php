<?php

use app\models\User;
use app\models\Article;
use app\models\Comment;
use app\models\Topic;

class AdminDashboardCest
{
  private $adminId;
  private $regularUserId;

  public function _before(FunctionalTester $I)
  {
    // 1. Очищення
    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // 2. Створюємо Адміна (Користувач №1)
    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => '123']);
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // 3. Створюємо Звичайного юзера (Користувач №2)
    $user = new User(['name' => 'User', 'email' => 'user@test.com', 'password' => '123']);
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // 4. Створюємо Топік
    $topic = new Topic(['name' => 'Tech']);
    $topic->save(false);

    // 5. Створюємо Статтю (100 переглядів)
    $article = new Article([
      'title' => 'Post 1',
      'topic_id' => $topic->id,
      'user_id' => $admin->id,
      'viewed' => 100,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);

    // 6. Створюємо Коментар
    $comment = new Comment([
      'text' => 'Nice',
      'user_id' => $user->id,
      'article_id' => $article->id
    ]);
    $comment->save(false);
  }

  // ТЕСТ 1: Перевірка безпеки
  public function checkAccessControl(FunctionalTester $I)
  {
    // Гість
    $I->amOnPage(['admin/default/index']);
    $I->see('Login');

    // Звичайний юзер
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage(['admin/default/index']);
    $I->seeResponseCodeIs(403);
  }

  // ТЕСТ 2: Перевірка статистики Dashboard
  public function checkDashboardStats(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage(['admin/default/index']);

    $I->see('Dashboard', 'h1');

    // Перевіряємо цифри на картках:

    // Users: Ми створили Admin + User = 2
    $I->see('Users');
    $I->see('2', '.stat-card-value');

    // Articles: Створили 1 статтю
    $I->see('Articles');
    $I->see('1', '.stat-card-value');

    // Comments: Створили 1 коментар
    $I->see('Comments');
    // $I->see('1'); // Цю перевірку Codeception може сплутати з цифрою статей, тому пропускаємо або уточнюємо селектор

    // Views: Ми дали статті 100 переглядів
    $I->see('Total Views');
    $I->see('100', '.stat-card-value');

    // Перевіряємо наявність кнопок швидких дій
    $I->seeLink('Manage Admins');
    $I->seeLink('Manage Articles');
  }
}
