<?php
namespace app\modules;

use app\lib\DBPDO;
use app\lib\Helper;
use MakePDF;

class TopClientsPDF extends BasePage {
    use MakePDF;

    public function landOrientation(): bool
    {
        return false;
    }

    public function makeData($params)
    {
        set_time_limit(300);
        $this->fileName = 'Топ_30_клиентов_' . date('d.m.Y');
        $dateBegin = $params['dateBegin'] ?? '';
        $dateEnd = $params['dateEnd'] ?? '';
        $type = (int)($params['type'] ?? 0);
        $withOrderHP = (int)($params['withOrderHP'] ?? 0);
        $withOrderFlexo = (int)($params['withOrderFlexo'] ?? 0);
        $withOrderOffset = (int)($params['withOrderOffset'] ?? 0);
        $field = $type == 1 ? 'priceFactAll' : 'shipmentFO';

        $data = $values = [];
        if ($withOrderHP) {
            $data[] = " SELECT id, clientId, isCompleteShipment, completeShipmentDate, IFNULL(priceFactAll, 0) AS priceFactAll, IFNULL(shipmentFO, 0) AS shipmentFO FROM order_hp"
                . " WHERE isCompleteShipment=1 AND completeShipmentDate>=:dateBegin AND completeShipmentDate<=:dateEnd";
            $values['dateBegin'] = Helper::dateFormatMySQL($dateBegin);
            $values['dateEnd'] = Helper::dateFormatMySQL($dateEnd);
        }
        if ($withOrderFlexo) {
            $data[] = " SELECT id, clientId, isCompleteShipment, completeShipmentDate, IFNULL(priceFactAll, 0) AS priceFactAll, IFNULL(shipmentFO, 0) AS shipmentFO FROM order_flexo"
                . " WHERE isCompleteShipment=1 AND completeShipmentDate>=:dateBegin1 AND completeShipmentDate<=:dateEnd1";
            $values['dateBegin1'] = Helper::dateFormatMySQL($dateBegin);
            $values['dateEnd1'] = Helper::dateFormatMySQL($dateEnd);
        }
        if ($withOrderOffset) {
            $data[] = " SELECT id, clientId, isCompleteShipment, completeShipmentDate, IFNULL(priceFactAll, 0) AS priceFactAll, IFNULL(shipmentFO, 0) AS shipmentFO FROM order_offset"
                . " WHERE isCompleteShipment=1 AND completeShipmentDate>=:dateBegin2 AND completeShipmentDate<=:dateEnd2";
            $values['dateBegin2'] = Helper::dateFormatMySQL($dateBegin);
            $values['dateEnd2'] = Helper::dateFormatMySQL($dateEnd);
        }

        $sql = "SELECT u.companyShort, u.company, SUM(z.shipmentFO) AS shipmentFO, SUM(z.priceFactAll) AS priceFactAll, COUNT(z.id) AS amount
            FROM users u
                
            LEFT JOIN ("
            . implode(" UNION ALL ", $data)
            .") z ON z.clientId=u.id

            GROUP BY u.id ORDER BY {$field} DESC LIMIT 30";
//        print $sql;die();
        $items = DBPDO::getData($sql, $values);

        $print = '<div style="text-align: center; font-weight: bold; font-size: 16px; position: relative;">Топ 30 клиентов '
            . '<br>с ' . Helper::dateFormatPoint($dateBegin) . ' по ' . Helper::dateFormatPoint($dateEnd)
            . '<br>' . ($type == 1 ? 'по стоимости работ  ФО' : 'по отгрузке ФО')
            . '</div>';
        $print .= '<div style="text-align: right">' . date('d.m.Y H:i') . '</div>';

        if ($items) {
            $print .= '<table class="w100">';
            $print .= $this->htmlTableHead($type);
            $amount = $shipmentFO = $priceFactAll = 0;

            foreach ($items as $k => $item) {
                $print .= '<tr>'
                    . '<td class="right">' . ($k + 1) . '</td>'
                    . '<td class="ws-nw">' . $item['companyShort'] . '</td>'
                    . '<td class="right' . ($type == 0 ? ' td-bold' : '') . '">' . nf($item['shipmentFO']) . '</td>'
                    . '<td class="right' . ($type == 1 ? ' td-bold' : '') . '">' . nf($item['priceFactAll']) . '</td>'
                    . '<td class="right">' . Helper::nf($item['amount']) . '</td>'
                    . '</tr>';
                $shipmentFO += $item['shipmentFO'];
                $priceFactAll += $item['priceFactAll'];
                $amount += $item['amount'];
            }
            $print .= '<tr>';
            $print .= '<td></td>';
            $print .= '<td></td>';
            $print .= '<td class="right b ws-nw' . ($type == 0 ? ' td-bold' : '') . '">' . nf($shipmentFO) . '</td>';
            $print .= '<td class="right b ws-nw' . ($type == 1 ? ' td-bold' : '') . '">' . nf($priceFactAll) . '</td>';
            $print .= '<td class="right b ws-nw">' . Helper::nf($amount) . '</td>';
            $print .= '</tr>';
            $print .= '</table>';
        }
//        print $print; die();

//	print $print . '<link href="/manager/lib/mpdf/_bill.css" rel="stylesheet">';

        return $print;
    }

    public function htmlTableHead($type = 0)
    {
        return '<tr>'
            . '<th>#</th>'
            . '<th>Клиент</th>'
            . '<th class="' . ($type == 0 ? 'td-bold' : '') . '">Отгрузка ФО, руб</th>'
            . '<th class="' . ($type == 1 ? 'td-bold' : '') . '">Стоимость работ ФО, руб</th>'
            . '<th>Кол-во заказов</th>'
            . '</tr>';
    }
}