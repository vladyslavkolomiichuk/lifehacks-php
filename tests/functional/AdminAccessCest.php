<?php

use app\models\User;

class AdminAccessCest
{
  private $adminId;
  private $regularUserId;

  public function _before(FunctionalTester $I)
  {
    User::deleteAll();

    // 1. Створюємо Адміна (isAdmin = 1)
    $admin = new User();
    $admin->name = 'Admin';
    $admin->email = 'admin@test.com';
    $admin->setPassword('admin123');
    $admin->isAdmin = 1; // <--- ВАЖЛИВО
    $admin->save(false);
    $this->adminId = $admin->id;

    // 2. Створюємо звичайного юзера (isAdmin = 0)
    $user = new User();
    $user->name = 'Simple User';
    $user->email = 'user@test.com';
    $user->setPassword('user123');
    $user->isAdmin = 0; // <--- ВАЖЛИВО
    $user->save(false);
    $this->regularUserId = $user->id;
  }

  // СЦЕНАРІЙ 1: Гість (не залогінений) пробує зайти в адмінку
  public function guestTryToAccessAdmin(FunctionalTester $I)
  {
    // Припускаємо, що маршрут адмінки '/admin' або '/admin/index'
    // Змініть це на ваш реальний URL
    $I->amOnPage(['/admin/index']);

    // Його має перекинути на логін
    $I->see('Login');
    $I->dontSee('Dashboard'); // Не має бачити контент адмінки
  }

  // СЦЕНАРІЙ 2: Звичайний юзер пробує зайти
  public function regularUserTryToAccessAdmin(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage(['/admin/index']);

    // Тут залежить від вашої реалізації:
    // АБО він бачить 403 Forbidden
    // АБО його кидає на головну
    // АБО його кидає на логін

    // Найчастіше в Yii2 це помилка 403
    $I->seeResponseCodeIs(403);
    // Або перевірка тексту
    $I->see('Forbidden');
  }

  // СЦЕНАРІЙ 3: Адмін заходить
  public function adminAccess(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage(['/admin/index']);

    $I->seeResponseCodeIs(200);
    // Перевіряємо текст, який є тільки в адмінці
    // Наприклад, заголовок "Control Panel" або "Manage Articles"
    $I->see('Admin');
  }
}
