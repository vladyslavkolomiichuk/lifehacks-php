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
    // Set a specific layout for the admin panel
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
            'roles' => ['@'], // Authenticated users
            'matchCallback' => function ($rule, $action) {
              // Check if user is admin (1 = admin)
              return Yii::$app->user->identity->isAdmin == 1;
            }
          ],
        ],
      ],
    ];
  }
}
