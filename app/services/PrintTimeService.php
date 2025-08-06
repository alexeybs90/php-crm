<?php

namespace app\services;

class PrintTimeService
{
    const RAPPORT = [500, 600, 700, 800, 900, 990];
    const COLORS = 9;
    const MATERIALS = ['simple', 'metal'];

    public function keys(): array
    {
        $keys = [];
        foreach (self::MATERIALS as $material) {
            foreach (self::RAPPORT as $rapport) {
                for ($i = 1; $i <= self::COLORS; $i ++) {
                    $keys [] = $material . '_' . $rapport . '_' . $i;
                }
            }
        }
        return $keys; //[ simple_500_1, simple_600_1, metal_500_1, simple_990_7 etc. ]
    }

    public static function create(): PrintTimeService
    {
        return new self();
    }

    public function getPrintTimeParams(): array
    {
        $data = [];
        foreach ($this->keys() as $key) {
            $data[$key] = \Param::fetchOrCreate($key)->value ?: '';
        }
        return $data;
    }

    public function savePrintTimeParams($params = []): bool
    {
        foreach ($this->keys() as $key) {
            $item = \Param::fetchOrCreate($key);
            $item->value = $params[$key] ?? 0;
            if (!$item->saveData()) {
                return false;
            }
        }
        return true;
    }

    public function calcOrderPrintTime(\OrderBase $order): int
    {
        $time = 0;
        $rapport = (int)$order->rapportPrint();
        if (!in_array($rapport, self::RAPPORT)) {
            $newRapport = 0;
            foreach (self::RAPPORT as $num) {
                if ($rapport < $num) break;
                $newRapport = $num;
            }
            $rapport = $newRapport;
        }
        $order->setData('typeMaterialName', '');
        $t = $order->typeMaterialName();
        $material = 'simple';
        if (preg_match('/металл/ui', $t)) {
            $material = 'metal';
        }
        $colors = $order->makets() ? strlen($order->makets()[0]->colorsAmount) : 0;
        $vKey = $material . '_' . $rapport . '_' . $colors;
        $v = \Param::getParam($vKey);
        if ($v) $time = ceil($order->calcConsumptionMP() / $v);
        return $time;
    }
}