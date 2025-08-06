<?php

namespace app\lib;

class Helper
{
    public static function go($loc): void
    {
        header("Location: " . $loc);
    }

    public static function dateFormatPoint($date)
    {
        if($date) {
            if(strpos($date,' ')) {
                $val = explode(' ', $date);
                $date = $val[0];
            }
            if (strpos($date, '-')) {
                $sd = explode("-", $date);
                $year = $sd[0];
                $month = $sd[1];
                $day = $sd[2];
                $date = $day . '.' . $month . '.' . $year . (isset($val[1]) ? ' ' . $val[1] : '');
            }
        }
        return $date;
    }

    public static function dateFormatMySQL($date) {
        if($date) {
            if(strpos($date,' ')) {
                $val = explode(' ', $date);
                $date = $val[0];
            }
            if(strpos($date,'.')) {
                $sd = explode(".", $date);
                $year = $sd[2];
                $month = $sd[1];
                $day = $sd[0];
                $date = $year.'-'.$month.'-'.$day;
            }
        }
        return $date;
    }

    public static function nf($n, $decimals = 0, $space = ' '): string
    {
        return number_format((float)$n, $decimals, ',', $space);
    }

    public static function getExtByName($nameFile): string
    {
        $array = explode('.', $nameFile);
        return strtolower(@end($array));
    }

    public static function getNameFile($nameFile): bool|string
    {
        $array = explode('/', $nameFile);
        return @end($array);
    }

    public static function getMimeTypeByName($nameFile): string
    {
        $ext = self::getExtByName($nameFile);

        $type = 'application/octet-stream';

        switch($ext) {
            case 'pdf':
                $type = 'application/pdf';
                break;
            case 'cdr':
                $type = 'image/x-coreldraw';
                break;
            case 'psd':
                $type = 'image/x-photoshop';
                break;
            case 'ps':
                $type = 'application/postscript';
                break;
            case 'gif':
                $type = 'image/gif';
                break;
            case 'jpg':
            case 'jpeg':
                $type = 'image/jpeg';
                break;
            case 'png':
                $type = 'image/png';
                break;
            case 'xls':
                $type = 'application/vnd.ms-excel';
                break;
            case 'doc':
                $type = 'application/msword';
                break;
            case 'txt':
                $type = 'text/plain';
                break;
            case 'rar':
                $type = 'application/x-rar-compressed';
                break;
            case 'zip':
                $type = 'application/zip, application/octet-stream';
                break;
        }

        return $type;
    }
}