<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $name
 * @property string $login
 * @property string $password
 * @property string $image
 * @property int $isAdmin
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * Вказуємо назву таблиці в БД
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * Правила валідації (потрібні для Gii та збереження даних)
     */
    public function rules()
    {
        return [
            [['name', 'login', 'password'], 'required'],
            [['isAdmin'], 'integer'],
            [['name', 'login', 'password', 'image'], 'string', 'max' => 255],
            [['login'], 'unique'], // Логін (email) має бути унікальним
        ];
    }

    /**
     * --- МЕТОДИ ІНТЕРФЕЙСУ IdentityInterface (Для авторизації) ---
     */

    /**
     * Знаходить ідентиті (користувача) за ID.
     * Саме тут використовується findOne, який викликав помилку.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Знаходить користувача за токеном доступу (зазвичай для API).
     * Ми поки залишаємо пустим або null, бо у нас сесійна авторизація.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Знаходить користувача за логіном (використовується нами вручну).
     */
    public static function findByUsername($username)
    {
        return static::findOne(['login' => $username]);
    }

    /**
     * Повертає ID поточного користувача.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Повертає ключ автентифікації (для cookie "Запам'ятати мене").
     * Оскільки в нашій простій таблиці немає поля auth_key, повертаємо null або можна додати поле в БД.
     * Для курсової часто достатньо заглушки, якщо "remember me" не критичний.
     */
    public function getAuthKey()
    {
        // У повноцінному проекті тут треба повертати $this->auth_key;
        return null;
    }

    /**
     * Валідує ключ автентифікації.
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Перевірка пароля.
     *
     * @param string $password пароль для перевірки
     * @return bool чи збігається пароль
     */
    public function validatePassword($password)
    {
        // Варіант 1: Для нашого поточного коду (без шифрування, як у вас зараз)
        return $this->password === $password;

        /* Варіант 2: Якщо в майбутньому ви почнете хешувати паролі (рекомендовано):
        return Yii::$app->security->validatePassword($password, $this->password);
        */
    }

    public function getArticles()
    {
        // Зв'язок: User має багато Articles (по полю user_id)
        return $this->hasMany(Article::class, ['user_id' => 'id']);
    }
}
