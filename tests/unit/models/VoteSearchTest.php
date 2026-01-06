<?php

namespace tests\unit\models;

use app\models\VoteSearch;
use app\models\Vote;
use app\models\User;
use app\models\Article;
use app\models\Topic;
use Yii;

class VoteSearchTest extends \Codeception\Test\Unit
{
  protected $tester;

  protected function _before()
  {
    // Ensure clean DB by disabling FOREIGN KEY CHECKS
    $db = Yii::$app->db;
    $db->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();

    Vote::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    $db->createCommand('SET FOREIGN_KEY_CHECKS = 1')->execute();
  }

  public function testSearch()
  {
    $topic = new Topic(['name' => 'VoteTopic']);
    $topic->save(false);

    $u = new User(['name' => 'A', 'email' => 'a@t.com']);
    $u->password = '1';
    $u->save(false);

    // Create target article
    $targetArticle = new Article([
      'title' => 'Target',
      'user_id' => $u->id,
      'topic_id' => $topic->id,
      'date' => date('Y-m-d')
    ]);
    $targetArticle->save(false);

    // Create 2 voters
    $u1 = new User(['name' => 'Voter 1', 'email' => 'v1@t.com']);
    $u1->password = '1';
    $u1->save(false);
    $u2 = new User(['name' => 'Voter 2', 'email' => 'v2@t.com']);
    $u2->password = '1';
    $u2->save(false);

    // Add votes
    (new Vote(['user_id' => $u1->id, 'article_id' => $targetArticle->id]))->save(false);
    (new Vote(['user_id' => $u2->id, 'article_id' => $targetArticle->id]))->save(false);

    $searchModel = new VoteSearch();

    // Search votes for target article
    $params = ['VoteSearch' => ['article_id' => $targetArticle->id]];
    $dataProvider = $searchModel->search($params);

    $this->assertEquals(2, $dataProvider->getTotalCount(), 'Should be 2 votes for article ID ' . $targetArticle->id);
  }
}
