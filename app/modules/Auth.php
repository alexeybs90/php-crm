<?php
namespace app\modules;

class Auth extends BasePage {

    public function init() {
        $this->login();
        $this->cssFiles['/css/Auth.css'] = true;
        $this->jsFilesBottom['/js/Auth.js'] = true;
    }

    public function showContent()
    {
        //vue component auth
        ?>
        <div id="app">
            <h2>Авторизация</h2>
            <auth></auth>
        </div>
        <?php
    }
}