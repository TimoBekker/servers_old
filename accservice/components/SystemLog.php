<?php
namespace app\components;

use yii\base\BaseObject;
use app\models\User;

/**
* @property eventCodes отражает коды событий и степень важности событий в системе логирования
*/

class SystemLog extends BaseObject
{
    public $eventCodes = [
    	101 => 'Информация',
    	102 => 'Предупреждение',
    	401 => 'Информация',
        402 => 'Предупреждение',
    	405 => 'Предупреждение',
    	201 => 'Информация',
    	202 => 'Предупреждение',
    	403 => 'Информация',
        404 => 'Предупреждение',
    	406 => 'Предупреждение',
    	301 => 'Информация',
    	302 => 'Предупреждение',
    	501 => 'Предупреждение',
    	502 => 'Предупреждение',
    	503 => 'Предупреждение',
    	504 => 'Предупреждение',
    	601 => 'Информация',
    	602 => 'Информация',
    	701 => 'Информация',
    ];
/*
    public function __construct($param1, $param2, $config = [])
    {
    	//
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        //
    }
*/
    public function iavailable(){
    	exit("я доступен");
    }

    /**
    * вызов логирования в таблицу логов Yii::$app->systemlog->dolog(102, "sdfsdf");
    */

    public function dolog($user_id, $code, $message, $objBefore = "", $objAfter = ""){
    	if(!array_key_exists($code, $this->eventCodes))
    		throw new \Exception("Кода регистрируемого события не существует");
        $grade = $this->eventCodes[$code];
        $user = User::findOne($user_id);
        $user_data = $user->last_name.' '.$user->first_name.' '.$user->second_name.' ('.$user->organization0->name.')';
        $message = addslashes($message);
        $objBefore = addslashes($objBefore);
        $objAfter = addslashes($objAfter);
    	$db = \Yii::$app->db;
    	$res = $db->createCommand(
    		"INSERT INTO `r_log` (`code`, `grade`, `date_emergence`, `user`, `content`, `object_before`, `object_after`)
    			  VALUES ($code, '{$grade}', now(), '{$user_data}', '{$message}', '{$objBefore}', '{$objAfter}')")
    		->execute();
    }
}