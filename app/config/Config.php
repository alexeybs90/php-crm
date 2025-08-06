<?php
namespace app\config;

class Config
{
	const IS_DEV  = true;
	const IS_PROD = !self::IS_DEV;

	const DB_HOST 	  =  '#';
	const DB_USERNAME = "#";
	const DB_PASS	  = "#";
	const DB_NAME 	  = "#";
	const DB_PORT 	  = "3306";

	const CRYPT_PASSWORD = '#';

	const USER_COOKIE_NAME = 'uid_user';

	const DB_LOG = true;
	const ADDRESS = '#';
	const APP_URL = 'https://#';

    const TYPE_FILE = [
        'jpg','jpeg','png','gif','doc','docx','csv','xls','mp4',
        'xlsx','pdf','txt','odt','eps','cdr','ai','ps','tif','rar','zip','7z',
    ];
    const TYPE_FILE_IMG = ['jpg','jpeg','png','gif'];
}
