<?php

namespace app\controllers;

use app\models\ContactForm;
use app\models\LoginForm;
use app\models\ShortenUrlForm;
use app\services\ShortUrlService;
use app\services\UrlAvailabilityChecker;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'create-short-url' => ['post'],
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'only' => ['create-short-url'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return array
     */
    public function actionCreateShortUrl(): array
    {
        $form = new ShortenUrlForm();
        $form->load(Yii::$app->request->post(), '');

        if (!$form->validate()) {
            return [
                'success' => false,
                'message' => reset($form->getFirstErrors()) ?: 'Некорректный URL.',
            ];
        }

        $checker = new UrlAvailabilityChecker();
        if (!$checker->isAvailable($form->url)) {
            return [
                'success' => false,
                'message' => 'Данный URL не доступен',
            ];
        }

        $shortUrlService = new ShortUrlService();

        try {
            $shortUrlModel = $shortUrlService->create($form->url);
            $shortUrl = $shortUrlService->getShortUrl($shortUrlModel);

            return [
                'success' => true,
                'message' => 'Короткая ссылка успешно создана.',
                'shortUrl' => $shortUrl,
                'qrCodeDataUri' => $shortUrlService->makeQrCodeDataUri($shortUrl),
                'visitsCount' => (int)$shortUrlModel->visits_count,
            ];
        } catch (\Throwable $exception) {
            Yii::error($exception->getMessage(), __METHOD__);

            return [
                'success' => false,
                'message' => 'Не удалось создать короткую ссылку.',
            ];
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
