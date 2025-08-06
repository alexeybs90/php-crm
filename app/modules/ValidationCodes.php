<?php
namespace app\modules;

use app\lib\DBPDO;
use app\repositories\validationcode\ValidationCodeRepository;
use app\services\ValidationCodeService;

class ValidationCodes extends BasePage {
    public $service;

    public function init() {
        parent::init();

        $this->cssFiles['/css/style.css'] = false;
        $this->cssFiles['/css/opensans.css'] = false;

        $this->cssFiles['/css/ValidationCodes.css?v=1.0503'] = true;
        $this->jsFilesBottom['/js/ValidationCodes.js?v=1.0503'] = true;

        $repository = ValidationCodeRepository::create(DBPDO::$dbLink);
        $this->service = ValidationCodeService::create($repository);
    }

    //получаем номер месяца и год, выводим дни и соответствующие числа из базы по фамилиям
    public function ajaxList($params = [])
    {
        $page = (int)($params['page'] ?? 1);
        $month = (int)date('m') + $page - 1;
        $year = (int)date('Y');
        $y = floor($month / 12);
        if ($month % 12 == 0) {
            $month = 12;
            $y --;
        } else {
            $month = $month % 12;
        }
        $year += $y;

        $forms = DBPDO::getData('select id,last_name,first_name from workers WHERE type_worker=:id'
            . ' ORDER BY last_name, first_name', ['id' => User::ID_TECHNOLOGIST]);
        $workerItems = [];
        $workerIdOptions = [['value' => 0, 'text' => '']];
        foreach ($forms as $fdata) {
            $workerIdOptions[] = ['value' => (int)$fdata['id'], 'text' => $fdata['last_name'].' '.$fdata['first_name']];
            $workerItems[(int)$fdata['id']] = $fdata['last_name'].' '.$fdata['first_name'];
        }
        $forms = DBPDO::getData('select id,last_name,first_name from workers ORDER BY last_name, first_name');
        $workerIdFullOptions = [['value' => 0, 'text' => '']];
        foreach ($forms as $fdata) {
            $workerIdFullOptions[] = ['value' => (int)$fdata['id'], 'text' => $fdata['last_name'].' '.$fdata['first_name']];
        }

        $datesDB = $this->service->fetchListByParams();
        $datesData = [];
        if ($datesDB) {
            foreach ($datesDB as $dateDB) {
                $datesData[$dateDB->dateWork] = $dateDB;
            }
        }

        $items = array_map(function ($item) use ($datesData) {
            $trClass = [];
            if ($item['isWeekend']) $trClass []= 'tr-weekend';
            if ($item['isToday']) {
                $trClass [] = 'tr-today';
            }
            $values = [];
            $dateMySql = $item['dateMySql'];
            if (isset($datesData[$dateMySql])) {
                $values[] = $datesData[$dateMySql]->value;
            }
            return [
                'dateWork' => $item['dateFormat'],
                'dayWork' => $item['dayName'],
                'values' => $values,
                'trClass' => $trClass,
            ];
        }, \app\lib\Calendar::daysOfMonth($month, $year));

        print json_encode([
            'items' => $items,
            'workerIdOptions' => $workerIdOptions,
            'workerIdFullOptions' => $workerIdFullOptions,
            'monthNow' => $month,
            'workerItems' => $workerItems,
        ]);
    }

    public function showBody() {
        ?>
        <div id="app">
            <schedule></schedule>
        </div>
        <?php
    }
}