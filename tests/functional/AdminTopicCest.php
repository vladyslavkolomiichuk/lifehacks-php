<?php

use app\models\User;
use app\models\Topic;

class AdminTopicCest
{
  private $adminId;
  private $regularUserId;
  private $topicId; // ID тестової категорії для редагування/видалення

  public function _before(FunctionalTester $I)
  {
    // 1. Очищення бази
    Topic::deleteAll();
    User::deleteAll();

    // 2. Створюємо Адміна
    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => '123']);
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // 3. Створюємо звичайного юзера
    $user = new User(['name' => 'User', 'email' => 'user@test.com', 'password' => '123']);
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // 4. Створюємо існуючу категорію для тестів Index/Update/Delete
    $topic = new Topic(['name' => 'Existing Topic']);
    $topic->save(false);
    $this->topicId = $topic->id;
  }

  // ТЕСТ 1: Перевірка безпеки
  public function checkAccessControl(FunctionalTester $I)
  {
    // Гість -> Логін
    $I->amOnPage(['admin/topic/index']);
    $I->see('Login');

    // Звичайний юзер -> Forbidden
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage(['admin/topic/index']);
    $I->seeResponseCodeIs(403);
  }

  // ТЕСТ 2: Перегляд списку (Index)
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage(['admin/topic/index']);

    $I->see('Topics', 'h1');
    $I->see('Existing Topic'); // Бачимо ту, що створили в _before
    $I->seeLink('Create Topic');
  }

  // ТЕСТ 3: Створення категорії (Create)
  public function createTopic(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage(['admin/topic/create']);

    $I->see('Create Topic', 'h1');

    // Заповнюємо форму (використовуємо клас .dark-form з вашого view)
    $I->submitForm('.dark-form', [
      'Topic[name]' => 'New PHP Category',
    ]);

    // Має перекинути на index
    $I->seeCurrentUrlMatches('~admin/topic/index~');

    // Перевіряємо, чи з'явився запис
    $I->see('New PHP Category');
    $I->seeRecord(Topic::class, ['name' => 'New PHP Category']);
  }

  // ТЕСТ 4: Редагування (Update)
  public function updateTopic(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage(['admin/topic/update', 'id' => $this->topicId]);

    $I->see('Update Topic: Existing Topic', 'h1');

    // Змінюємо назву
    $I->submitForm('.dark-form', [
      'Topic[name]' => 'Updated Topic Name',
    ]);

    // Перевіряємо результат
    $I->seeCurrentUrlMatches('~admin/topic/index~');
    $I->see('Updated Topic Name');
    $I->dontSee('Existing Topic'); // Старої назви не має бути
  }

  // ТЕСТ 5: Видалення (Delete)
  public function deleteTopic(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    // Відправляємо POST на видалення
    $I->sendPost(['admin/topic/delete', 'id' => $this->topicId]);

    // Перевіряємо
    $I->amOnPage(['admin/topic/index']);
    $I->dontSee('Existing Topic');
    $I->dontSeeRecord(Topic::class, ['id' => $this->topicId]);
  }
}
