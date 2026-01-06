<?php

use app\models\User;
use app\models\Article;

/**
 * Functional tests for user profile page.
 */
class ProfileCest
{
  private $userId;

  /**
   * Runs before each test. Sets up a user and one article.
   */
  public function _before(FunctionalTester $I)
  {
    // Clean tables
    Article::deleteAll();
    User::deleteAll();

    // Create test user
    $user = new User();
    $user->name = 'Original Name';
    $user->email = 'user@profile.com';
    $user->setPassword('password123');
    $user->isAdmin = 0;
    $user->save(false);

    $this->userId = $user->id;

    // Create a sample article for the user
    $article = new Article();
    $article->title = 'My First Article';
    $article->user_id = $user->id;
    $article->viewed = 100;
    $article->upvotes = 5;
    $article->date = date('Y-m-d');
    $article->save(false);
  }

  /**
   * Test 1: Guest cannot access profile page.
   */
  public function checkAccessControl(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=profile/index');

    $I->see('Login');
    $I->see('Please fill out the following fields to login:');
  }

  /**
   * Test 2: Dashboard statistics display correctly.
   */
  public function checkDashboardDisplay(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage('/index-test.php?r=profile/index');

    $I->see('User Cabinet');

    $I->see('Original Name');
    $I->see('user@profile.com');

    // Check statistics
    $I->see('1');    // Number of articles
    $I->see('100');  // Number of views

    $I->see('My Articles');
    $I->see('My First Article');
  }

  /**
   * Test 3: Update name and email in profile.
   */
  public function updateProfileName(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage('/index-test.php?r=profile/update');

    $I->see('Update Profile');

    $I->fillField(['name' => 'User[name]'], 'Updated Name');
    $I->fillField(['name' => 'User[email]'], 'newemail@profile.com');

    $I->click('Save');

    $I->seeInCurrentUrl('r=profile');
    $I->seeInCurrentUrl('index');

    $I->seeRecord(User::class, [
      'id' => $this->userId,
      'name' => 'Updated Name',
      'email' => 'newemail@profile.com'
    ]);

    $I->see('Updated Name');
  }

  /**
   * Test 4: Change password and ensure it's updated.
   */
  public function updatePassword(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);

    $user = User::findOne($this->userId);
    $oldPasswordHash = $user->password;

    $I->amOnPage('/index-test.php?r=profile/update');

    $I->fillField(['name' => 'User[password]'], 'newpassword123');
    $I->click('Save Changes');

    $I->seeInCurrentUrl('r=profile');
    $I->see('User Cabinet');

    // Ensure password hash changed in DB
    $I->dontSeeRecord(User::class, [
      'id' => $this->userId,
      'password' => $oldPasswordHash
    ]);
  }
}
