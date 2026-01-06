<?php

namespace tests\unit\models;

use Yii;
use app\models\LoginForm;
use app\models\User;

/**
 * Unit tests for LoginForm model.
 */
class LoginFormTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  /**
   * Set up test user before each test.
   */
  protected function _before()
  {
    // Clean DB
    User::deleteAll();

    // Create a test user with known password
    $user = new User();
    $user->name = 'Test User';
    $user->email = 'demo@example.com';
    $user->password = Yii::$app->security->generatePasswordHash('demo123');
    $user->save(false);
  }

  /**
   * Clean up after each test.
   */
  protected function _after()
  {
    Yii::$app->user->logout();
  }

  /**
   * Test basic validation rules (required, email format).
   */
  public function testValidation()
  {
    $model = new LoginForm();

    // Empty fields
    $model->email = null;
    $model->password = null;
    $this->assertFalse($model->validate());
    $this->assertArrayHasKey('email', $model->errors);
    $this->assertArrayHasKey('password', $model->errors);

    // Invalid email format
    $model->email = 'invalid-email';
    $model->password = 'demo123';
    $this->assertFalse($model->validate(['email']));
  }

  /**
   * Test login with wrong password.
   */
  public function testLoginWrongPassword()
  {
    $model = new LoginForm([
      'email' => 'demo@example.com',
      'password' => 'wrong-password',
    ]);

    $this->assertFalse($model->login(), 'Should not login with wrong password');
    $this->assertArrayHasKey('password', $model->errors);
    $this->assertStringContainsString('Incorrect email or password', $model->errors['password'][0]);
    $this->assertTrue(Yii::$app->user->isGuest);
  }

  /**
   * Test login with non-existent email.
   */
  public function testLoginNonExistentUser()
  {
    $model = new LoginForm([
      'email' => 'nobody@example.com',
      'password' => 'demo123',
    ]);

    $this->assertFalse($model->login());
    $this->assertTrue(Yii::$app->user->isGuest);
  }

  /**
   * Test successful login.
   */
  public function testLoginCorrect()
  {
    $model = new LoginForm([
      'email' => 'demo@example.com',
      'password' => 'demo123',
    ]);

    $this->assertTrue($model->login(), 'User should login successfully');
    $this->assertEmpty($model->errors);
    $this->assertFalse(Yii::$app->user->isGuest);
    $this->assertEquals('demo@example.com', Yii::$app->user->identity->email);
  }
}
