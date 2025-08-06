<?php
namespace app\modules;

class HomePage extends BasePage {

    public $table = '';
//    public $check_pass = false;

    public function init() {
        parent::init();

        $this->top_items = array('exit'=>'');
    }
    
    public function ajaxSave($params = array()) {}

    public function ajaxForm($params = array()) {}

    public function ajaxList($params = array()) {}

    public function showContent($params = array())
    {
        ?>
        <h1>Welcome, <?=$this->user->first_name . ' ' . $this->user->last_name ?></h1>
        <?php
    }

}
?>