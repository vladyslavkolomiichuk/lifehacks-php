<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Form model for user registration.
 */
class SignupForm extends Model
{
  public $name;
  public $email;
  public $password;

  /**
   * Validation rules.
   */
  public function rules()
  {
    return [
      ['name', 'trim'],
      ['name', 'required'],
      ['name', 'string', 'min' => 2, 'max' => 255],

      ['email', 'trim'],
      ['email', 'required'],
      ['email', 'email'],
      ['email', 'string', 'max' => 255],

      // Ensure email uniqueness
      [
        'email',
        'unique',
        'targetClass' => User::class,
        'targetAttribute' => 'email',
        'message' => 'This email is already taken.',
      ],

      ['password', 'required'],
      ['password', 'string', 'min' => 6],
    ];
  }

  /**
   * Creates a new user account.
   */
  public function signup()
  {
    if (!$this->validate()) {
      return null;
    }

    $user = new User();
    $user->name = $this->name;
    $user->email = $this->email;
    $user->password = $this->password;
    $user->image = 'default.jpg';
    $user->isAdmin = 0;

    return $user->save() ? $user : null;
  }
}
