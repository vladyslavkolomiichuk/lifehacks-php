<?php

use app\models\User;

class LoginCest
{
  // Метод виконується перед кожним тестом
  public function _before(FunctionalTester $I)
  {
    // 1. Повне очищення для ізоляції тесту
    User::deleteAll();

    // 2. Створюємо тестового користувача
    $user = new User();
    $user->name = 'Test User';
    $user->email = 'test@example.com';
    $user->setPassword('password123');
    $user->isAdmin = 0;
    $user->save(false);
  }

  // ТЕСТ 1: Перевірка відкриття сторінки
  public function openLoginPage(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=auth/login');

    // Перевіряємо текст, який специфічний саме для форми, а не для меню
    $I->see('Please fill out the following fields to login:');

    // Перевіряємо наявність форми та полів за точними селекторами
    $I->seeElement('#login-form');
    $I->seeElement('input', ['name' => 'LoginForm[email]']);
    $I->seeElement('input', ['name' => 'LoginForm[password]']);
  }

  // ТЕСТ 2: Вхід з неправильним паролем
  public function loginWithWrongPassword(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=auth/login');

    // Використовуємо надійний метод заповнення через масив атрибутів
    $I->fillField(['name' => 'LoginForm[email]'], 'test@example.com');
    $I->fillField(['name' => 'LoginForm[password]'], 'wrong_pass');

    // Клікаємо на кнопку за її іменем з view
    $I->click('login-button');

    // Перевіряємо валідаційне повідомлення (має збігатися з моделлю LoginForm)
    $I->see('Incorrect email or password.');

    // Перевіряємо, що користувач залишився на сторінці логіну
    $I->seeElement('#login-form');
  }

  // ТЕСТ 3: Успішний вхід
  public function loginSuccessfully(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=auth/login');

    $I->fillField(['name' => 'LoginForm[email]'], 'test@example.com');
    $I->fillField(['name' => 'LoginForm[password]'], 'password123');

    $I->click('login-button');

    // Після успішного входу Yii редиректить на головну, де в меню має бути Logout
    $I->see('Logout');

    // Додаткова перевірка: форма логіну має зникнути
    $I->dontSeeElement('#login-form');
  }
}
