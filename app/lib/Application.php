<?php
namespace app\lib;

use app\config\Config;
use app\models\User;

class Application
{
    public static $module = null;
	public static $document_root = null;
	public static $urlSite = null;
	public static User $user;
    const MODULE_PATH = 'app\\modules\\';
    const HOME_PAGE_MODULE = 'HomePage';

	public static function init()
	{
        self::$document_root = $_SERVER["DOCUMENT_ROOT"] . '/../../';

        if (isset($_SERVER['HTTP_HOST'])) {
            self::$urlSite = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        }

		$go404 = 0;
		$pageData = [];
		$pageURL = $_GET['menu'] ?? '';
        $pageModule = $_GET['m'] ?? '';

        $pageModules = require_once self::$document_root .'app/config/pageModules.php';
        if (isset($pageModules[$pageURL])) $pageModule = $pageModules[$pageURL];

        $pageURLWithNoAuth = [
            'import-files', 'stamp-xml', 'form-lak-xml', 'stamp-xml',
            'order-flexo-report-pdf', 'test-page', 'order-hp-report-pdf'
        ];
        $pageModuleWithNoAuth = [
            'BotHandler', 'BotInfo', 'PatternFilmXML', 'StampXML',
            'OrderFlexoReportPDF', 'TestPage', 'OrderHPReportPDF'
        ];
        User::$crypt_password = Config::CRYPT_PASSWORD;
        $arr = User::login();
        self::$user = $arr['user'];

		if (!in_array($pageURL, $pageURLWithNoAuth)
            && !in_array($pageModule, $pageModuleWithNoAuth)
            && !User::isAuth()) {
			$pageModule = 'Auth';
		} elseif ($pageModule) {
            //TODO: вся работа с БД => в репозитории
			$data = DBPDO::getDataOne("SELECT * FROM menu_workers WHERE php=:module", ['module' => $pageModule]);
			if (!empty($data)) {
				$pageData['page'] = $data;
			}
		} elseif ($pageURL) {
			// /menu/
			$data = DBPDO::getDataOne("SELECT * FROM menu_workers WHERE urlname=:url", ['url' => $pageURL]);
			if (!empty($data)) {
				$pageData['page'] = $data;
				$pageModule = $data['php'];
			} else $go404 = true;
		} else {
			$pageData['page'] = array('id' => null, 'php' => '', 'name' => '');
			$pageModule = self::HOME_PAGE_MODULE;
		}

        $fullClassName = self::MODULE_PATH . $pageModule;

		if (!$pageModule || !class_exists($fullClassName)) {
		    $go404 = true;
        }
		if ($go404) $pageModule = 'Page404';

		if ($pageModule && class_exists($fullClassName)) {
			self::$module = new $fullClassName($pageData);
            return;
		}
        print 'module not found';
	}

	public static function run()
    {
        self::init();
        self::$module?->show();
        DBPDO::dbClose();
		die();
	}
}