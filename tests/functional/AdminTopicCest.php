<?php

use app\models\User;
use app\models\Topic;

class AdminTopicCest
{
  private $adminId;
  private $regularUserId;
  private $topicId;

  public function _before(FunctionalTester $I)
  {
    Topic::deleteAll();
    User::deleteAll();

    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com']);
    $admin->setPassword('123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    $user = new User(['name' => 'User', 'email' => 'user@test.com']);
    $user->setPassword('123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    $topic = new Topic(['name' => 'Existing Topic']);
    $topic->save(false);
    $this->topicId = $topic->id;
  }

  public function checkAccessControl(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=admin/topic/index');
    $I->see('Login');

    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/topic/index');
    $I->seeResponseCodeIs(403);
  }

  // ТЕСТ 2: Перегляд списку (Index)
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/topic/index');

    $I->see('Topics', 'h1');
    $I->see('Existing Topic');

    // ВИПРАВЛЕННЯ: замість seeElement з URL використовуємо seeLink з текстом.
    // Якщо у вас кнопка називається просто "Create", замініть текст.
    $I->seeLink('Create Topic');
  }

  // ТЕСТ 3: Створення категорії (Create)
  public function createTopic(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/topic/create');

    $I->see('Create Topic', 'h1');

    $I->fillField(['name' => 'Topic[name]'], 'New PHP Category');

    // ВИПРАВЛЕНО: Використовуємо тип кнопки submit, бо текст може бути "Create"
    $I->click('button[type=submit]');

    // ВИПРАВЛЕНО: Замість перевірки URL перевіряємо заголовок h1 списку після редиректу
    $I->see('Topics', 'h1');
    $I->see('New PHP Category');
    $I->seeRecord(Topic::class, ['name' => 'New PHP Category']);
  }

  // ТЕСТ 4: Редагування (Update)
  public function updateTopic(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/topic/update&id=' . $this->topicId);

    $I->see('Update Topic', 'h1');

    $I->fillField(['name' => 'Topic[name]'], 'Updated Topic Name');

    // ВИПРАВЛЕНО: Використовуємо тип кнопки submit
    $I->click('button[type=submit]');

    // ВИПРАВЛЕНО: Перевірка заголовка h1 замість URL
    $I->see('Topics', 'h1');
    $I->see('Updated Topic Name');
    $I->dontSee('Existing Topic');
  }

  public function deleteTopic(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    $I->sendAjaxPostRequest('/index-test.php?r=admin/topic/delete&id=' . $this->topicId);

    $I->amOnPage('/index-test.php?r=admin/topic/index');
    $I->dontSee('Existing Topic');
    $I->dontSeeRecord(Topic::class, ['id' => $this->topicId]);
  }
}
