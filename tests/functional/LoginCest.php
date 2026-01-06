<?php

use app\models\User;

/**
 * Functional tests for login page and authentication.
 */
class LoginCest
{
  /**
   * Runs before each test. Creates a fresh test user.
   */
  public function _before(FunctionalTester $I)
  {
    // Clean up users for isolation
    User::deleteAll();

    // Create a test user
    $user = new User();
    $user->name = 'Test User';
    $user->email = 'test@example.com';
    $user->setPassword('password123');
    $user->isAdmin = 0;
    $user->save(false);
  }

  /**
   * Test 1: Open login page
   */
  public function openLoginPage(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=auth/login');

    // Verify the login page header
    $I->see('Please fill out the following fields to login:');

    // Verify presence of form and input fields
    $I->seeElement('#login-form');
    $I->seeElement('input', ['name' => 'LoginForm[email]']);
    $I->seeElement('input', ['name' => 'LoginForm[password]']);
  }

  /**
   * Test 2: Attempt login with incorrect password
   */
  public function loginWithWrongPassword(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=auth/login');

    $I->fillField(['name' => 'LoginForm[email]'], 'test@example.com');
    $I->fillField(['name' => 'LoginForm[password]'], 'wrong_pass');

    // Click the login button
    $I->click('login-button');

    // Verify validation error
    $I->see('Incorrect email or password.');

    // Ensure still on login page
    $I->seeElement('#login-form');
  }

  /**
   * Test 3: Successful login
   */
  public function loginSuccessfully(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=auth/login');

    $I->fillField(['name' => 'LoginForm[email]'], 'test@example.com');
    $I->fillField(['name' => 'LoginForm[password]'], 'password123');

    $I->click('login-button');

    // Verify redirect and that Logout is visible
    $I->see('Logout');

    // Verify the login form is gone
    $I->dontSeeElement('#login-form');
  }
}
