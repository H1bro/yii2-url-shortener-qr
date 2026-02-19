<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $short_url_id
 * @property string $visitor_ip
 * @property string|null $user_agent
 * @property int $visited_at
 *
 * @property-read ShortUrl $shortUrl
 */
class ShortUrlVisitLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%short_url_visit_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['short_url_id', 'visitor_ip', 'visited_at'], 'required'],
            [['short_url_id', 'visited_at'], 'integer'],
            [['visitor_ip'], 'string', 'max' => 45],
            [['user_agent'], 'string', 'max' => 1024],
            [['short_url_id'], 'exist', 'targetClass' => ShortUrl::class, 'targetAttribute' => ['short_url_id' => 'id']],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getShortUrl()
    {
        return $this->hasOne(ShortUrl::class, ['id' => 'short_url_id']);
    }
}
