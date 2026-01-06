<?php

namespace app\models;

use Yii;

/**
 * ActiveRecord model for the "topic" table.
 *
 * @property int $id
 * @property string|null $name
 * @property Article[] $articles
 */
class Topic extends \yii\db\ActiveRecord
{
    /**
     * Returns the table name.
     */
    public static function tableName()
    {
        return 'topic';
    }

    /**
     * Validation rules.
     */
    public function rules()
    {
        return [
            [['name'], 'default', 'value' => null],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * Attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Related articles.
     */
    public function getArticles()
    {
        return $this->hasMany(Article::class, ['topic_id' => 'id']);
    }
}
