<?php

namespace app\controllers;

use app\models\ShortUrl;
use app\models\ShortUrlVisitLog;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RedirectController extends Controller
{
    /**
     * @param string $code
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionGo(string $code): Response
    {
        $shortUrl = ShortUrl::find()->where(['short_code' => $code])->one();
        if (!$shortUrl instanceof ShortUrl) {
            throw new NotFoundHttpException('Короткая ссылка не найдена.');
        }

        $log = new ShortUrlVisitLog([
            'short_url_id' => $shortUrl->id,
            'visitor_ip' => Yii::$app->request->userIP ?? 'unknown',
            'user_agent' => Yii::$app->request->userAgent,
            'visited_at' => time(),
        ]);

        if (!$log->save()) {
            Yii::warning($log->errors, __METHOD__);
        }

        $shortUrl->updateCounters(['visits_count' => 1]);

        return $this->redirect($shortUrl->original_url, 302);
    }
}
