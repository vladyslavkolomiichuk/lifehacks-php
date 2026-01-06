<?php

use app\models\User;

/**
 * Functional tests for admin User management.
 */
class AdminUserCest
{
  private $adminId;
  private $regularUserId;

  /**
   * Prepare test data: admin and regular user.
   */
  public function _before(FunctionalTester $I)
  {
    User::deleteAll();

    // Admin
    $admin = new User(['name' => 'Super Admin', 'email' => 'admin@test.com']);
    $admin->setPassword('123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // Regular user
    $user = new User(['name' => 'Simple User', 'email' => 'user@test.com']);
    $user->setPassword('123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;
  }

  /**
   * Test 1: Access control - guest and non-admin user cannot access admin User pages.
   */
  public function checkAccessControl(FunctionalTester $I)
  {
    // Guest
    $I->amOnPage('/index-test.php?r=admin/user/index');
    $I->see('Login');

    // Regular user
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/user/index');
    $I->seeResponseCodeIs(403);
  }

  /**
   * Test 2: Index page displays users.
   */
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/user/index');

    $I->see('Users', 'h1');
    $I->see('Super Admin');
    $I->see('Simple User');

    // Optional: check roles displayed
    $I->see('Admin');
    $I->see('User');
  }

  /**
   * Test 3: Update regular user (change name and role).
   */
  public function updateUser(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/user/update&id=' . $this->regularUserId);

    $I->see('Update User', 'h1');

    $I->fillField(['name' => 'User[name]'], 'Promoted User');
    $I->selectOption(['name' => 'User[isAdmin]'], '1');

    $I->click('button[type=submit]');

    $I->see('Users', 'h1');
    $I->see('Promoted User');

    $I->seeRecord(User::class, ['id' => $this->regularUserId, 'isAdmin' => 1]);
  }

  /**
   * Test 4: Delete another user.
   */
  public function deleteOtherUser(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    $I->sendAjaxPostRequest('/index-test.php?r=admin/user/delete&id=' . $this->regularUserId);

    $I->amOnPage('/index-test.php?r=admin/user/index');
    $I->dontSee('Simple User');
    $I->dontSeeRecord(User::class, ['id' => $this->regularUserId]);
  }

  /**
   * Test 5: Attempt self-deletion should fail.
   */
  public function trySelfDelete(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->sendAjaxPostRequest('/index-test.php?r=admin/user/delete&id=' . $this->adminId);

    $I->amOnPage('/index-test.php?r=admin/user/index');
    $I->seeRecord(User::class, ['id' => $this->adminId]);
    $I->see('Super Admin');
  }
}
