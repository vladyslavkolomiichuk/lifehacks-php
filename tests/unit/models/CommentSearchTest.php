<?php

namespace tests\unit\models;

use app\models\CommentSearch;
use app\models\Comment;
use app\models\Article;
use app\models\User;
use app\models\Topic;
use Yii;

/**
 * Unit test for CommentSearch model.
 */
class CommentSearchTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  /**
   * Clear database before each test.
   */
  protected function _before()
  {
    $db = Yii::$app->db;
    $db->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();

    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    $db->createCommand('SET FOREIGN_KEY_CHECKS = 1')->execute();
  }

  /**
   * Test searching comments by user_id and text.
   */
  public function testSearch()
  {
    // 1. Create user
    $userAlice = new User([
      'name' => 'Alice Unique',
      'email' => 'alice_unique@test.com',
      'password' => '123456'
    ]);
    $userAlice->save(false);

    // 2. Create topic
    $topic = new Topic(['name' => 'General']);
    $topic->save(false);

    // 3. Create article
    $article = new Article([
      'title' => 'Post',
      'user_id' => $userAlice->id,
      'topic_id' => $topic->id,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);

    // 4. Create comments
    $c1 = new Comment([
      'text' => 'SEARCH_ME_1',
      'user_id' => $userAlice->id,
      'article_id' => $article->id,
      'date' => date('Y-m-d H:i:s')
    ]);
    $c1->save(false);

    $c2 = new Comment([
      'text' => 'SEARCH_ME_2',
      'user_id' => $userAlice->id,
      'article_id' => $article->id,
      'date' => date('Y-m-d H:i:s')
    ]);
    $c2->save(false);

    $searchModel = new CommentSearch();

    // 5. Search by user_id
    $params = ['CommentSearch' => ['user_id' => $userAlice->id]];
    $dataProvider = $searchModel->search($params);
    $this->assertEquals(2, $dataProvider->getTotalCount(), 'Should find 2 comments by user ID');

    // 6. Search by text containing SEARCH_ME
    $params = ['CommentSearch' => ['text' => 'SEARCH_ME']];
    $dataProvider = $searchModel->search($params);
    $this->assertEquals(2, $dataProvider->getTotalCount(), 'Should find 2 comments containing SEARCH_ME');
  }
}
