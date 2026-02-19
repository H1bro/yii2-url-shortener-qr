<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $original_url
 * @property string $short_code
 * @property int $visits_count
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read ShortUrlVisitLog[] $visitLogs
 */
class ShortUrl extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%short_url}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['original_url', 'short_code'], 'required'],
            [['original_url'], 'string'],
            [['visits_count', 'created_at', 'updated_at'], 'integer'],
            [['short_code'], 'string', 'max' => 32],
            [['short_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getVisitLogs()
    {
        return $this->hasMany(ShortUrlVisitLog::class, ['short_url_id' => 'id']);
    }
}
