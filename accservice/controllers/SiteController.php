<?php
namespace app\controllers;

use Yii;
use app\models\LoginForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\SignupForm;
use app\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use app\models\ViewSearch;
use app\models\registry\REvent;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class SiteController extends MainController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'except' => ['login', 'logout'], // выходить и пытаться входить могут все
                    // 'only' => ['logout', 'signup', 'about'],
                    'rules' => [
    /*                    [
                            'actions' => ['signup'],
                            'allow' => true,
                            'roles' => ['?'],
                        ],
                        [
                            'actions' => ['logout'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        // my
                        [
                            'actions' => ['about'],
                            'allow' => true,
                            'roles' => ['contact-view'],
                        ],*/
                        [
                            'actions' => ['index', 'search'],
                            'allow' => true,
                            'roles' => ['default'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'logout' => ['post'],
                    ],
                ],
            ]
        );
    }

    /**
     * @inheritdoc
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

    public function actionIndex()
    {
        return $this->render('index',[
            'events' => (new REvent)->listEventsForIndex()
        ]);
    }

    // поиск по сайту (по сущностям)
    public function actionSearch()
    {
        $searchstring = Yii::$app->request->get('searchstring');
        $model = new ViewSearch();
        $dataProvider = $model->search($searchstring ? $searchstring : '');
        $dataProvider->pagination = new \yii\data\Pagination(['defaultPageSize' => 50]);

        return $this->render('searchresult', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLogin()
    {

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        $this->layout = 'login';
        // var_dump(Yii::$app->request->post());
/*        return $this->render('login', [
            'model' => $model,
        ]);*/
        // var_dump('stop');exit;

        // если пришел аякс то логинимся по ЭЦП
        if ( Yii::$app->request->isAjax ) {
            // Yii::$app->end('тестаякс'); // аналог exit('тестаякс');
            // exit(Yii::$app->request->post('signString').' '.Yii::$app->request->post('dataString'));
            $signString = (string)Yii::$app->request->post('signString');
            $dataString = (string)Yii::$app->request->post('dataString');
            if ($model->loginElectronic($signString, $dataString)) {
                return $this->goBack();
            }
            Yii::$app->end(); // заканчиваем скрипт
        }

        // если пришла форма то логинимся по паролю и логину
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

        // return $this->goHome();

        // в нашем случае при выходе сразу выкатываемся на страницу входа
        // поскольку анонимные пользователи не должны вообще иметь доступ ни к какой странице
        return $this->redirect(\Yii::$app->urlManager->createUrl(['site/login']));
    }

    /*
    public function actionContact()
    {
        if (Yii::$app->user->can('contact-view')) {
            $model = new ContactForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                    Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
                } else {
                    Yii::$app->session->setFlash('error', 'There was an error sending email.');
                }

                return $this->refresh();
            } else {
                return $this->render('contact', [
                    'model' => $model,
                ]);
            }
        }
        else{
            //echo "<pre>";
            // var_dump($this->authManager->getRoles());
            // var_dump($this->getModules());

            // var_dump(get_class($this));
            // var_dump(get_object_vars($this));
            //$rule = new yii\rbac\Rule;
            // $rule->allow = false;
            // $rule->actions = ['contact'];
            // $rule->controllers = ['site'];

            //var_dump($rule);
            // file_put_contents("c:\\bodarule.txt", serialize($rule));
            throw new ForbiddenHttpException;
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
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
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
    */
}
