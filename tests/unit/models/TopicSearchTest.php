<?php

namespace tests\unit\models;

use app\models\TopicSearch;
use app\models\Topic;

class TopicSearchTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    // Очищаємо таблицю тем перед тестом
    Topic::deleteAll();
  }

  public function testSearch()
  {
    $topic = new Topic(['name' => 'UniqueTopic']);
    $topic->save(false);

    $searchModel = new TopicSearch();
    $params = ['TopicSearch' => ['name' => 'UniqueTopic']];
    $dataProvider = $searchModel->search($params);

    $this->assertEquals(1, $dataProvider->getTotalCount());
  }
}
