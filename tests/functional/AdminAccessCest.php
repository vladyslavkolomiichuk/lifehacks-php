<?php

use app\models\User;

/**
 * Functional tests for admin access control.
 */
class AdminAccessCest
{
  private $adminId;
  private $regularUserId;

  /**
   * Runs before each test.
   * Sets up an admin and a regular user.
   */
  public function _before(FunctionalTester $I)
  {
    // Clear users table
    User::deleteAll();

    // Create admin user
    $admin = new User();
    $admin->name = 'Admin';
    $admin->email = 'admin@test.com';
    $admin->setPassword('admin123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // Create regular user
    $user = new User();
    $user->name = 'Simple User';
    $user->email = 'user@test.com';
    $user->setPassword('user123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;
  }

  /**
   * Scenario 1: Guest (not logged in) tries to access admin page.
   */
  public function guestTryToAccessAdmin(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=admin/default/index');

    // Verify redirected to login
    $I->seeInCurrentUrl('auth');
    $I->seeInCurrentUrl('login');

    // Verify login form text
    $I->see('Login', 'h3');
    $I->see('Please fill out the following fields to login:');
  }

  /**
   * Scenario 2: Regular user tries to access admin page.
   */
  public function regularUserTryToAccessAdmin(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/default/index');

    // Expect forbidden response
    $I->seeResponseCodeIs(403);
    $I->see('Forbidden');
    $I->dontSee('Admin Panel');
  }

  /**
   * Scenario 3: Admin user successfully accesses admin page.
   */
  public function adminAccess(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/default/index');

    // Expect OK response
    $I->seeResponseCodeIs(200);

    // Verify admin panel text
    $I->see('Admin');
    $I->dontSee('Please fill out the following fields to login:');
  }
}
