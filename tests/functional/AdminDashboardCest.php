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
    // 1. Повне очищення
    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // 2. Створюємо Адміна
    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com']);
    $admin->setPassword('123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // 3. Створюємо Звичайного юзера
    $user = new User(['name' => 'User', 'email' => 'user@test.com']);
    $user->setPassword('123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // 4. Створюємо Топік
    $topic = new Topic(['name' => 'Tech']);
    $topic->save(false);

    // 5. Створюємо Статтю
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
      'article_id' => $article->id,
      'date' => date('Y-m-d H:i:s')
    ]);
    $comment->save(false);
  }

  // ТЕСТ 1: Перевірка безпеки
  public function checkAccessControl(FunctionalTester $I)
  {
    // Гість
    $I->amOnPage('/index-test.php?r=admin/default/index');
    $I->see('Login');

    // Звичайний юзер
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/default/index');
    $I->seeResponseCodeIs(403);
  }

  // ТЕСТ 2: Перевірка статистики Dashboard
  public function checkDashboardStats(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/default/index');

    $I->see('Dashboard', 'h1');

    // 1. Перевіряємо цифри на картках (використовуємо ваш CSS клас)
    $I->see('2', '.stat-card-value'); // Users
    $I->see('1', '.stat-card-value'); // Articles
    $I->see('100', '.stat-card-value'); // Views

    // 2. Перевіряємо тексти карток, щоб переконатися, що вони на місці
    $I->see('Users', '.stat-card-title');
    $I->see('Articles', '.stat-card-title');
    $I->see('Comments', '.stat-card-title');

    // 3. Перевіряємо кнопки в блоці Quick Actions
    // Це найнадійніший спосіб перевірити навігацію в тесті
    $I->see('Add Topic', 'a');
    $I->see('Manage Admins', 'a');
    $I->see('Manage Articles', 'a');

    // Перевіряємо текст "View Details", який є у ваших картках
    $I->see('View Details', 'a');
  }
}
