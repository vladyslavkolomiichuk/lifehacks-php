<?php

namespace tests\unit\models;

use app\models\SignupForm;
use app\models\User;

class SignupFormTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    // Очищаємо таблицю користувачів перед кожним тестом
    User::deleteAll();
  }

  // ТЕСТ 1: Перевірка валідації (некоректні дані)
  public function testValidation()
  {
    $model = new SignupForm();

    // Сценарій 1: Порожні поля
    $this->assertFalse($model->validate(), 'Empty model should not be valid');
    $this->assertArrayHasKey('name', $model->errors);
    $this->assertArrayHasKey('email', $model->errors);
    $this->assertArrayHasKey('password', $model->errors);

    // Сценарій 2: Короткий пароль (< 6 символів)
    $model->password = '123';
    $this->assertFalse($model->validate(['password']), 'Password should be min 6 chars');

    // Сценарій 3: Некоректний Email
    $model->email = 'not-email';
    $this->assertFalse($model->validate(['email']), 'Email should be valid');
  }

  // ТЕСТ 2: Успішна реєстрація
  public function testCorrectSignup()
  {
    $model = new SignupForm([
      'name' => 'New User',
      'email' => 'newuser@example.com',
      'password' => 'secret_password',
    ]);

    // 1. Перевіряємо, чи валідація проходить
    $this->assertTrue($model->validate(), 'Model should be valid with correct data');

    // 2. Викликаємо метод signup()
    $user = $model->signup();

    // 3. Перевіряємо, чи повернувся об'єкт User
    $this->assertNotNull($user, 'Signup should return user object');
    $this->assertInstanceOf(User::class, $user);

    // 4. Перевіряємо, чи записались дані в базу
    // ВАЖЛИВО: У вашому коді ви записуєте $this->email у $user->email
    $this->assertEquals('newuser@example.com', $user->email);
    $this->assertEquals('New User', $user->name);

    // Перевіряємо пароль (у вашому коді він не хешується)
    $this->assertEquals('secret_password', $user->password);

    // Перевіряємо дефолтні значення
    $this->assertEquals('default.jpg', $user->image);
    $this->assertEquals(0, $user->isAdmin);

    // Переконуємось, що запис є в БД
    $this->tester->seeRecord(User::class, ['email' => 'newuser@example.com']);
  }

  // ТЕСТ 3: Перевірка унікальності Email
  public function testDuplicateEmail()
  {
    // Крок 1: Створюємо першого користувача
    $firstUser = new User();
    $firstUser->name = 'First';
    // Увага: валідатор у вашій формі перевіряє колонку 'email', 
    // тому для тесту ми мусимо заповнити 'email' в User, якщо така колонка є.
    // Якщо у User є тільки 'login', то валідатор у SignupForm треба виправити (див. примітку нижче).
    // Припускаємо, що в User є поле email, яке перевіряє унікальність.
    $firstUser->email = 'duplicate@example.com';
    $firstUser->password = '123456';
    $firstUser->save(false);

    // Крок 2: Пробуємо зареєструвати другого з тим самим email
    $model = new SignupForm([
      'name' => 'Second User',
      'email' => 'duplicate@example.com', // Той самий email
      'password' => 'new_password',
    ]);

    // Очікуємо помилку валідації
    $this->assertFalse($model->validate(), 'Should not allow duplicate email');
    $this->assertArrayHasKey('email', $model->errors);
    $this->assertEquals('Ця пошта вже зайнята.', $model->errors['email'][0]);
  }
}
