<?php

use app\models\User;

class AdminAccessCest
{
  private $adminId;
  private $regularUserId;

  public function _before(FunctionalTester $I)
  {
    // Очищаємо базу перед кожним тестом
    User::deleteAll();

    // 1. Створюємо Адміна (isAdmin = 1)
    $admin = new User();
    $admin->name = 'Admin';
    $admin->email = 'admin@test.com';
    $admin->setPassword('admin123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // 2. Створюємо звичайного юзера (isAdmin = 0)
    $user = new User();
    $user->name = 'Simple User';
    $user->email = 'user@test.com';
    $user->setPassword('user123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;
  }

  // СЦЕНАРІЙ 1: Гість (не залогінений) пробує зайти в адмінку
  public function guestTryToAccessAdmin(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=admin/default/index');

    // Варіант 1: Шукаємо частину рядка в URL без спецсимволів
    $I->seeInCurrentUrl('auth');
    $I->seeInCurrentUrl('login');

    // Варіант 2: Перевіряємо заголовок форми, що є 100% доказом того, що ми на логіні
    $I->see('Login', 'h3');
    $I->see('Please fill out the following fields to login:');
  }

  // СЦЕНАРІЙ 2: Звичайний юзер пробує зайти в адмінку
  public function regularUserTryToAccessAdmin(FunctionalTester $I)
  {
    // amLoggedInAs автоматично знаходить юзера в БД за ID і логінить його в сесію
    $I->amLoggedInAs($this->regularUserId);

    $I->amOnPage('/index-test.php?r=admin/default/index');

    // Якщо у вас налаштовано RBAC або AccessControl (matchCallback),
    // то при спробі доступу без прав isAdmin=1 зазвичай повертається 403 помилка.
    $I->seeResponseCodeIs(403);

    // Перевіряємо текст стандартної помилки Yii2
    $I->see('Forbidden');
    $I->dontSee('Admin Panel');
  }

  // СЦЕНАРІЙ 3: Адмін успішно заходить
  public function adminAccess(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    $I->amOnPage('/index-test.php?r=admin/default/index');

    // Перевіряємо успішний статус
    $I->seeResponseCodeIs(200);

    // Перевіряємо наявність специфічного тексту адмінки (наприклад, заголовок)
    $I->see('Admin');
    $I->dontSee('Please fill out the following fields to login:');
  }
}
