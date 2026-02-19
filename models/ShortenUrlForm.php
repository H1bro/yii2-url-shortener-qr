<?php

namespace app\models;

use yii\base\Model;

class ShortenUrlForm extends Model
{
    public string $url = '';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url'], 'required'],
            [['url'], 'trim'],
            [['url'], 'string', 'max' => 2048],
            [['url'], 'url'],
            [['url'], 'validateScheme'],
        ];
    }

    /**
     * @param string $attribute
     */
    public function validateScheme($attribute): void
    {
        $scheme = parse_url($this->$attribute, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'], true)) {
            $this->addError($attribute, 'Поддерживаются только URL со схемой http или https.');
        }
    }
}
