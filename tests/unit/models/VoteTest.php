<?php

namespace tests\unit\models;

use app\models\Vote;
use app\models\User;
use app\models\Article;
use app\models\Topic;

class VoteTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    // Clear all tables before test
    Vote::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();
  }

  // TEST 1: Validation (required fields)
  public function testValidation()
  {
    $vote = new Vote();

    // Scenario 1: Empty model
    $this->assertFalse($vote->validate(), 'Vote should not be valid without user_id and article_id');
    $this->assertArrayHasKey('user_id', $vote->errors);
    $this->assertArrayHasKey('article_id', $vote->errors);

    // Scenario 2: Wrong data types (must be integer)
    $vote->user_id = 'not-a-number';
    $vote->article_id = 'string';
    $this->assertFalse($vote->validate(), 'IDs must be integers');
  }

  // TEST 2: Save and relations
  public function testSavingAndRelations()
  {
    // 1. Create article author
    $author = new User(['name' => 'Author', 'email' => 'author@test.com', 'password' => '123']);
    $author->save(false);

    // 2. Create voter
    $voter = new User(['name' => 'Voter', 'email' => 'voter@test.com', 'password' => '123']);
    $voter->save(false);

    // 3. Create topic and article
    $topic = new Topic(['name' => 'Tech']);
    $topic->save(false);

    $article = new Article([
      'title' => 'Best Article',
      'user_id' => $author->id,
      'topic_id' => $topic->id
    ]);
    $article->save(false);

    // 4. Create vote
    $vote = new Vote();
    $vote->user_id = $voter->id;
    $vote->article_id = $article->id;

    // Check save
    $this->assertTrue($vote->save(), 'Vote should be saved successfully');

    // 5. Check relations
    $this->assertNotNull($vote->article, 'HasOne relation to Article');
    $this->assertEquals($article->title, $vote->article->title);

    $this->assertNotNull($vote->user, 'HasOne relation to User');
    $this->assertEquals($voter->email, $vote->user->email);
  }
}
