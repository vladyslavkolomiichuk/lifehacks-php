<?php

use app\models\User;
use app\models\Article;

class ProfileCest
{
  // ID користувача для тестів
  private $userId;

  public function _before(FunctionalTester $I)
  {
    // 1. Очищаємо базу
    Article::deleteAll();
    User::deleteAll();

    // 2. Створюємо користувача
    $user = new User();
    $user->name = 'Original Name';
    $user->email = 'user@profile.com';
    $user->setPassword('password123');
    $user->isAdmin = 0;
    $user->image = 'default.jpg'; // Важливо для view
    $user->save(false);

    $this->userId = $user->id;

    // 3. Створюємо одну статтю для цього користувача (щоб перевірити статистику)
    $article = new Article();
    $article->title = 'My First Article';
    $article->user_id = $user->id;
    $article->viewed = 100;
    $article->upvotes = 5;
    // Додайте інші обов'язкові поля, якщо є (наприклад topic_id)
    // $article->topic_id = 1; 
    $article->save(false);
  }

  // ТЕСТ 1: Перевірка доступу (Гість не повинен бачити профіль)
  public function checkAccessControl(FunctionalTester $I)
  {
    // Як гість пробуємо зайти
    $I->amOnPage(['profile/index']);
    // Має перекинути на логін
    $I->seeCurrentUrlEquals('/index-test.php/site/login'); // Або /auth/login залежно від конфігу
    $I->see('Login');
  }

  // ТЕСТ 2: Перевірка Dashboard (Статистика та дані)
  public function checkDashboardDisplay(FunctionalTester $I)
  {
    // Логінимось
    $I->amLoggedInAs($this->userId);

    $I->amOnPage(['profile/index']);

    // Перевіряємо заголовок
    $I->see('User Cabinet', 'title'); // Перевіряємо <title> або текст на сторінці

    // Перевіряємо особисті дані
    $I->see('Original Name', 'h3');
    $I->see('user@profile.com');

    // Перевіряємо Статистику (ми створили 1 статтю з 100 переглядів)
    // Шукаємо цифру 1 у блоці Articles
    $I->see('1', '.widget h3');
    // Шукаємо цифру 100 у блоці Views
    $I->see('100', '.widget h3');

    // Перевіряємо список статей
    $I->see('My Articles (1)');
    $I->see('My First Article');
  }

  // ТЕСТ 3: Редагування профілю (Зміна імені)
  public function updateProfileName(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage(['profile/update']);

    $I->see('Update Profile', 'h3');

    // Заповнюємо форму
    // Важливо: у view ви використовуєте $form->field($user, ...), тому name буде User[name]
    $I->submitForm('form', [
      'User[name]' => 'Updated Name',
      'User[email]' => 'newemail@profile.com',
      'User[password]' => '', // Залишаємо пустим, щоб не міняти
    ]);

    // Після успіху нас має перекинути на index з повідомленням
    $I->seeCurrentUrlMatches('~profile/index~');
    $I->see('Profile updated successfully!');

    // Перевіряємо, чи змінилось ім'я на сторінці
    $I->see('Updated Name', 'h3');
    $I->dontSee('Original Name', 'h3');
  }

  // ТЕСТ 4: Зміна пароля
  public function updatePassword(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage(['profile/update']);

    // Міняємо пароль
    $I->submitForm('form', [
      'User[name]' => 'Original Name',
      'User[email]' => 'user@profile.com',
      'User[password]' => 'newpassword123', // Новий пароль
    ]);

    $I->see('Profile updated successfully!');

    // Перевірка: Виходимо і пробуємо зайти з НОВИМ паролем
    Yii::$app->user->logout();

    // Йдемо на логін
    $I->amOnPage(['auth/login']); // Або site/login
    $I->submitForm('#login-form', [
      'LoginForm[email]' => 'user@profile.com',
      'LoginForm[password]' => 'newpassword123',
    ]);

    // Якщо зайшли успішно - побачимо Logout або User Cabinet
    $I->dontSee('Incorrect email or password');
    $I->see('Logout');
  }
}
