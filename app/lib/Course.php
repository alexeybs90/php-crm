<?php

namespace app\lib;

class Course {
    public static $courses = array();

    public static function getCourse($dat = 0)
    {
        $dat = substr(Helper::dateFormatMySQL($dat), 0, 10);
        if (isset(self::$courses[$dat])) {
            return self::$courses[$dat];
        }

        $course_arr["USD"] = 0;
        $course_arr["EUR"] = 0;
        $course_arr["GBP"] = 0;
        $course_arr["CNY"] = 0;
        $course_arr["KZT"] = 0;
        if ($dat == 0 || !$dat || $dat == '0000-00-00') {
            $dat = date('Y-m-d');
        }
        $query = "SELECT * FROM `course` WHERE dat=:dat";
        $data = DBPDO::getDataOne($query, ['dat' => $dat]);
        if ($data && (float)$data['usd'] != 0 && (float)$data['eur'] != 0 && (float)$data['cny'] != 0 && (float)$data['kzt'] != 0) {
            $course_arr["USD"] = $data['usd'];
            $course_arr["EUR"] = $data['eur'];
            $course_arr["GBP"] = $data['gbp'];
            $course_arr["CNY"] = $data['cny'];
            $course_arr["KZT"] = $data['kzt'];
        } else {
            $fplog = fopen(Application::$document_root . "upload/temp/logCourse.txt", "a");
            fwrite(
                $fplog,
                "\n" . date('Y-m-d H:i:s')
                . (Application::$module && Application::$module->user ? ' userId=' . Application::$module->user['id'] : '')
                . (isset($_POST['work']) ? ' post[work]=' . $_POST['work'] : '')
                . ' ' . $_SERVER['REQUEST_URI'] . ' ' . ($_SERVER['HTTP_REFERER'] ?? '') . "\n"
                . $query . "\n"
            );
            fclose($fplog);

            $URL = "https://www.cbr.ru/scripts/XML_daily.asp?date_req=" . date('d.m.Y', strtotime($dat));
            $allcurs = @simplexml_load_file($URL);
            //   $handle = fopen("http://www.cbr.ru/scripts/XML_daily.asp?date_req=".date('d.m.Y',strtotime($dat)), "r");
            //   $str=fread($handle,filesize("http://www.cbr.ru/scripts/XML_daily.asp?date_req=".date('d.m.Y',strtotime($dat))));
            if ($allcurs) {
                foreach ($allcurs as $key_c => $v_c) {
                    if ($v_c->CharCode == "USD") {
                        $course_arr["USD"] = (float)str_replace(',', '.', $v_c->Value[0]);
                    } elseif ($v_c->CharCode == "EUR") {
                        $course_arr["EUR"] = (float)str_replace(',', '.', $v_c->Value[0]);
                    } elseif ($v_c->CharCode == "GBP") {
                        $course_arr["GBP"] = (float)str_replace(',', '.', $v_c->Value[0]);
                    } elseif ($v_c->CharCode == "CNY") {
                        $nominal = (int)$v_c->Nominal;
                        if (!$nominal) $nominal = 1;
                        $course_arr["CNY"] = (float)str_replace(',', '.', $v_c->Value[0]) / $nominal;
                    } elseif ($v_c->CharCode == "KZT") {
                        $nominal = (int)$v_c->Nominal;
                        if (!$nominal) $nominal = 1;
                        $course_arr["KZT"] = (float)str_replace(',', '.', $v_c->Value[0]) / $nominal;
                    }
                }
                if (strtotime($dat) <= time() && $course_arr["USD"] != 0 && $course_arr["EUR"] != 0 && $course_arr["CNY"] != 0) {
                    $query_c = "INSERT INTO course(dat, usd, eur, gbp, cny, kzt, hand) VALUES (:dat, :usd, :eur, :gbp, :cny, :kzt, 0)";
                    $result_c = DBPDO::query($query_c, [
                        'dat' => $dat,
                        'usd' => $course_arr['USD'],
                        'eur' => $course_arr['EUR'],
                        'gbp' => $course_arr['GBP'],
                        'cny' => $course_arr['CNY'],
                        'kzt' => $course_arr['KZT'],
                    ]);
                }
            }
        }
        self::$courses[$dat] = $course_arr;
        return $course_arr;
    }
}