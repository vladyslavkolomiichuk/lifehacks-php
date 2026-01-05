<?php

use app\models\User;

class SignupCest
{
  // Очищаємо базу перед кожним тестом, щоб уникнути помилок "Email вже зайнятий"
  public function _before(FunctionalTester $I)
  {
    User::deleteAll();
  }

  // ТЕСТ 1: Чи відкривається сторінка
  public function openSignupPage(FunctionalTester $I)
  {
    // Йдемо на сторінку реєстрації
    $I->amOnPage(['auth/signup']);

    // Перевіряємо заголовок h3 з вашого view
    $I->see('Signup', 'h3');

    // Перевіряємо наявність полів (Yii генерує імена на основі назви моделі)
    $I->seeElement('input', ['name' => 'SignupForm[name]']);
    $I->seeElement('input', ['name' => 'SignupForm[email]']);
    $I->seeElement('input', ['name' => 'SignupForm[password]']);
  }

  // ТЕСТ 2: Спроба відправити порожню форму (Валідація)
  public function signupWithEmptyFields(FunctionalTester $I)
  {
    $I->amOnPage(['auth/signup']);

    // Відправляємо порожню форму
    $I->submitForm('#form-signup', []);

    // Очікуємо побачити стандартні помилки Yii
    $I->see('Name cannot be blank');
    $I->see('Email cannot be blank');
    $I->see('Password cannot be blank');

    // Переконуємось, що користувач не створився
    $I->dontSeeRecord(User::class, ['email' => 'test@example.com']);
  }

  // ТЕСТ 3: Успішна реєстрація
  public function signupSuccessfully(FunctionalTester $I)
  {
    $I->amOnPage(['auth/signup']);

    // Заповнюємо форму даними
    $formData = [
      'SignupForm[name]' => 'Test User',
      'SignupForm[email]' => 'newuser@test.com',
      'SignupForm[password]' => 'password123',
    ];

    // Відправляємо форму (використовуємо ID форми з вашого View)
    $I->submitForm('#form-signup', $formData);

    // 1. Перевіряємо БД: чи з'явився такий юзер?
    // Примітка: перевіряємо по email, який ми ввели
    $I->seeRecord(User::class, [
      'email' => 'newuser@test.com',
      'name' => 'Test User'
    ]);

    // 2. Перевіряємо перенаправлення (Redirect)
    // У вашому контролері AuthController::actionSignup написано: return $this->redirect(['login']);
    // Тому ми очікуємо опинитися на сторінці Login
    $I->see('Login', 'h3');
    $I->seeCurrentUrlEquals('/index-test.php/auth/login'); // URL може відрізнятись залежно від налаштувань, це опціональна перевірка
  }
}
