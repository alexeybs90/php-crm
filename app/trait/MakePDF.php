<?php

trait MakePDF
{
    public string $fileName = 'Отчет';

    public function landOrientation(): bool
    {
        return true;
    }

    public function doGet()
    {
        $params = $_GET;

        $mpdf = new \Mpdf\Mpdf(['margin_top' => 5, 'margin_bottom' => 13, 'margin_left' => 10,
            'margin_right' => 10, 'margin_header' => 10, 'margin_footer' => 10]);
        $mpdf->charset_in = 'utf-8';

//        $mpdf->autoPageBreak = false;
//        $mpdf->shrink_tables_to_fit = 1;
//        $mpdf->use_kwt = false;

//        $mpdf->SetHTMLHeader('11111');
        $mpdf->setFooter('Страница {PAGENO} из {nb}');
        if ($this->landOrientation()) $mpdf->AddPage('L');
//$print=iconv('cp1251','UTF-8//TRANSLIT',urldecode($print));
//        $stylesheet = file_get_contents($this->document_root . $this->dir . '/lib/mpdf/_bill.css');
        $stylesheet = '* {margin:0; padding:0;}'
            .'body {font: 12px Arial; color:#010103;}'
            .'table {margin: 5px 0 20px; border-top: 1px solid #333;border-left: 1px solid #333;border-collapse:collapse;}'
            . '.w100 {width: 100%;}'
            .'td, th {border-bottom: 1px solid #333;border-right: 1px solid #333;padding: 6px;font: 11px Arial;}'
            .'th {font:bold 12px Arial;}'
            .'.b {font-weight:bold;}'
            .'.td-bold {border: 3px solid #333;}'
            .'.left {text-align: left;}'
            .'.right {text-align: right;}'
            . '.material {font-weight:bold; color:#205fd6}'
            .'.f14, .f14 td, .f14 th {font-size: 14px}'
            .'.f16, .f16 td, .f16 th {font-size: 16px}'
            .'.f18, .f18 td, .f18 th {font-size: 18px}'
            .'.f20, .f20 td, .f20 th {font-size: 20px}'
            .'.ws-nw {white-space: nowrap;}';
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->list_indent_first_level = 0;
        $mpdf->WriteHTML($this->makeData($params), 2);
        $mpdf->Output($this->fileName . '.pdf', 'I');
    }

    /**
     * @param array $params
     * @return string
     */
    public function makeData($params)
    {
        return '' . print_r($params, true);
    }
}