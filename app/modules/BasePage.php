<?php
namespace app\modules;

use app\lib\Application;
use app\lib\Course;
use app\lib\Helper;
use app\models\User;
use app\services\ExcelService;
use app\config\Config;

class BasePage extends Module
{
    const META_MOBILE_HEAD = '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
    public User $user;
    public $pageData = [];

    public $access_level = [];
    public $title = '';
    public $isAjax = false;

    public $top_items = array('add'=>'','del'=>'','edit'=>'','print'=>'','exit'=>'');
    public array $course = [];

    public function doPost()
    {
        if (isset($_POST['authUser']) and $_POST['authUser'] == 1) {
            $login = $_POST['login'];
            $pass = $_POST['pass'];
            User::auth($login, $pass);
            $log = $this->login();
            $arr=array('message'=>'', 'href'=>'', 'showMess' => 'toError');
            if(!$log) {
                $arr['message']='<img src="/images/notpass.jpg" alt="notpass" style="width:172px;"><br>Неверный логин или пароль';
            }
            print json_encode($arr);
            return;
        }

        if (isset($_POST['exitUser']) and $_POST['exitUser']!='') {
            User::unlogin();
            Helper::go('/');
        }

        if (isset($_POST['work']) && User::isAuth()) {
            $method = 'ajax' . ucfirst($_POST['work']);
            if(method_exists($this, $method)) {
                $this->$method($_POST);
            }
        }
    }

    public function init()
    {
        $this->user = Application::$user;
        if ($this->isAuth()) {
            $this->access_level = User::getAccessLevels($this->pageId, $this->user->type_worker_id);
        }
        $this->course = Course::getCourse();

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') $this->isAjax = true;

        if ($this->pageData) $this->title = $this->pageData['name'];
        if (!$this->title) $this->title = 'MANAGER PANEL';
    }

    public function login()
    {
        User::$crypt_password = Config::CRYPT_PASSWORD;
        $arr = User::login();
        $this->user = $arr['user'];
        return User::isAuth();
    }

    public function isAuth()
    {
        return User::isAuth();
    }

    public function isAdmin(): bool
    {
        return $this->user->type_worker_id === User::ID_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->user->type_worker_id === User::ID_MANAGER;
    }
    public function isTechnologist(): bool
    {
        return $this->user->type_worker_id === User::ID_TECHNOLOGIST;
    }

    public function unlogin()
    {
        return User::unlogin();
    }

    public function getAccessLevels()
    {
        return $this->access_level;
    }

    public function getXLS($xls): array
    {
        $excel = new ExcelService();
        return $excel->readFile($xls);
    }
}