<?php
namespace app\models;

use app\config\Config;
use app\lib\DBPDO;
use app\models\model\Model;

class User extends Model
{
    public int $id = 0;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $login = '';
    public string $password = '';
    public string|int $type_worker_id = 0;

	public static $crypt_password = null;
	private static $is_auth = false;
    public static $names = [];

	const ID_MANAGER = 1;
	const ID_PRINTER = 2;
	const ID_USER = 3;
	const ID_ADMIN = 4;
	const ID_TECHNOLOGIST = 5;
    const ID_DESIGNER = 6;
	const ID_BUHGALTER = 7;

	public static function auth($lg='',$ps='')
    {
		$ip = $_SERVER["REMOTE_ADDR"];
		$login = substr($lg,0,100);
		$login = htmlspecialchars(stripslashes($login));
		$ps = md5(substr($ps,0,50));
		$crypt = $login.'[-]'.$ps.'[-]'.$_SERVER['HTTP_HOST'].'[-]'.$ip;
		$cipher = Encrypt::xxtea_encrypt($crypt, self::$crypt_password);
	    $cipher = base64_encode($cipher);
		setcookie(Config::USER_COOKIE_NAME, $cipher, time() + 3000000, '/');
		$_COOKIE[Config::USER_COOKIE_NAME] = $cipher;
    }

	public static function login()
    {
		$userData = array();
		if(isset($_COOKIE[Config::USER_COOKIE_NAME]))
		{
			$admin_login = base64_decode($_COOKIE[Config::USER_COOKIE_NAME]);
			$a_cook = Encrypt::xxtea_decrypt($admin_login, self::$crypt_password);
			$b_cook = explode('[-]',$a_cook);
			if (count($b_cook) > 1) {
				$login = $b_cook[0];
				$password = substr($b_cook[1], 0, 50);
			}
			if (isset($login) && isset($password) && $login && $password) {
                //TODO: вся работа с БД => в репозитории
			    $query="SELECT * FROM workers WHERE isFired!=1 AND login=:login AND password=:password";
                $userData = DBPDO::getDataOne($query, ['login' => $login, 'password' => $password]);
			    if ($userData) {
					self::$is_auth = true;
				}
			}
		}
		return array('is_auth' => self::$is_auth, 'user' => new self($userData));
	}
	
	public static function isAuth()
	{
		return self::$is_auth;
	}
	
	public static function unlogin()
	{
		self::$is_auth = false;
		setcookie(Config::USER_COOKIE_NAME, '', time() + 3000000, '/');
		return self::$is_auth;
	}
	
	public static function getAccessLevels($idmenu, $type_worker)
	{
		if (self::$is_auth) {
			$query = "select * from access_levels where idmenu=:idmenu and id_type_worker=:id_type_worker";
			$lvl= DBPDO::getData($query, ['idmenu' => $idmenu, 'id_type_worker' => $type_worker]);
			if(!empty($lvl)) return $lvl;
		}
		return array();
	}

	public static function getNameById($id, $onlyLastName = false): string
    {
	    if (!isset(self::$names[$id])) {
            self::$names[$id] = '';
            $user = DBPDO::getDataOne("SELECT last_name, first_name FROM users WHERE id=:id", ['id' => $id]);
            if ($user) {
                self::$names[$id] = $user['last_name'] . (!$onlyLastName ? ' ' . $user['first_name'] : '');
            }
        }
        return self::$names[$id];
    }

    public static function generatePassword($length): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        $characterCount = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, $characterCount - 1);
            $password .= $characters[$index];
        }
        return $password;
    }
}