<?php

namespace tests\unit\models;

use app\models\Topic;
use app\models\Article;
use app\models\User;

class TopicTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    // Clear tables to avoid old data interference
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();
  }

  // TEST 1: Validation check (Rules)
  public function testValidation()
  {
    $topic = new Topic();

    // Scenario 1: Valid name
    $topic->name = 'Programming';
    $this->assertTrue($topic->validate(), 'Topic with normal name should be valid');

    // Scenario 2: Name too long (max 255)
    $topic->name = str_repeat('a', 256);
    $this->assertFalse($topic->validate(['name']), 'Name should not exceed 255 chars');

    // Scenario 3: Data type check (String)
    // Yii2 won't auto-convert array to string; should fail
    $topic->name = ['array', 'is', 'not', 'string'];
    $this->assertFalse($topic->validate(['name']), 'Name must be a string');
  }

  // TEST 2: Save and articles relation
  public function testSavingAndArticlesRelation()
  {
    // 1. Create Topic
    $topic = new Topic();
    $topic->name = 'Lifehacks';

    // Check if saved
    $this->assertTrue($topic->save(), 'Topic should be saved');
    $this->assertNotNull($topic->id, 'Topic should have an ID after save');

    // 2. Prepare User for articles
    $user = new User(['name' => 'Author', 'email' => 'a@a.com', 'password' => '123']);
    $user->save(false);

    // 3. Create 2 articles linked to this topic
    $article1 = new Article([
      'title' => 'Lifehack #1',
      'user_id' => $user->id,
      'topic_id' => $topic->id, // link
      'date' => date('Y-m-d')
    ]);
    $article1->save(false);

    $article2 = new Article([
      'title' => 'Lifehack #2',
      'user_id' => $user->id,
      'topic_id' => $topic->id, // link
      'date' => date('Y-m-d')
    ]);
    $article2->save(false);

    // 4. Create article in another topic
    $otherTopic = new Topic(['name' => 'Other']);
    $otherTopic->save(false);

    $article3 = new Article([
      'title' => 'Other Article',
      'user_id' => $user->id,
      'topic_id' => $otherTopic->id // different topic
    ]);
    $article3->save(false);

    // --- RELATION CHECK ---

    // Refresh topic to get latest data
    // Better use ->articles property

    $relatedArticles = $topic->articles; // calls getArticles()

    // Should have 2 articles
    $this->assertCount(2, $relatedArticles, 'Topic should have exactly 2 related articles');

    // Check titles to ensure correct articles
    $titles = [$relatedArticles[0]->title, $relatedArticles[1]->title];
    $this->assertContains('Lifehack #1', $titles);
    $this->assertContains('Lifehack #2', $titles);
    $this->assertNotContains('Other Article', $titles);
  }
}
