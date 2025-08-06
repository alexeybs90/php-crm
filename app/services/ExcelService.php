<?php

namespace app\services;

use app\lib\Helper;

class ExcelService
{
    public function readFile(string $filename): array
    {
        $ext = Helper::getExtByName($filename);
        if ($ext == 'csv') {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
        } else if ($ext == 'xls') {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
        } else {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        }
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        return $sheet->toArray();
    }
}