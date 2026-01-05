<?php

use app\models\User;

class AdminUserCest
{
  private $adminId;
  private $regularUserId;

  public function _before(FunctionalTester $I)
  {
    // 1. Очищення
    User::deleteAll();

    // 2. Створюємо Адміна (Я)
    $admin = new User(['name' => 'Super Admin', 'email' => 'admin@test.com', 'password' => '123']);
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // 3. Створюємо іншого користувача (Жертва)
    $user = new User(['name' => 'Simple User', 'email' => 'user@test.com', 'password' => '123']);
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;
  }

  // ТЕСТ 1: Перевірка безпеки
  public function checkAccessControl(FunctionalTester $I)
  {
    // Гість
    $I->amOnPage(['admin/user/index']);
    $I->see('Login');

    // Звичайний юзер
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage(['admin/user/index']);
    $I->seeResponseCodeIs(403);
  }

  // ТЕСТ 2: Список користувачів
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage(['admin/user/index']);

    $I->see('Users Manager', 'h1');
    $I->see('Super Admin');
    $I->see('Simple User');
    // Перевіряємо бейджі
    $I->see('Admin', '.badge');
    $I->see('User', '.badge');
  }

  // ТЕСТ 3: Редагування (Зміна ролі та імені)
  public function updateUser(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage(['admin/user/update', 'id' => $this->regularUserId]);

    $I->see('Update User: Simple User', 'h1');

    // Змінюємо ім'я та даємо адмінку
    $I->submitForm('.dark-form', [
      'User[name]' => 'Promoted User',
      'User[isAdmin]' => 1, // Робимо адміном
    ]);

    $I->seeCurrentUrlMatches('~admin/user/index~');
    $I->see('User updated successfully.');

    // Перевіряємо, чи змінилось ім'я у списку
    $I->see('Promoted User');

    // Перевіряємо в базі, чи став він адміном
    $I->seeRecord(User::class, ['id' => $this->regularUserId, 'isAdmin' => 1]);
  }

  // ТЕСТ 4: Видалення іншого користувача
  public function deleteOtherUser(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    // Видаляємо Simple User
    $I->sendPost(['admin/user/delete', 'id' => $this->regularUserId]);

    $I->amOnPage(['admin/user/index']);
    $I->see('User deleted.');
    $I->dontSee('Simple User');
  }

  // ТЕСТ 5: Спроба самовидалення (Self-Delete Protection)
  public function trySelfDelete(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    // Пробуємо видалити себе (adminId)
    $I->sendPost(['admin/user/delete', 'id' => $this->adminId]);

    // Маємо залишитись на сторінці index
    $I->amOnPage(['admin/user/index']);

    // Маємо побачити повідомлення про помилку (Flash error)
    $I->see('You cannot delete yourself!');

    // Адмін все ще має існувати в базі
    $I->seeRecord(User::class, ['id' => $this->adminId]);
  }
}
