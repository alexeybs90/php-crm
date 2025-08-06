<?php

namespace app\services;

use app\lib\Application;
use app\models\Maintenance;
use app\models\MaintenanceFile;
use app\repositories\maintenance\MaintenanceFileRepository;

class MaintenanceFileService {
    protected MaintenanceFileRepository $repository;
    protected string $error = '';

    const UPLOAD_PATH_TEMP = 'upload/temp/';

    public static function create(MaintenanceFileRepository $repository): MaintenanceFileService
    {
        $s = new static();
        $s->repository = $repository;
        return $s;
    }

    public function fetchListByMaintenanceIds(array $maintenances): array
    {
        $ids = array_map(fn($a) => $a->id, $maintenances);
        return $this->repository->fetchListByMaintenanceIds($ids);
    }

    public function saveFiles(Maintenance $model, $newFiles = []): void
    {
        $error = '';
        $table = $this->repository->table();
        $fieldName = 'files';
        if (isset($newFiles) && is_array($newFiles)) {
            $DOCUMENT_ROOT = Application::$document_root;
            $upload_path = 'upload/' . $table . '/' . $fieldName . '/';
            if (!file_exists($DOCUMENT_ROOT . 'upload/' . $table . '/')) {
                mkdir($DOCUMENT_ROOT . 'upload/' . $table . '/', 0777);
            }
            if (!is_writable($DOCUMENT_ROOT . 'upload/' . $table . '/')) {
                chmod($DOCUMENT_ROOT . 'upload/' . $table . '/', 0777);
            }
            if (!file_exists($DOCUMENT_ROOT . $upload_path)) {
                mkdir($DOCUMENT_ROOT . $upload_path, 0777);
            }
            if (!is_writable($DOCUMENT_ROOT . $upload_path)) {
                chmod($DOCUMENT_ROOT . $upload_path, 0777);
            }

            $count = 0;
            if ($model->id) {
                $count = $this->repository->fetchCountFiles($model->id);
            }

            foreach ($newFiles as $key => $newFile) {
                $file = $newFile['file'];
                $r_name = $newFile['src_name'];
                $array = explode('/', $file);
                $filename = @end($array);
                $name = '';
                $pos = $count + $key;
                if (file_exists($DOCUMENT_ROOT . self::UPLOAD_PATH_TEMP . $filename)) {
                    $new_filename = $upload_path . $filename;
                    if (@copy($DOCUMENT_ROOT . self::UPLOAD_PATH_TEMP . $filename, $DOCUMENT_ROOT . $new_filename)) {
                        //$data[$fieldName] = $new_filename;
                        @unlink($DOCUMENT_ROOT . self::UPLOAD_PATH_TEMP . $filename);
                        $modelFile = new MaintenanceFile();
                        $modelFile->file = $new_filename;
                        $modelFile->name = $name;
                        $modelFile->pos = $pos;
                        $modelFile->parent_id = $model->id;
                        $modelFile->r_name = $r_name;
                        $ok = $this->repository->saveFile($modelFile);
                        if (!$ok) {
                            $error .= $this->getError() . '<br>';
                        }
                    }
                }
            }
        }
        $this->error = $error;
    }

    public function deleteFile(int $fileId): bool
    {
        if ($fileId) {
            $file =  $this->repository->fetchOne($fileId);
            if ($file) {
                if (file_exists(Application::$document_root . $file->file)
                    && !is_dir(Application::$document_root . $file->file)) {
                    unlink(Application::$document_root . $file->file);
                }
                return $this->repository->delete($file);
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}
