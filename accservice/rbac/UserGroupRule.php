<?php
namespace app\rbac;

/*
    Определяем свои Класс правила, которое мы можем привязать к группе. В то числе и в БД в таблице auth_items
    Чтобы в дальнейшем записать данное правило в БД надо воспользоваться консоль командой yii rbac/init, которая
    доступна только в приложении advanced. Эта команда сгенерит 2 файла. Один из которых будет возвращать массив
    сериализованных правил. Поскольку у нас инзначально не advanced использовался, то мы просто должны где-нибудь
    исполнить следующую комманду var_dump(serialize(new \app\rbac\UserGroupRule));exit; А потом скопировать прав
    ило в соотв. нужную строку таблицы auth_rule.
    Данные правила мы можем использовать по своему умотрению. Правило не расширит те разрешающие правила, которые
    мы определили в контроллерах для доступа к экшенам. Но могут запретить все, если вернут false.
*/

use Yii;
use yii\rbac\Rule;

class UserGroupRule extends Rule
{
    public $name = 'userGroup';

    public function execute($user, $item, $params)
    {
        /*if (!\Yii::$app->user->isGuest) {
            $group = \Yii::$app->user->identity->group;
            if ($item->name === 'administrator') {
                return $group == 'administrator';
            } elseif ($item->name === 'default') {
                return $group == 'administrator' || $group == 'default';
            }
        }*/
        return true;
    }
}