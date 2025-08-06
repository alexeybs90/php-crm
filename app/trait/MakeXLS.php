<?php

trait MakeXLS
{
    public string $fileName = 'Отчет';

    public function doGet()
    {
        $params = $_GET;
        // Создаем объект класса PHPExcel
        $xls = new PHPExcel();
        // Устанавливаем индекс активного листа
        $xls->setActiveSheetIndex(0);
        // Получаем активный лист
        $sheet = $xls->getActiveSheet();

        $this->makeData($params, $sheet, $xls);

        ob_end_clean();
        header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
        header ( "Last-Modified: " . gmdate("D,d M YH:i:s")." GMT" );
        header ( "Cache-Control: no-cache, must-revalidate" );
        header ( "Pragma: no-cache" );
        header ( "Content-type: application/vnd.ms-excel" );
        header ( 'Content-Disposition: attachment; filename=' . $this->fileName . '.xls');

        // Выводим содержимое файла
        $objWriter = new PHPExcel_Writer_Excel5($xls);
        $objWriter->save('php://output');
    }

    /**
     * @param $params
     * @param $sheet PHPExcel_Worksheet
     * @param $xls PHPExcel
     */
    public function makeData($params, PHPExcel_Worksheet $sheet, PHPExcel $xls)
    {
        $sheet->setTitle('Лист 1');
        $xls->createSheet(1);
        $xls->setActiveSheetIndex(1);
        $sheet->setCellValueByColumnAndRow(0, 1, 'test');
    }
}