<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;

class ArticleCest
{
  private $userId;
  private $topicId;

  public function _before(FunctionalTester $I)
  {
    // 1. Очищаємо БД
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // 2. Створюємо категорію (бо вона обов'язкова для статті)
    $topic = new Topic(['name' => 'LifeHacks']);
    $topic->save(false);
    $this->topicId = $topic->id;

    // 3. Створюємо користувача
    $user = new User();
    $user->name = 'Author';
    $user->email = 'author@test.com';
    $user->setPassword('123456');
    $user->save(false);
    $this->userId = $user->id;
  }

  // ТЕСТ 1: Гість бачить список статей
  public function checkIndexAsGuest(FunctionalTester $I)
  {
    // Створимо статтю вручну, щоб було що бачити
    $article = new Article([
      'title' => 'Public Article',
      'topic_id' => $this->topicId,
      'user_id' => $this->userId,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);

    $I->amOnPage(['article/index']);
    $I->see('LifeHacks - Articles', 'title');
    $I->see('Public Article');
    $I->dontSeeLink('+ Create New'); // Гість не має бачити кнопку створення (якщо ви її ховаєте у view)
  }

  // ТЕСТ 2: Захист (Гість не може створити статтю)
  public function checkCreateAccessControl(FunctionalTester $I)
  {
    $I->amOnPage(['article/create']);
    // Має перекинути на логін
    $I->see('Login');
    $I->seeCurrentUrlMatches('~/auth/login~');
  }

  // ТЕСТ 3: Створення статті (Авторизований)
  public function createArticleSuccessfully(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage(['article/create']);

    $I->see('Create New Tip', 'h3');

    // Заповнюємо форму
    // Зверніть увагу: Image ми пропускаємо, бо він не обов'язковий (сподіваюсь),
    // або треба покласти файл у tests/_data/
    $I->submitForm('form', [
      'Article[title]' => 'My Functional Test Article',
      'Article[topic_id]' => $this->topicId, // Select option value
      'Article[description]' => 'This is a description content',
      'Article[tag]' => 'test, codeception',
    ]);

    // Після успіху контролер перекидає на profile/index
    $I->seeCurrentUrlMatches('~profile/index~');
    $I->see('Article created successfully!');

    // Перевіряємо чи є запис в базі
    $I->seeRecord(Article::class, ['title' => 'My Functional Test Article']);
  }

  // ТЕСТ 4: Пошук
  public function searchArticle(FunctionalTester $I)
  {
    // Створюємо статтю
    $article = new Article([
      'title' => 'UniqueSearchTerm',
      'topic_id' => $this->topicId,
      'user_id' => $this->userId,
      'description' => 'text'
    ]);
    $article->save(false);

    // Йдемо на сторінку пошуку
    $I->amOnPage(['article/search', 'q' => 'UniqueSearchTerm']);

    $I->see('Search results for: "UniqueSearchTerm"', 'h1');
    $I->see('UniqueSearchTerm', '.article-title');
  }
}
