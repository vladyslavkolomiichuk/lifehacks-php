<?php

namespace app\modules\admin;

use Yii;
use yii\filters\AccessControl;

class Module extends \yii\base\Module
{
  public $controllerNamespace = 'app\modules\admin\controllers';

  public function init()
  {
    parent::init();
    $this->layout = 'main';
  }

  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [
            'allow' => true,
            'roles' => ['@'],
            'matchCallback' => function ($rule, $action) {
              return Yii::$app->user->identity->isAdmin == 1;
            }
          ],
        ],
      ],
    ];
  }
}
