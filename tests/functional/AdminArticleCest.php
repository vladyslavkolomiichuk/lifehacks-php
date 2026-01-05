<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;

class AdminArticleCest
{
  private $adminId;
  private $regularUserId;
  private $topicId;
  private $articleId; // ID статті для тестування редагування/видалення

  public function _before(FunctionalTester $I)
  {
    // 1. Очищення бази
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // 2. Створюємо Адміна
    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => '123']);
    $admin->isAdmin = 1; // <--- ВАЖЛИВО
    $admin->save(false);
    $this->adminId = $admin->id;

    // 3. Створюємо звичайного юзера (для перевірки заборони доступу)
    $user = new User(['name' => 'Simple', 'email' => 'user@test.com', 'password' => '123']);
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // 4. Створюємо Топік (потрібен для dropdown у формі)
    $topic = new Topic(['name' => 'News']);
    $topic->save(false);
    $this->topicId = $topic->id;

    // 5. Створюємо одну статтю (для тесту Index/Update/Delete)
    $article = new Article([
      'title' => 'Existing Article',
      'description' => 'Desc',
      'topic_id' => $topic->id,
      'user_id' => $admin->id,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);
    $this->articleId = $article->id;
  }

  // ТЕСТ 1: Перевірка безпеки (Security Check)
  public function checkAccessControl(FunctionalTester $I)
  {
    // Сценарій А: Гість
    $I->amOnPage(['admin/article/index']);
    $I->see('Login'); // Має перекинути на логін

    // Сценарій Б: Звичайний юзер
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage(['admin/article/index']);
    $I->seeResponseCodeIs(403); // Forbidden
  }

  // ТЕСТ 2: Адмін бачить список статей (Index)
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage(['admin/article/index']);

    $I->see('Articles Manager', 'h1');
    $I->see('Existing Article'); // Бачимо статтю, створену в _before
    $I->see('News'); // Назва топіка
  }

  // ТЕСТ 3: Створення статті (Create)
  public function createArticle(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage(['admin/article/create']);

    $I->see('Create Article', 'h1');

    // Заповнюємо форму
    // Імена полів беремо з Article моделі: Article[title], Article[topic_id]...
    $I->submitForm('.dark-form', [
      'Article[title]' => 'New Admin Article',
      'Article[description]' => 'Content created by admin',
      'Article[topic_id]' => $this->topicId, // Вибираємо ID топіка
      'Article[user_id]' => $this->adminId,   // Вибираємо автора
      'Article[tag]' => 'admin, test',
    ]);

    // Після успішного збереження нас редіректить на view
    $I->see('New Admin Article', 'h1'); // Заголовок на сторінці View

    // Перевіряємо базу
    $I->seeRecord(Article::class, ['title' => 'New Admin Article']);
  }

  // ТЕСТ 4: Редагування статті (Update)
  public function updateArticle(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    // Йдемо на сторінку редагування існуючої статті
    $I->amOnPage(['admin/article/update', 'id' => $this->articleId]);

    $I->see('Update Article:', 'h1');

    // Змінюємо заголовок
    $I->submitForm('.dark-form', [
      'Article[title]' => 'Updated Title By Admin',
    ]);

    // Перевіряємо результат на сторінці перегляду
    $I->see('Updated Title By Admin', 'h1');
  }

  // ТЕСТ 5: Видалення статті (Delete)
  public function deleteArticle(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    // Оскільки видалення вимагає методу POST, ми можемо відправити його напряму
    $I->sendPost(['admin/article/delete', 'id' => $this->articleId]);

    // Після видалення нас кидає на index
    $I->amOnPage(['admin/article/index']);

    // Статті більше не має бути
    $I->dontSee('Existing Article');
    $I->dontSeeRecord(Article::class, ['id' => $this->articleId]);
  }
}
