<?php

namespace tests\unit\models;

use Yii;
use app\models\LoginForm;
use app\models\User;

class LoginFormTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  // Очищаємо базу і створюємо тестового юзера перед тестами
  protected function _before()
  {
    User::deleteAll();

    $user = new User();
    $user->name = 'Test User';
    $user->email = 'demo@example.com';
    // Генеруємо хеш для пароля 'demo123', щоб перевірка validatePassword пройшла успішно
    $user->password = Yii::$app->security->generatePasswordHash('demo123');
    $user->save(false);
  }

  protected function _after()
  {
    // Обов'язково виходимо з системи після тесту, щоб не впливати на інші тесты
    Yii::$app->user->logout();
  }

  // ТЕСТ 1: Перевірка стандартної валідації (формат email, required)
  public function testValidation()
  {
    $model = new LoginForm();

    // Сценарій 1: Порожні поля
    $model->email = null;
    $model->password = null;
    $this->assertFalse($model->validate(), 'Should fail with empty fields');
    $this->assertArrayHasKey('email', $model->errors);
    $this->assertArrayHasKey('password', $model->errors);

    // Сценарій 2: Некоректний формат Email
    $model->email = 'not-an-email';
    $model->password = 'demo123';
    $this->assertFalse($model->validate(['email']), 'Should fail with invalid email format');
  }

  // ТЕСТ 2: Спроба входу з неправильним паролем
  public function testLoginWrongPassword()
  {
    $model = new LoginForm([
      'email' => 'demo@example.com',
      'password' => 'wrong-password', // Невірний пароль
    ]);

    // Валідація має пройти (формат правильний), але login() має повернути false
    // АБО validate() поверне false через validatePassword (залежить від реалізації Yii validator)

    $this->assertFalse($model->login(), 'Admin should not be logged in with wrong password');

    // Перевіряємо, чи з'явилася помилка саме на полі password
    $this->assertArrayHasKey('password', $model->errors);
    $this->assertStringContainsString('Incorrect email or password', $model->errors['password'][0]);

    // Переконуємось, що система все ще вважає нас гостем
    $this->assertTrue(Yii::$app->user->isGuest);
  }

  // ТЕСТ 3: Спроба входу з неіснуючим email
  public function testLoginNonExistentUser()
  {
    $model = new LoginForm([
      'email' => 'nobody@example.com', // Такого юзера немає
      'password' => 'demo123',
    ]);

    $this->assertFalse($model->login());
    $this->assertTrue(Yii::$app->user->isGuest);
  }

  // ТЕСТ 4: Успішний вхід
  public function testLoginCorrect()
  {
    $model = new LoginForm([
      'email' => 'demo@example.com',
      'password' => 'demo123', // Вірний пароль
    ]);

    $this->assertTrue($model->login(), 'User should be logged in successfully');

    // Перевіряємо помилки (їх не має бути)
    $this->assertEmpty($model->errors, 'Should be no validation errors');

    // Перевіряємо статус Yii::$app->user
    $this->assertFalse(Yii::$app->user->isGuest, 'User should not be a guest anymore');
    $this->assertEquals('demo@example.com', Yii::$app->user->identity->email);
  }
}
