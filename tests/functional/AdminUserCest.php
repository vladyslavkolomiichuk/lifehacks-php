<?php

use app\models\User;

class AdminUserCest
{
  private $adminId;
  private $regularUserId;

  public function _before(FunctionalTester $I)
  {
    // 1. Очищення бази перед тестом
    User::deleteAll();

    // 2. Створюємо Адміна (Я)
    $admin = new User(['name' => 'Super Admin', 'email' => 'admin@test.com']);
    $admin->setPassword('123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // 3. Створюємо іншого користувача (Жертва)
    $user = new User(['name' => 'Simple User', 'email' => 'user@test.com']);
    $user->setPassword('123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;
  }

  // ТЕСТ 1: Перевірка безпеки
  public function checkAccessControl(FunctionalTester $I)
  {
    // Гість
    $I->amOnPage('/index-test.php?r=admin/user/index');
    $I->see('Login');

    // Звичайний юзер
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/user/index');
    $I->seeResponseCodeIs(403);
  }

  // ТЕСТ 2: Список користувачів
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/user/index');

    $I->see('Users', 'h1'); // Адаптовано під стандартний заголовок Gii
    $I->see('Super Admin');
    $I->see('Simple User');

    // Перевіряємо статуси/ролі (якщо у вас вони виводяться текстом)
    $I->see('Admin');
    $I->see('User');
  }

  // ТЕСТ 3: Редагування (Зміна ролі та імені)
  public function updateUser(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/user/update&id=' . $this->regularUserId);

    $I->see('Update User', 'h1');

    $I->fillField(['name' => 'User[name]'], 'Promoted User');
    $I->selectOption(['name' => 'User[isAdmin]'], '1');

    $I->click('button[type=submit]');

    // ПЕРЕВІРКА: замість URL перевіряємо контент сторінки списку
    $I->see('Users', 'h1');
    $I->see('Promoted User');

    $I->seeRecord(User::class, ['id' => $this->regularUserId, 'isAdmin' => 1]);
  }

  // ТЕСТ 4: Видалення іншого користувача
  public function deleteOtherUser(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    // Видаляємо через AJAX POST (найбільш надійно для GridView кнопок)
    $I->sendAjaxPostRequest('/index-test.php?r=admin/user/delete&id=' . $this->regularUserId);

    $I->amOnPage('/index-test.php?r=admin/user/index');
    $I->dontSee('Simple User');
    $I->dontSeeRecord(User::class, ['id' => $this->regularUserId]);
  }

  // ТЕСТ 5: Спроба самовидалення
  public function trySelfDelete(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->sendAjaxPostRequest('/index-test.php?r=admin/user/delete&id=' . $this->adminId);
    $I->amOnPage('/index-test.php?r=admin/user/index');

    // Просто перевіряємо, що запис нікуди не зник
    $I->seeRecord(User::class, ['id' => $this->adminId]);
    $I->see('Super Admin'); // Бачимо себе в списку
  }
}
