<?php

use app\models\User;
use app\models\Article;

class ProfileCest
{
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
    $user->save(false);

    $this->userId = $user->id;

    // 3. Створюємо статтю (перевірте, чи потрібен topic_id у вашій моделі)
    $article = new Article();
    $article->title = 'My First Article';
    $article->user_id = $user->id;
    $article->viewed = 100;
    $article->upvotes = 5;
    $article->date = date('Y-m-d');
    $article->save(false);
  }

  // ТЕСТ 1: Гість не бачить профіль
  public function checkAccessControl(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=profile/index');

    // Має перекинути на логін
    $I->see('Login');
    $I->see('Please fill out the following fields to login:');
  }

  // ТЕСТ 2: Статистика кабінету
  public function checkDashboardDisplay(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage('/index-test.php?r=profile/index');

    // Перевіряємо заголовок (через текст на сторінці, це надійніше за <title>)
    $I->see('User Cabinet');

    // Перевіряємо особисті дані
    $I->see('Original Name');
    $I->see('user@profile.com');

    // Перевіряємо Статистику
    $I->see('1'); // Кількість статей
    $I->see('100'); // Кількість переглядів

    $I->see('My Articles');
    $I->see('My First Article');
  }

  // ТЕСТ 3: Редагування імені
  public function updateProfileName(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage('/index-test.php?r=profile/update');

    $I->see('Update Profile');

    $I->fillField(['name' => 'User[name]'], 'Updated Name');
    $I->fillField(['name' => 'User[email]'], 'newemail@profile.com');

    $I->click('Save');

    // 1. Використовуємо більш гнучку перевірку URL (враховуємо %2F)
    $I->seeInCurrentUrl('r=profile');
    $I->seeInCurrentUrl('index');

    // 2. Оскільки SweetAlert невидимий, перевіряємо зміни в БД
    $I->seeRecord(User::class, [
      'id' => $this->userId,
      'name' => 'Updated Name',
      'email' => 'newemail@profile.com'
    ]);

    // 3. Перевіряємо, що нове ім'я відображається в інтерфейсі кабінету
    $I->see('Updated Name');
  }

  // ТЕСТ 4: Зміна пароля та перелогін
  public function updatePassword(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);

    // Запам'ятовуємо старий хеш пароля прямо через модель
    $user = \app\models\User::findOne($this->userId);
    $oldPasswordHash = $user->password;

    $I->amOnPage('/index-test.php?r=profile/update');

    // Заповнюємо новий пароль
    $I->fillField(['name' => 'User[password]'], 'newpassword123');

    // Натискаємо кнопку (текст з вашого update.php)
    $I->click('Save Changes');

    // 1. Перевіряємо редирект назад у кабінет
    $I->seeInCurrentUrl('r=profile');
    $I->see('User Cabinet');

    // 2. Перевіряємо, що в базі запис оновився
    // Ми перевіряємо, що у користувача з нашим ID пароль більше НЕ дорівнює старому хешу
    $I->dontSeeRecord(\app\models\User::class, [
      'id' => $this->userId,
      'password' => $oldPasswordHash
    ]);
  }
}
