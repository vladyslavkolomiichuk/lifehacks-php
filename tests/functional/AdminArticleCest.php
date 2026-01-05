<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;

class AdminArticleCest
{
  private $adminId;
  private $regularUserId;
  private $topicId;
  private $articleId;

  public function _before(FunctionalTester $I)
  {
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com']);
    $admin->setPassword('123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    $user = new User(['name' => 'Simple', 'email' => 'user@test.com']);
    $user->setPassword('123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    $topic = new Topic(['name' => 'News']);
    $topic->save(false);
    $this->topicId = $topic->id;

    $article = new Article([
      'title' => 'Existing Article',
      'description' => 'Desc',
      'topic_id' => $topic->id,
      'user_id' => $admin->id,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);
    $this->articleId = $article->id;
  }

  // ТЕСТ 1: Перевірка безпеки
  public function checkAccessControl(FunctionalTester $I)
  {
    // Сценарій А: Гість
    $I->amOnPage('/index-test.php?r=admin/article/index');
    $I->see('Login');

    // Сценарій Б: Звичайний юзер
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/article/index');
    $I->seeResponseCodeIs(403);
  }

  // ТЕСТ 2: Список статей
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/article/index');

    // Перевіряємо текст, який точно є в адмінці (наприклад, заголовки GridView)
    $I->see('Articles', 'h1');
    $I->see('Existing Article');
    $I->see('News');
  }

  // ТЕСТ 3: Створення статті
  public function createArticle(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/article/create');

    $I->see('Create Article', 'h1');

    $I->fillField(['name' => 'Article[title]'], 'New Admin Article');
    $I->fillField(['name' => 'Article[description]'], 'Content created by admin');

    // Використовуємо selectOption для ТЕМ (Topics)
    $I->selectOption(['name' => 'Article[topic_id]'], (string)$this->topicId);

    // ВИПРАВЛЕНО: Використовуємо selectOption для АВТОРА (Users)
    $I->selectOption(['name' => 'Article[user_id]'], (string)$this->adminId);

    $I->click('button[type=submit]');

    $I->see('New Admin Article', 'h1');
    $I->seeRecord(Article::class, ['title' => 'New Admin Article']);
  }

  // ТЕСТ 4: Редагування
  public function updateArticle(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/article/update&id=' . $this->articleId);

    $I->fillField(['name' => 'Article[title]'], 'Updated Title By Admin');

    // Тут також краще використати селектор типу
    $I->click('button[type=submit]');

    $I->see('Updated Title By Admin', 'h1');
  }

  // ТЕСТ 5: Видалення
  public function deleteArticle(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    // У Yii2 видалення часто захищене VerbFilter (тільки POST)
    // Використовуємо вбудований метод відправки POST запиту
    $I->sendAjaxPostRequest('/index-test.php?r=admin/article/delete&id=' . $this->articleId);

    $I->amOnPage('/index-test.php?r=admin/article/index');
    $I->dontSee('Existing Article');
    // Перевірка в БД, що запис зник
    $I->dontSeeRecord(Article::class, ['id' => $this->articleId]);
  }
}
