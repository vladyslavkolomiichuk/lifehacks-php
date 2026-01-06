<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * ActiveRecord model for the "user" table.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $image
 * @property int $isAdmin
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * Returns the table name.
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * Validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['password'], 'string', 'min' => 6],
            [['isAdmin'], 'integer'],
            [['name', 'email', 'password', 'image'], 'string', 'max' => 255],
            [['email'], 'unique'],
        ];
    }

    /**
     * Finds a user by ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds a user by access token (not used).
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Finds a user by email.
     */
    public static function findByUsername($username)
    {
        return static::findOne(['email' => $username]);
    }

    /**
     * Returns the user ID.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the auth key (stub).
     */
    public function getAuthKey()
    {
        return '';
    }

    /**
     * Validates the auth key.
     */
    public function validateAuthKey($authKey)
    {
        return true;
    }

    /**
     * Sets a hashed password.
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Validates a password against the stored hash.
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Related articles.
     */
    public function getArticles()
    {
        return $this->hasMany(Article::class, ['user_id' => 'id']);
    }

    /**
     * Returns the user avatar path or a fallback image.
     */
    public function getThumb()
    {
        $path = Yii::getAlias('@webroot/uploads/') . $this->image;

        return ($this->image && file_exists($path))
            ? '/uploads/' . $this->image
            : '/uploads/no-image.png';
    }
}
