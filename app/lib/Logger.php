<?php
namespace app\lib;

class Logger
{
    public $resource = null;

    public function __construct($filename = "upload/temp/log.txt")
    {
        $this->resource = fopen(Application::$document_root . $filename, "a");
    }

    public function write($data): bool|int
    {
        if (!is_string($data)) {
            $data = print_r($data, true);
        }

        return fwrite(
            $this->resource,
            date('Y-m-d H:i:s')
            . (Application::$module && Application::$module->user ? ' userId=' . Application::$module->user['id'] : '')
            . (isset($_POST['work']) ? ' post[work]=' . $_POST['work'] : '')
            . ' ' . $_SERVER['REQUEST_URI'] . ' ' . ($_SERVER['HTTP_REFERER'] ?? '')
            ."\n" . $data . "\n\n"
        );
    }

    public function close() {
        fclose($this->resource);
    }
}