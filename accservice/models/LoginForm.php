<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $userid;

    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

/*    public function load($param){
        var_dump(parent::load($param));
    }*/

    public function attributeLabels()
    {
        return [
            'userid' => 'Пользователь',
            'username' => 'Логин',
            'password' => 'Пароль',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['userid', 'integer'],
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный Логин или Пароль.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
        // var_dump($this->userid);exit;
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } elseif( $domainData = $this->_getDomainAccount($this->username, $this->password, "dc-servers.guso.loc", "DC=guso,DC=loc", "@guso.loc") ) {//.71 основной контроллер .72 резервный контроллре домена
            // var_dump($domainData);
            if ( $user = $this->getUser() ) {
                // залогиниться под доменкой
                // var_dump(Yii::$app->user);exit;
                return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            } else {
                // создать нового пользователя
                // залогиниться под доменкой
                $arrFIO = explode(' ', $domainData['nam']);
                $newUser = new User();
                $newUser->username   = $domainData['sam'];
                $newUser->phone_work = (string)$domainData['tel'];
                $newUser->email = $domainData['email'];
                $newUser->last_name = $arrFIO[0];
                $newUser->first_name = $arrFIO[1];
                $newUser->second_name = $arrFIO[2];
                $newUser->organization = 1; // присваиваем айдишник организации 1 - РЦУП. Она должна существовать в базе!!!
                $newUser->id = null;
                // var_dump($newUser->save());
                if ( $newUser->save() ) {
                    return Yii::$app->user->login(User::findOne($newUser->id), $this->rememberMe ? 3600 * 24 * 30 : 0);
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /*
        Вход по ЭЦП
    */
    public function loginElectronic($signString, $dataString)
    {
        $email = false;
        // проверка подписи - получение данных сертификата (на сервере!)
        // $wsdl = "http://10.0.43.58:30175/XmlSigningService/service?wsdl";
        // $wsdl = "http://10.0.43.98:30175/XmlSigningService/service?wsdl";
        // $url = 'http://'.$_SERVER['REMOTE_ADDR'].':30175/XmlSigningService/service';
        // $url = 'http://localhost:30175/XmlSigningService/service';
        $url = 'http://10.0.43.98:30175/XmlSigningService/service';
        // $url = 'http://10.0.43.58:30175/XmlSigningService/service';
        $wsdl = "servicewsdl/service_1.wsdl"; //лучше локально положить на сервер
        $client = new \SoapClient( $wsdl,
            [
                "location" => $url,
                "uri"      => "http://tempuri.org/IXmlSigningService/VerifyString",
                "style"    => SOAP_RPC,
                "use"      => SOAP_ENCODED
            ]
        );
        if (!$client) {
            Yii::$app->end('Невозможно связаться с сервисом подписи!');
        }
        $params = [];
        $params["base64Data"] = $signString;
        $params["base64Signatures"]["string"] = $dataString;
        try {
            $result = $client->GetSignaturesInfo($params); // var_dump($result); exit;
            if ( $result->GetSignaturesInfoResult->SignatureInfoResult->ErrorCode == 0 ) {
                $fio = $result->GetSignaturesInfoResult->SignatureInfoResult->Certificates->CertificateInfo->Subject->CommonName;
                $email = $result->GetSignaturesInfoResult->SignatureInfoResult->Certificates->CertificateInfo->Subject->Email;
                // Для нового сертификата выбираем данные сертификата по-новому из "сырой" строки
                // Используем для этого написанную функцию парсинга $this->parseFullSubject()
                $FullSubject = $result->GetSignaturesInfoResult->SignatureInfoResult->Certificates->CertificateInfo->FullSubject;
                $FullSubject = $this->parseFullSubject($FullSubject);
                if ( isset($FullSubject['SN']) ) {
                    $fio = $FullSubject['SN'].' '.$FullSubject['G'];
                    $email = $FullSubject['E'];
                }
            } else {
                Yii::$app->end('Неверная сигнатура подписи!');
            }
        } catch (\SoapFault $e) {
            Yii::$app->end('Ошибка при обращении к сервису подписи!');
            // echo "SOAP Fault: ".$e->getMessage()."<br />\n";
        }
        if ( $email ) {

		$user = User::findOne(['email' => $email]);
            if ( $user && ($user->getFullName() === $fio)) {
                // если залогинит, то вернет true
                return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            }
            // Yii::$app->end($fio.' '.$user->getFullName());
            Yii::$app->end('Пользователь с данной ЭЦП не зарегистрирован в системе');
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
            // $this->_user = User::findOne($this->userid);
        }
        // var_dump($this->_user);exit;

        return $this->_user;
    }

    /*  Получение данных доменного
    аккаунта
    */
    private function _getDomainAccount($name, $pass, $host, $d, $domain)
    {
        $SearchFor =        $name;            // Строка для поиска в Account Domen - е
        $SearchField =      "samaccountname"; // Поле в Account Domen - е, покоторому делаем поиск
        $LDAPHost =         $host;            // Адрес Account Domen - е
        $dn =               $d;               // Base DN в Account Domen - е
        $LDAPUserDomain =   $domain;          // Домен
        $LDAPUser =         $name;            // Логин
        $LDAPUserPassword = $pass;            // Пароль

/////////////////////

//	$file = '/tmp/file_log_pass';
//	$dt = date("d.m.y");
//	$vr = date("H:i:s");
//	file_put_contents($file, " $dt $vr $name $pass;", FILE_APPEND);

/////////////////////

        $LDAPFieldsToFind = array("cn", "givenname", "samaccountname", "telephonenumber", "mail");

        // $cnx = ldap_connect($LDAPHost) or exit("Ошибка подключения к LDAP"); // todo: обойти exit
        if ( !$cnx = ldap_connect($LDAPHost) ) {
            $this->addError('username', 'Ошибка подключения к LDAP');
            return false;
        }
        ldap_set_option($cnx, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($cnx, LDAP_OPT_REFERRALS, 0);
        // debug_zval_dump($cnx);
        // var_dump($cnx, $LDAPUser, $LDAPUserDomain, $LDAPUserPassword);exit;
        try {
            ldap_bind($cnx, $LDAPUser.$LDAPUserDomain, $LDAPUserPassword);
        } catch (\yii\base\ErrorException $e) {
            return false;
        }

        $filter="($SearchField=$SearchFor*)"; //Маска для поиска (* - имщем все совпадения)
        $sr=ldap_search($cnx, $dn, $filter, $LDAPFieldsToFind);
        $info = ldap_get_entries($cnx, $sr);
        if (!$info) return false;
        // if ( $info["count"] > 1 ) exit('Пользователей с таким именем и паролем в домене более чем 1'); // todo: обойти exit
        if ( $info["count"] > 2 ) {
            $this->addError('username', 'Пользователей с таким именем и паролем в домене более чем 1');
            return false;
        }
        // var_dump($info[0]);
        $res = array();
        $res['sam'] =   $info[0]['samaccountname'][0];
        $res['giv'] =   $info[0]['givenname'][0];
        $res['tel'] =   $info[0]['telephonenumber'][0];
        $res['email'] = $info[0]['mail'][0];
        $res['nam'] =   $info[0]['cn'][0];
        return $res;
    }

    /*  Парсинг строки FullSubject,
    возвращаемой в классе Методом GetSignaturesInfo сервиса подписи
    возвращает массив распарсенных данных с Именоваными ключами
    */
    public function parseFullSubject($string){
        $keyString = '';
        $valueString = '';
        $arrResult = array();
        for ($i = strlen($string) - 1 , $arrkey = 0, $thekey = false; $i >= -1; $i--) {
            if ($thekey) {
                if ($i >= 0 && $string[$i] != ',') {
                    $keyString .= $string[$i];
                } else {
                    $arrResult[trim(strrev($keyString))] = strrev($valueString);
                    $keyString = '';
                    $valueString = '';
                    $thekey = false;
                }
                continue;
            }
            if ($string[$i] != '=') {
                $valueString .= $string[$i];
            } else {
                $arrkey++;
                $thekey = true;
            }
        }
        foreach ($arrResult as $key => $value) {
            if( $value[0] == '"' && $value[strlen($value)-1] == '"' )
                $arrResult[$key] = substr($arrResult[$key], 1, strlen($value)-2);
        }
        return $arrResult;
    }
}
