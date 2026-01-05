<?php

use app\models\User;

class SignupCest
{
  public function _before(FunctionalTester $I)
  {
    // Очищаємо базу перед тестом
    User::deleteAll();
  }

  public function openSignupPage(FunctionalTester $I)
  {
    // ВИПРАВЛЕНО: передаємо рядок замість масиву
    $I->amOnPage('/index-test.php?r=auth/signup');
    $I->see('Signup', 'h3');
    $I->seeElement('input', ['name' => 'SignupForm[name]']);
    $I->seeElement('input', ['name' => 'SignupForm[email]']);
    $I->seeElement('input', ['name' => 'SignupForm[password]']);
  }

  public function signupWithEmptyFields(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=auth/signup');

    // Клікаємо саме по кнопці форми
    $I->click('button[type=submit]');

    $I->expect('validation errors are displayed');
    $I->see('Name cannot be blank');
    $I->see('Email cannot be blank');
    $I->see('Password cannot be blank');
  }

  public function signupSuccessfully(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=auth/signup');

    $I->fillField(['name' => 'SignupForm[name]'], 'Test User');
    $I->fillField(['name' => 'SignupForm[email]'], 'newuser@test.com');
    $I->fillField(['name' => 'SignupForm[password]'], 'password123');

    $I->click('button[type=submit]');

    // ВАРІАНТ 1: Перевіряємо частинами (найбільш стабільно)
    $I->seeInCurrentUrl('auth');
    $I->seeInCurrentUrl('login');

    // ВАРІАНТ 2: Або перевіряємо текст на сторінці логіна
    $I->see('Please fill out the following fields to login:');

    // ВАРІАНТ 3: Перевірка бази даних (найважливіше!)
    $I->seeRecord('app\models\User', [
      'email' => 'newuser@test.com',
      'name' => 'Test User'
    ]);
  }
}
