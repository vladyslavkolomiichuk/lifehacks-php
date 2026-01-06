<?php

namespace tests\unit\models;

use app\models\TopicSearch;
use app\models\Topic;

/**
 * Unit test for TopicSearch model.
 */
class TopicSearchTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    // Clear topics table before each test
    Topic::deleteAll();
  }

  /**
   * Test searching for a topic by name.
   */
  public function testSearch()
  {
    // Create a topic
    $topic = new Topic(['name' => 'UniqueTopic']);
    $topic->save(false);

    // Initialize search model
    $searchModel = new TopicSearch();

    // Search by exact name
    $params = ['TopicSearch' => ['name' => 'UniqueTopic']];
    $dataProvider = $searchModel->search($params);

    // Assert that exactly one topic is found
    $this->assertEquals(1, $dataProvider->getTotalCount(), 'Should find 1 topic with name "UniqueTopic"');

    // Optional: verify the found record has correct name
    $foundTopic = $dataProvider->getModels()[0];
    $this->assertEquals('UniqueTopic', $foundTopic->name);
  }
}
