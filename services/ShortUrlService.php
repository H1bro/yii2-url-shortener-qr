<?php

namespace app\services;

use app\models\ShortUrl;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use yii\db\Exception as DbException;
use yii\helpers\Url;

class ShortUrlService
{
    private const SHORT_CODE_LENGTH = 8;
    private const MAX_GENERATION_ATTEMPTS = 10;

    /**
     * @param string $originalUrl
     * @return ShortUrl
     * @throws DbException
     */
    public function create(string $originalUrl): ShortUrl
    {
        $existing = ShortUrl::find()->where(['original_url' => $originalUrl])->one();
        if ($existing instanceof ShortUrl) {
            return $existing;
        }

        $model = new ShortUrl();
        $model->original_url = $originalUrl;

        for ($i = 0; $i < self::MAX_GENERATION_ATTEMPTS; $i++) {
            $model->short_code = $this->generateCode();
            if ($model->save()) {
                return $model;
            }

            if (!$model->hasErrors('short_code')) {
                break;
            }
            $model->clearErrors('short_code');
        }

        throw new DbException('Не удалось сгенерировать уникальный короткий код.');
    }

    /**
     * @param ShortUrl $shortUrl
     * @return string
     */
    public function getShortUrl(ShortUrl $shortUrl): string
    {
        return Url::to(['/redirect/go', 'code' => $shortUrl->short_code], true);
    }

    /**
     * @param string $shortUrl
     * @return string
     */
    public function makeQrCodeDataUri(string $shortUrl): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($shortUrl);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * @return string
     */
    private function generateCode(): string
    {
        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxIndex = strlen($alphabet) - 1;
        $code = '';

        for ($i = 0; $i < self::SHORT_CODE_LENGTH; $i++) {
            $code .= $alphabet[random_int(0, $maxIndex)];
        }

        return $code;
    }
}
