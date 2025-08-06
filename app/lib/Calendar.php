<?php

namespace app\lib;

class Calendar
{
    public static function daysOfMonth($month, $year): array
    {
        $dateNow = date('Y-m-d');
        $items = [];
        $days = ['Вск', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
        for ($d = 1; $d <= 31; $d ++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month) {
                $dateMySql = date('Y-m-d', $time);
                $dayNum = (int)date('w', $time);
                $items[] = [
                    'day' => $d,
                    'dayOfWeek' => $dayNum,
                    'dateFormat' => date('d.m.Y', $time),
                    'dateMySql' => $dateMySql,
                    'dayName' => $days[$dayNum],
                    'isWeekend' => $dayNum == 0 || $dayNum == 6,
                    'isToday' => $dateMySql == $dateNow,
                ];
            }
        }
        return $items;
    }
}