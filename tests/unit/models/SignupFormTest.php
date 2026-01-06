<?php

namespace tests\unit\models;

use app\models\SignupForm;
use app\models\User;

/**
 * Unit tests for SignupForm model.
 */
class SignupFormTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    // Clear users table before each test
    User::deleteAll();
  }

  /**
   * Test validation rules: required fields, password length, email format.
   */
  public function testValidation()
  {
    $model = new SignupForm();

    // 1. Empty fields
    $this->assertFalse($model->validate(), 'Empty model should not be valid');
    $this->assertArrayHasKey('name', $model->errors);
    $this->assertArrayHasKey('email', $model->errors);
    $this->assertArrayHasKey('password', $model->errors);

    // 2. Too short password
    $model->password = '123';
    $this->assertFalse($model->validate(['password']), 'Password must be at least 6 characters');

    // 3. Invalid email format
    $model->email = 'not-email';
    $this->assertFalse($model->validate(['email']), 'Email should be valid');
  }

  /**
   * Test successful signup.
   */
  public function testCorrectSignup()
  {
    $model = new SignupForm([
      'name' => 'New User',
      'email' => 'newuser@example.com',
      'password' => 'secret_password',
    ]);

    // Validation should pass
    $this->assertTrue($model->validate(), 'Model should be valid with correct data');

    // Perform signup
    $user = $model->signup();

    // Returned object should be User
    $this->assertNotNull($user);
    $this->assertInstanceOf(User::class, $user);

    // Check saved data
    $this->assertEquals('New User', $user->name);
    $this->assertEquals('newuser@example.com', $user->email);
    $this->assertEquals('secret_password', $user->password); // depends on whether signup hashes password
    $this->assertEquals(0, $user->isAdmin);
    $this->assertEquals('default.jpg', $user->image);

    // Confirm record exists in DB
    $this->tester->seeRecord(User::class, ['email' => 'newuser@example.com']);
  }

  /**
   * Test that duplicate emails are rejected.
   */
  public function testDuplicateEmail()
  {
    // Create first user
    $firstUser = new User([
      'name' => 'First User',
      'email' => 'duplicate@example.com',
      'password' => '123456',
    ]);
    $firstUser->save(false);

    // Attempt to register another with same email
    $model = new SignupForm([
      'name' => 'Second User',
      'email' => 'duplicate@example.com',
      'password' => 'new_password',
    ]);

    // Validation should fail
    $this->assertFalse($model->validate(), 'Duplicate email should not be allowed');
    $this->assertArrayHasKey('email', $model->errors);
    $this->assertEquals('This email is already taken.', $model->errors['email'][0]);
  }
}
