<?php

namespace tests\unit\models;

use app\models\User;
use app\models\Article;
use Yii;

class UserTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    // Clear DB before each test
    Article::deleteAll();
    User::deleteAll();
  }

  // TEST 1: Validation (Required fields, Unique email)
  public function testValidation()
  {
    $user = new User();

    // Scenario 1: Empty fields (should fail)
    $this->assertFalse($user->validate(), 'User should not be valid with empty fields');
    $this->assertArrayHasKey('name', $user->errors, 'Name is required');
    $this->assertArrayHasKey('email', $user->errors, 'Email is required');

    // Scenario 2: Short password (should fail)
    $user->password = '123';
    $this->assertFalse($user->validate(['password']), 'Password must be at least 6 chars');

    // Scenario 3: Valid data (should pass)
    $user->name = 'Valid User';
    $user->email = 'valid@test.com';
    $user->password = 'secret123';
    $this->assertTrue($user->validate(), 'User should be valid with correct data');

    // Scenario 4: Unique email
    $user->save(false);

    $duplicateUser = new User();
    $duplicateUser->name = 'Duplicate';
    $duplicateUser->email = 'valid@test.com'; // same email
    $duplicateUser->password = 'password';

    $this->assertFalse($duplicateUser->validate(['email']), 'Email must be unique');
    $this->assertArrayHasKey('email', $duplicateUser->errors);
  }

  // TEST 2: Password hashing and verification
  public function testPasswordLogic()
  {
    $user = new User();

    $rawPassword = 'my_super_secret_password';
    $user->setPassword($rawPassword);

    $this->assertNotEquals($rawPassword, $user->password, 'Password should be hashed');
    $this->assertNotEmpty($user->password, 'Hash should be set');

    $this->assertTrue($user->validatePassword($rawPassword), 'Correct password should validate');
    $this->assertFalse($user->validatePassword('wrong_password'), 'Wrong password should fail');
  }

  // TEST 3: Identity interface methods
  public function testIdentityMethods()
  {
    $user = new User();
    $user->name = 'Identity Tester';
    $user->email = 'identity@test.com';
    $user->setPassword('123456');
    $user->save(false);

    // findIdentity by ID
    $foundUser = User::findIdentity($user->id);
    $this->assertNotNull($foundUser);
    $this->assertEquals($user->email, $foundUser->email);

    // findByUsername by email
    $foundByEmail = User::findByUsername('identity@test.com');
    $this->assertNotNull($foundByEmail);
    $this->assertEquals($user->id, $foundByEmail->id);

    // non-existent user
    $this->assertNull(User::findByUsername('nobody@test.com'));

    // getId
    $this->assertEquals($user->id, $user->getId());
  }

  // TEST 4: getThumb() logic
  public function testGetThumb()
  {
    $user = new User();

    // No image
    $user->image = null;
    $this->assertEquals('/uploads/no-image.png', $user->getThumb());

    // Image set but file missing
    $user->image = 'non_existent_avatar.jpg';
    $this->assertEquals('/uploads/no-image.png', $user->getThumb());
  }

  // TEST 5: Relations
  public function testRelations()
  {
    $user = new User(['name' => 'Author', 'email' => 'author@test.com']);
    $user->save(false);

    $article = new Article();
    $article->title = 'My Post';
    $article->user_id = $user->id;
    $article->save(false);

    // Check relation
    $this->assertNotEmpty($user->articles);
    $this->assertEquals('My Post', $user->articles[0]->title);
  }
}
