<?php

namespace tests\unit\models;

use app\models\Article;
use app\models\Topic;
use app\models\User;

/**
 * Unit tests for Article model.
 */
class ArticleTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  /**
   * Cleans tables before each test to avoid conflicts.
   */
  protected function _before()
  {
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();
  }

  /**
   * Test validation rules of Article model.
   */
  public function testValidation()
  {
    // 1. Create real User and Topic for 'exist' rules
    $user = new User(['name' => 'Tester', 'email' => 'test@test.com']);
    $user->setPassword('123456');
    $user->save(false);

    $topic = new Topic(['name' => 'Test Topic']);
    $topic->save(false);

    $article = new Article();

    // Scenario 1: Valid data
    $article->title = 'Test Title';
    $article->description = 'Some long text description';
    $article->date = date('Y-m-d');
    $article->user_id = $user->id;
    $article->topic_id = $topic->id;
    $this->assertTrue($article->validate(), 'Article should be valid with correct data');

    // Scenario 2: Non-existing user_id
    $article->user_id = 9999;
    $this->assertFalse($article->validate(['user_id']), 'Validation should fail if user_id does not exist');

    // Scenario 3: Title too long
    $article->user_id = $user->id; // restore valid user_id
    $article->title = str_repeat('a', 256);
    $this->assertFalse($article->validate(['title']), 'Validation should fail for title > 255 chars');
  }

  /**
   * Test saving the model and checking relations.
   */
  public function testSavingAndRelations()
  {
    $user = new User(['name' => 'Author', 'email' => 'author@test.com']);
    $user->setPassword('123');
    $user->save(false);

    $topic = new Topic(['name' => 'Tech']);
    $topic->save(false);

    $article = new Article();
    $article->title = 'My Article';
    $article->user_id = $user->id;
    $article->topic_id = $topic->id;
    $article->date = date('Y-m-d');

    $this->assertTrue($article->save(), 'Article should save successfully');
    $this->assertNotNull($article->id, 'Article ID should be generated');
  }

  /**
   * Test getImage() returns a string (placeholder or path).
   */
  public function testGetThumb()
  {
    $article = new Article();
    $thumb = $article->getImage();

    $this->assertNotEmpty($thumb, 'getImage() should return a non-empty value');
    $this->assertIsString($thumb, 'getImage() should return a string');
  }
}
