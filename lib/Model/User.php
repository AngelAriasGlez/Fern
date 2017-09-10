<?php
namespace fw\Model;

use \fw\Data\Query\Where;
class User extends \fw\Data\Record{
    private static $SessionKey = 'L_LOGIN';
    private static $TokenKey = 'sS%4$ae(fE_u';
    /**
     * @type String
     */
    public $PreviousAccess;
    /**
     * @type String
     */
    public $Password;
    /**
     * @type String
     */
    public $Username;
    /**
     * @type String
     */
    public $Email;

    public function sessionLogIn(){
        if($pk = $this->pk()){
            $_SESSION[self::$SessionKey] = $pk;
            $this->PreviousAccess = time();
            $this->save();
            return true;
        }
        return false;
    }
    public function sessionLogOut(){
        $_SESSION[self::$SessionKey] = false;
        return session_destroy();
    }
    public static function sessionGetLogged(){
        if(isset($_SESSION[self::$SessionKey])){
            if($_SESSION[self::$SessionKey] !== false){
                if($x = static::getRepository()->findByPk((int)$_SESSION[self::$SessionKey])){
                    return $x;
                }
            }
        }
        return null;
    }

    public function getToken(){
        if($this->pk()) {
           return \JWT::encode([$this->pk(), time()], self::$TokenKey);
        }
        return null;
    }
    public static function findByToken($token, $tokenExpireDays=365){
        $expireSeconds = $tokenExpireDays * 24 * 3600;
        $obj = \JWT::decode($token, self::$TokenKey);
        if($obj[1] + $expireSeconds < time()) return null;
        return static::getRepository()->findByPk($obj[0]);
    }

    public static function findByEmail($email){
        if($user = self::getRepository()->findBy('Email', $email)){
            return $user->first();
        }
        return null;
    }
    public static function findWithUsernameCredentials($username, $password){
        return static::findWithCredentials('Username', $username, $password);
    }
    public static function findWithEmailCredentials($email, $password){
        return static::findWithCredentials('Email', $email, $password);
    }

    /**
     * @param $field
     * @param $fieldvalue
     * @param $password
     * @return \fw\Model\User|null
     */
    private static function findWithCredentials($field, $fieldvalue, $password){
        $where = Where::build()
            ->equals($field, $fieldvalue)->_and()
            ->equals('Password', static::encriptPassword($password));
        $user = static::getRepository()->query()->where($where)->exec()->first();
        if($user !== null){
            return $user;
        }
        return null;
    }

    private static function encriptPassword($pass){
        return $pass;
        //return sha1($pass);
    }
}
