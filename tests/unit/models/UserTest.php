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
    // Clean up database before each test
    Article::deleteAll();
    User::deleteAll();
  }

  // TEST 1: Validation Rules (Required fields, Unique email)
  public function testValidation()
  {
    $user = new User();

    // Scenario 1: Empty fields (Should fail)
    $this->assertFalse($user->validate(), 'User should not be valid with empty fields');
    $this->assertArrayHasKey('name', $user->errors, 'Name is required');
    $this->assertArrayHasKey('email', $user->errors, 'Email is required');

    // Scenario 2: Short password (Should fail)
    $user->password = '123';
    $this->assertFalse($user->validate(['password']), 'Password must be at least 6 chars');

    // Scenario 3: Valid Data (Should pass)
    $user->name = 'Valid User';
    $user->email = 'valid@test.com';
    $user->password = 'secret123';
    $this->assertTrue($user->validate(), 'User should be valid with correct data');

    // Scenario 4: Unique Email Check
    // First, save a user
    $user->save(false);

    // Try to create another user with the SAME email
    $duplicateUser = new User();
    $duplicateUser->name = 'Duplicate';
    $duplicateUser->email = 'valid@test.com'; // Same email
    $duplicateUser->password = 'password';

    $this->assertFalse($duplicateUser->validate(['email']), 'Email must be unique');
    $this->assertArrayHasKey('email', $duplicateUser->errors);
  }

  // TEST 2: Password Hashing Logic
  public function testPasswordLogic()
  {
    $user = new User();

    // 1. Test setPassword (Hashing)
    $rawPassword = 'my_super_secret_password';
    $user->setPassword($rawPassword);

    $this->assertNotEquals($rawPassword, $user->password, 'Password should be hashed, not plain text');
    $this->assertNotEmpty($user->password, 'Password hash should be set');

    // 2. Test validatePassword (Verification)
    $this->assertTrue($user->validatePassword($rawPassword), 'Should return true for correct password');
    $this->assertFalse($user->validatePassword('wrong_password'), 'Should return false for wrong password');
  }

  // TEST 3: Identity Interface Methods (findIdentity, findByUsername, etc)
  public function testIdentityMethods()
  {
    // Create a user to find
    $user = new User();
    $user->name = 'Identity Tester';
    $user->email = 'identity@test.com';
    $user->setPassword('123456');
    $user->save(false);

    // 1. Test findIdentity (Find by ID)
    $foundUser = User::findIdentity($user->id);
    $this->assertNotNull($foundUser);
    $this->assertEquals($user->email, $foundUser->email);

    // 2. Test findByUsername (Find by Email)
    $foundByEmail = User::findByUsername('identity@test.com');
    $this->assertNotNull($foundByEmail);
    $this->assertEquals($user->id, $foundByEmail->id);

    // 3. Test non-existent user
    $this->assertNull(User::findByUsername('nobody@test.com'));

    // 4. Test getId()
    $this->assertEquals($user->id, $user->getId());
  }

  // TEST 4: Custom getThumb() Logic
  public function testGetThumb()
  {
    $user = new User();

    // Scenario 1: No image set
    $user->image = null;
    $this->assertEquals('/uploads/no-image.png', $user->getThumb());

    // Scenario 2: Image set in DB, but file missing on disk
    $user->image = 'non_existent_avatar.jpg';
    $this->assertEquals('/uploads/no-image.png', $user->getThumb());
  }

  // TEST 5: Relations
  public function testRelations()
  {
    // Create User
    $user = new User(['name' => 'Author', 'email' => 'author@test.com']);
    $user->save(false);

    // Create Article linked to User
    $article = new Article();
    $article->title = 'My Post';
    $article->user_id = $user->id;
    $article->save(false);

    // Check Relation
    $this->assertNotEmpty($user->articles);
    $this->assertEquals('My Post', $user->articles[0]->title);
  }
}
