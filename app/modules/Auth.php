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
        ?>
        <div id="app">
            <auth></auth>
        </div>
        <?php
    }
}