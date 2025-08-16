<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class Flashsale extends CBitrixComponent
{

    public function executeComponent()
    {
        $nowDate = date("d.m.Y H:i:s");

        $dbProductDiscountBanner = \CCatalogDiscount::GetList(
            ['ID' => 'DESC'],
            [
                'ACTIVE' => 'Y',
                '+<ACTIVE_FROM' => $nowDate,
                '>ACTIVE_TO' => $nowDate,
                'XML_ID' => $this->arParams['XML_ID'] ?? 'BANNER',
            ],
            false,
            false,
            ['NAME', 'ACTIVE_TO', 'NOTES'],
        );

        if ($arProductDiscountBanner = $dbProductDiscountBanner->Fetch()) {
            $dateTimeZone = new \DateTimeZone('Europe/Moscow');
            $date = new \DateTime('', $dateTimeZone);
            $offset = $dateTimeZone->getOffset($date) / 60 / 60;
            $offset = ($offset < 0 ? '-' : '+') . str_pad(abs($offset), 2, '0', STR_PAD_LEFT);

            $flashsale_date = DateTime::createFromFormat(
                'd.m.Y H:i:s',
                $arProductDiscountBanner['ACTIVE_TO'],
                $dateTimeZone
            );
            $this->arResult['TITLE'] = $arProductDiscountBanner['NAME'];
            $this->arResult['FOOTER'] = nl2br($arProductDiscountBanner['NOTES']);
            $this->arResult['DT_END_STR'] = $flashsale_date->format('Y-m-d\TH:i:s') . ".000$offset:00";
            $flashsaleDiff = date_diff($flashsale_date, new DateTime);
            $daily = $flashsaleDiff->d > 0;
            if (!$daily) {
                $val1 = $flashsaleDiff->h + $flashsaleDiff->d * 24;
                $val2 = $flashsaleDiff->i;
                $val3 = $flashsaleDiff->s;
            } else {
                $val1 = $flashsaleDiff->d;
                $val2 = $flashsaleDiff->h;
                $val3 = $flashsaleDiff->i;
            }
            $this->arResult['VAL1'] = str_pad($val1, 2, '0', STR_PAD_LEFT);
            $this->arResult['VAL2'] = str_pad($val2, 2, '0', STR_PAD_LEFT);
            $this->arResult['VAL3'] = str_pad($val3, 2, '0', STR_PAD_LEFT);
            $this->arResult['DAILY'] = $daily ? 'Y' : 'N';
            $this->includeComponentTemplate();
        }
    }
}
