<?php

namespace tests\unit\models;

use app\models\Article;
use app\models\ArticleSearch;
use app\models\User;
use app\models\Topic;

/**
 * Unit test for ArticleSearch model.
 */
class ArticleSearchTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  /**
   * Runs before each test: clean tables to avoid interference.
   */
  protected function _before()
  {
    $db = \Yii::$app->db;

    // Temporarily disable foreign key checks to safely delete all records
    $db->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();

    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    $db->createCommand('SET FOREIGN_KEY_CHECKS = 1')->execute();
  }

  /**
   * Test basic search by topic_id.
   */
  public function testSearch()
  {
    // 1. Create a user
    $user = new User([
      'name' => 'Admin',
      'email' => 'a@a.com',
    ]);
    $user->setPassword('123');
    $user->save(false);

    // 2. Create a topic
    $topic = new Topic(['name' => 'News']);
    $topic->save(false);

    // 3. Create two articles in the same topic
    (new Article([
      'title' => 'First News',
      'topic_id' => $topic->id,
      'user_id' => $user->id,
      'date' => '2025-01-01'
    ]))->save(false);

    (new Article([
      'title' => 'Second News',
      'topic_id' => $topic->id,
      'user_id' => $user->id,
      'date' => '2025-01-01'
    ]))->save(false);

    // 4. Search for articles by topic_id
    $searchModel = new ArticleSearch();
    $dataProvider = $searchModel->search(['ArticleSearch' => ['topic_id' => $topic->id]]);

    // 5. Assert that 2 articles are returned
    $this->assertEquals(2, $dataProvider->getTotalCount(), 'Should find 2 articles in News topic');
  }
}
