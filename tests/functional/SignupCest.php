<?php

use app\models\User;

/**
 * Functional tests for user signup.
 */
class SignupCest
{
  /**
   * Runs before each test: clean the user table.
   */
  public function _before(FunctionalTester $I)
  {
    User::deleteAll();
  }

  /**
   * Test 1: Open the signup page and check the form fields.
   */
  public function openSignupPage(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=auth/signup');

    $I->see('Signup', 'h3');
    $I->seeElement('input', ['name' => 'SignupForm[name]']);
    $I->seeElement('input', ['name' => 'SignupForm[email]']);
    $I->seeElement('input', ['name' => 'SignupForm[password]']);
  }

  /**
   * Test 2: Submit the signup form with empty fields to trigger validation errors.
   */
  public function signupWithEmptyFields(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=auth/signup');

    $I->click('button[type=submit]');

    $I->expect('validation errors are displayed');
    $I->see('Name cannot be blank');
    $I->see('Email cannot be blank');
    $I->see('Password cannot be blank');
  }

  /**
   * Test 3: Successfully sign up a new user.
   */
  public function signupSuccessfully(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=auth/signup');

    $I->fillField(['name' => 'SignupForm[name]'], 'Test User');
    $I->fillField(['name' => 'SignupForm[email]'], 'newuser@test.com');
    $I->fillField(['name' => 'SignupForm[password]'], 'password123');

    $I->click('button[type=submit]');

    // Verify redirection to login page
    $I->seeInCurrentUrl('auth');
    $I->seeInCurrentUrl('login');

    // Verify login form is visible
    $I->see('Please fill out the following fields to login:');

    // Verify the user is saved in the database
    $I->seeRecord(User::class, [
      'email' => 'newuser@test.com',
      'name' => 'Test User'
    ]);
  }
}
