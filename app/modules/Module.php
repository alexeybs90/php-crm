<?php
namespace app\modules;

use app\lib\Application;

class Module
{
	public $pageId = 0;
	public $document_root = '';
	public $pageData = array();

	public $cssFiles = array();
	public $jsFiles = array();
	public $jsFilesBottom = array();

	public function __construct($pageData = array())
	{
		$this->document_root = \app\lib\Application::$document_root;
//		die($this->document_root);
		if (!empty($pageData)) {
			if (!empty($pageData['page'])) {
				$this->pageId = $pageData['page']['id'];
				$this->pageData = $pageData['page'];
			}
		}
		$this->init();
	}

	public function show() {

		if ( $_SERVER['REQUEST_METHOD'] == "POST" ) $this->doPost();
		else $this->doGet();
	}

	public function doPost() {
	}

	public function doGet() {
		$this->loadView( $this->viewIndex() );
	}

    public function init() {
    }

	public function showBody()	{
	}

	public function showBeforeBody() {
	}

	public function showAfterBody()	{
	}

    //
	public function viewIndex()	{
		return 'index';
	}

    public function loadCSS()  {
        foreach($this->cssFiles as $file => $on) {
            if(!$on) continue;
            ?>
            <link rel="stylesheet" type="text/css" href="<?=$file ?>"/>
            <?php
        }
    }

    public function loadJS()  {
        foreach($this->jsFiles as $file => $on) {
            if(!$on) continue;
            ?>
            <script type="text/javascript" src="<?=$file ?>"></script>
            <?php
        }
    }

    public function loadJSBottom()  {
        foreach($this->jsFilesBottom as $file => $on) {
            if(!$on) continue;
            ?>
            <script type="text/javascript" src="<?=$file ?>"></script>
            <?php
        }
    }

	//подгружает шаблон по названию файла и передает в него данные
	public function loadView($file, $params=array())
	{
		$pathTo = Application::$document_root . '/app/views/';
		if($file && file_exists($pathTo . $file.'.php')) {
			require($pathTo . $file . '.php');
		}
	}

}
