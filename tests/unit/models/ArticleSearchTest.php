<?php

namespace tests\unit\models;

use app\models\Article;
use app\models\ArticleSearch;
use app\models\User;
use app\models\Topic;

class ArticleSearchTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  // Цей метод запускається ПЕРЕД кожним тестом. 
  // Ми використовуємо його, щоб очистити таблицю статей перед тестом, 
  // аби старі дані не заважали.
  protected function _before()
  {
    $db = \Yii::$app->db;
    $db->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();
    $db->createCommand('SET FOREIGN_KEY_CHECKS = 1')->execute();
  }

  public function testSearch()
  {
    $user = new User(['name' => 'Admin', 'email' => 'a@a.com', 'password' => '123']);
    $user->save(false);
    $topic = new Topic(['name' => 'News']);
    $topic->save(false);

    // Створюємо ДВІ статті в ОДНІЙ категорії
    (new Article(['title' => 'First News', 'topic_id' => $topic->id, 'user_id' => $user->id, 'date' => '2025-01-01']))->save(false);
    (new Article(['title' => 'Second News', 'topic_id' => $topic->id, 'user_id' => $user->id, 'date' => '2025-01-01']))->save(false);

    $searchModel = new ArticleSearch();
    // Шукаємо за топіком
    $dataProvider = $searchModel->search(['ArticleSearch' => ['topic_id' => $topic->id]]);

    $this->assertEquals(2, $dataProvider->getTotalCount(), 'Має знайти 2 статті в категорії News');
  }
}
