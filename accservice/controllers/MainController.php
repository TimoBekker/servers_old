<?php
namespace app\controllers;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Main Controller
 * От этого класса будем наследовать все контроллеры в системе (в т.ч. и в модулях)
 * В нем сделаем общую примесь для управления доступом. Общие правила для всех контроллеров
 * В наследуемых контроллерах Переопределяемые примеси будем мержить с примесью этого кон-ра
 */
class MainController extends Controller
{
    // общая примесь
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['*'], // вначале запрещаем доступ всем
                    ],
                    // [
                    //     'allow' => true,
                    //     'roles' => ['@'], // Открываем доступ только автори
                    // ],
                    [
                        'allow' => true,
                        'roles' => ['administrator'], // Открываем ко всему доступ админу
                    ],
                    [
                        // страницу с сообщениями об ошибках видят все зарегеные
                        'actions' => ['error'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
}
