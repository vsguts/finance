<?php

namespace app\controllers;

use app\models\Account;
use app\models\Classification;
use app\models\ClassificationCategory;
use app\models\Counterparty;
use app\models\CounterpartyCategory;
use app\models\Currency;
use app\models\form\ContactForm;
use app\models\form\UserLoginForm;
use app\models\form\UserPasswordResetRequestForm;
use app\models\form\UserResetPasswordForm;
use app\models\form\UserSignupForm;
use app\models\Transaction;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

class SiteController extends AbstractController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['signup', 'login', 'logout', 'contact', 'about', 'faq'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['signup', 'login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['contact'],
                        'roles' => ['contact_form'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['about'],
                        'roles' => ['about_page'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['faq'],
                        'roles' => ['faq_page'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

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

    public function actionIndex()
    {
        $user = Yii::$app->user;
        $dashboard = [];

        if ($user->can('transaction_view')) {
            $dashboard[] = [
                'name' => __('Transactions'),
                'link' => Url::to(['transaction/index']),
                'count' => Transaction::find()->permission()->count(),
            ];
        }

        if ($user->can('account_view')) {
            $dashboard[] = [
                'name' => __('Accounts'),
                'link' => Url::to(['account/index']),
                'count' => Account::find()->count(),
            ];
        }

        if ($user->can('classification_view')) {
            $dashboard[] = [
                'name' => __('Classifications'),
                'link' => Url::to(['classification/index']),
                'count' => Classification::find()->count(),
            ];
            $dashboard[] = [
                'name' => __('Classification categories'),
                'link' => Url::to(['classification-category/index']),
                'count' => ClassificationCategory::find()->count(),
            ];
        }

        if ($user->can('counterparty_view')) {
            $dashboard[] = [
                'name' => __('Counterparties'),
                'link' => Url::to(['counterparty/index']),
                'count' => Counterparty::find()->count(),
            ];
            $dashboard[] = [
                'name' => __('Counterparty categories'),
                'link' => Url::to(['counterparty-category/index']),
                'count' => CounterpartyCategory::find()->count(),
            ];
        }

        if ($user->can('currency_view')) {
            $dashboard[] = [
                'name' => __('Currencies'),
                'link' => Url::to(['currency/index']),
                'count' => Currency::find()->count(),
            ];
        }

        if ($user->can('user_manage')) {
            $dashboard[] = [
                'name' => __('Users'),
                'link' => Url::to(['user/index']),
                'count' => User::find()->count(),
            ];
        }

        return $this->render('index', [
            'dashboard' => $dashboard
        ]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new UserLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', __('Thank you for contacting us. We will respond to you as soon as possible.'));
            } else {
                Yii::$app->session->setFlash('error', __('There was an error sending email.'));
            }

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionFaq()
    {
        return $this->render('faq');
    }

    public function actionSignup()
    {
        $model = new UserSignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new UserPasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', __('Check your email for further instructions.'));

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', __('Sorry, we are unable to reset password for email provided.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new UserResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', __('New password was saved.'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

}
