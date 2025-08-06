<?php

namespace app\services;

class OrderMaterialPullService
{
    public array $materials_storage = [];
    public string $message = '';

    public static function create(): OrderMaterialPullService
    {
        return new self();
    }

    public function error(): string
    {
        return $this->message;
    }

    public function validate(\OrderMaterial $sklad): bool
    {
        $this->message = '';
        //обязательно проверяем, что ролик НЕ списан, чтоб избежать двойного списания
        if ($sklad->isPull == 1) {
//            $this->message .= "#".($sklad->pos+1) . "Этот ролик уже списан[os_id:{$sklad->id} ms_id:{$sklad->materialsSkladId} num:{$sklad->numRoll}]";
            return false;
        }

        $material_sklad = $this->fetchMaterialStorage($sklad->materialsSkladId);
        if (!$material_sklad) {
            $this->message .= "#".($sklad->pos+1)." Не найден ролик";
            return false;
        }

        $ok = true;
        if ((int)$sklad->used > (int)$material_sklad['length']) {
            $this->message .= "<div>#".($sklad->pos+1)." Списываемая длина не должна быть больше длины роля</div>";
            $ok = false;
        }

        if ((int)$sklad->used <= $sklad->minUsed()) {
            $this->message .= "<div>#".($sklad->pos+1)." Списываемая длина должна быть больше {$sklad->minUsed()}</div>";
            $ok = false;
        }

        if (!$sklad->printWorkerId) {
            $this->message .= "<div>#".($sklad->pos+1)." Не заполнен печатник</div>";
            $ok = false;
        }

        if ($sklad->maxWidth() > 0 && $material_sklad['width'] > $sklad->maxWidth()) {
            $this->message .= "<div>#".($sklad->pos+1)." Ширина роля больше {$sklad->maxWidth()}</div>";
            $ok = false;
        }
        return $ok;
    }

    public function materialPull(\OrderMaterial $sklad)
    {
        if (!$this->validate($sklad)) return false;

        $material_sklad = $this->fetchMaterialStorage($sklad->materialsSkladId);
        $newSkladLength = $material_sklad['length'] - (int)$sklad->used; //вычитаем списываемую длину со склада

        \DBPDO::query("UPDATE materials_sklad SET length=:length, m2=:m2 WHERE id=:id",
            [
                'length' => $newSkladLength,
                'm2' => ($newSkladLength * $material_sklad['width'] / 1000),
                "id" => $sklad->materialsSkladId,
            ]);
        $sklad->isPull = 1;
        $sklad->rollLengthSrc = $material_sklad['length'];
        $sklad->materialId = $material_sklad['material_id'];
        $sklad->rollWidth = $material_sklad['width'];
        $sklad->numRoll = $material_sklad['num_roll'];
        //теперь стоимость, валюту и курс берем из ролика в момент списывания
        $sklad->course = $material_sklad['course'];
        $sklad->materialPrice = $material_sklad['price'];
        $sklad->materialCur = $material_sklad['cur'];
        $sklad->save();

        $storage = $this->checkSkladPullItem($sklad);
        if (!$storage) $storage = $material_sklad['storage'];
        \MaterialStorage::logPull([
            'skladId' => $sklad->materialsSkladId, 'num_roll' => $sklad->numRoll, 'used' => $sklad->used,
            'order' => $sklad->order(), 'width' => $sklad->rollWidth, 'length' => $newSkladLength, 'storage' => $storage
        ]);

        return true;
    }

    public function fetchMaterialStorage($id)
    {
        //фетчим материал-склад
        if (!isset($this->materials_storage[$id])) {
            $sql = "SELECT ms.* FROM " . \MaterialStorage::table() . " ms WHERE ms.id=:id";
            $material_sklad = \DBPDO::getDataOne($sql, ['id' => $id]);
            $this->materials_storage[$id] = $material_sklad;
        } else {
            $material_sklad = $this->materials_storage[$id];
        }
        return $material_sklad;
    }

    public function checkSkladPullItem(\OrderMaterial $sklad)
    {
        $storage = $sklad->setStorageAfterPull();

        if ($sklad->withPullReserveNotification()) {
            //ищем если ролик забронирован где то
            $skladReserveF = \OrderFlexoSkladReserve::fetchListItems(['materialsSkladId' => $sklad->materialsSkladId]);
            $skladReserveHP = \OrderHPSkladReserve::fetchListItems(['materialsSkladId' => $sklad->materialsSkladId]);
            $skladReserveOS = \OrderOffsetSkladReserve::fetchListItems(['materialsSkladId' => $sklad->materialsSkladId]);
            if ($skladReserveF && $skladReserveF[0] || $skladReserveHP && $skladReserveHP[0] || $skladReserveOS && $skladReserveOS[0]) {
                if ($skladReserveF && $skladReserveF[0]) $orderReserve = \OrderFlexo::fetchItem($skladReserveF[0]->orderFlexoId);
                elseif ($skladReserveOS && $skladReserveOS[0]) $orderReserve = \OrderOffset::fetchItem($skladReserveOS[0]->orderFlexoId);
                else $orderReserve = \OrderHP::fetchItem($skladReserveHP[0]->orderHPId);
                $name = $orderReserve ?
                    '<a href="' . \Config::APP_URL . $orderReserve->url() . '">' . $orderReserve->getOrderName() . '</a>'
                    : 'удален';
                $msg = 'ролик ' . $sklad->numRoll . ' был забронирован под заказ ' . $name
                    . ', но был списан на заказ <a href="' . \Config::APP_URL . $sklad->order()->url() . '">'
                    . $sklad->order()->getOrderName() . '</a>';
                \TelegramBot::sendMessage(\TelegramBot::CHAT_IDS['FEDOTOV_ARTEM'], $msg, 1);
            }
        }
        return $storage;
    }

    public function materialCancel(\OrderMaterial $sklad)
    {
        //обязательно проверяем что ролик списан, чтоб избежать двойного списания
        if ($sklad->isPull != 1) {
            $this->message = "Этот ролик НЕ списан[os_id:{$sklad->id} ms_id:{$sklad->materialsSkladId} num:{$sklad->numRoll}]";
            return false;
        }

        $material_sklad = $this->fetchMaterialStorage($sklad->materialsSkladId);
        if (!$material_sklad) {
            $this->message .= "#" . ($sklad->pos + 1) . " Не найден ролик [ms_id:{$sklad->materialsSkladId} num:{$sklad->numRoll} ]";
            return false;
        }

        try {
            \DBPDO::beginTransaction();
            $length = $material_sklad['length'] + (int)$sklad->used;

            \DBPDO::query("UPDATE materials_sklad SET length=:length, m2=:m2 WHERE id=:id",
                [
                    'length' => $length,
                    'm2' => $length * $material_sklad['width'] / 1000,
                    'id' => $sklad->materialsSkladId,
                ]);
            $sklad->delete();

            $storage = $material_sklad['storage'];
            \MaterialStorage::logPull([
                'skladId' => $sklad->materialsSkladId, 'num_roll' => $sklad->numRoll, 'used' => $sklad->used,
                'order' => $sklad->order(), 'width' => $sklad->rollWidth, 'length' => $length, 'storage' => $storage,
                'cancelPull' => true,
            ]);
            \DBPDO::commit();
        } catch (\Exception $e) {
            \DBPDO::rollBack();
            $this->message .= "ошибка бд: " . $e->getMessage();
            return false;
        }

        return true;
    }
}