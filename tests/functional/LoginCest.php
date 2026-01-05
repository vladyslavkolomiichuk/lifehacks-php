<?php

use app\models\User;

class LoginCest
{
  // Метод виконується перед кожним тестом
  public function _before(FunctionalTester $I)
  {
    // 1. Очищаємо таблицю юзерів
    User::deleteAll();

    // 2. Створюємо тестового користувача, під яким будемо заходити
    $user = new User();
    $user->name = 'Test User';
    $user->email = 'test@example.com';
    $user->setPassword('password123'); // Хешуємо пароль
    $user->isAdmin = 0;
    $user->save(false);
  }

  // ТЕСТ 1: Чи відкривається сторінка входу
  public function openLoginPage(FunctionalTester $I)
  {
    // Заходимо на маршрут AuthController -> actionLogin
    $I->amOnPage(['auth/login']);

    // Перевіряємо заголовок (з вашого view файлу)
    $I->see('Login', 'h3');

    // Перевіряємо наявність полів
    $I->seeElement('input', ['name' => 'LoginForm[email]']);
    $I->seeElement('input', ['name' => 'LoginForm[password]']);
  }

  // ТЕСТ 2: Вхід з НЕПРАВИЛЬНИМ паролем
  public function loginWithWrongPassword(FunctionalTester $I)
  {
    $I->amOnPage(['auth/login']);

    // Заповнюємо форму
    $I->submitForm('#login-form', [
      'LoginForm[email]' => 'test@example.com',
      'LoginForm[password]' => 'wrong_pass',
    ]);

    // Очікуємо побачити помилку (текст з LoginForm::validatePassword)
    $I->see('Incorrect email or password.');

    // Ми все ще повинні бути на сторінці логіну (бачимо форму)
    $I->seeElement('#login-form');
  }

  // ТЕСТ 3: Вхід з ПРАВИЛЬНИМ паролем
  public function loginSuccessfully(FunctionalTester $I)
  {
    $I->amOnPage(['auth/login']);

    // Заповнюємо форму правильними даними
    $I->submitForm('#login-form', [
      'LoginForm[email]' => 'test@example.com',
      'LoginForm[password]' => 'password123',
    ]);

    // Після успішного входу нас перекидає на головну (goHome/goBack)
    // Перевіряємо, що ми більше НЕ бачимо форми логіну
    $I->dontSeeElement('#login-form');

    // Перевіряємо, що ми залогінені (зазвичай з'являється кнопка Logout)
    // Примітка: Цей рядок спрацює, якщо у вашому layout (main.php) є слово "Logout"
    $I->see('Logout');
  }
}
