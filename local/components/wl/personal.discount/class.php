<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class PersonalDiscount extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        if($USER->isAuthorized()) {
            $this->arResult['USER_DISCOUNT'] = end(CCatalogDiscountSave::GetDiscount(["USER_ID" => $USER->GetId(), "USER_GROUPS" => $USER->GetUserGroupArray(), "SITE_ID" => "s1"]));

            $dbSaveDiscountRanges = CCatalogDiscountSave::GetRangeByDiscount([], ['DISCOUNT_ID' => $this->arResult['USER_DISCOUNT']['ID']]);
            if(empty($this->arResult['USER_DISCOUNT'])){
                $this->arResult['USER_DISCOUNT']['SUMM'] = 0;
                $this->arResult['USER_DISCOUNT']['VALUE'] = 0;
            }

            $this->arResult['RANGES'] = [];
            $this->arResult['sumToNextLevel'] = 0;
            while ($arRange = $dbSaveDiscountRanges->Fetch()) {
                $this->arResult['RANGES'][] = $arRange;
                if ($this->arResult['USER_DISCOUNT']['SUMM'] < $arRange['RANGE_FROM'] && $this->arResult['sumToNextLevel'] == 0) {
                    $this->arResult['sumToNextLevel'] = $arRange['RANGE_FROM'] - $this->arResult['USER_DISCOUNT']['SUMM'];
                }
            }

            $this->arResult['userHasMaxDiscount'] = false;
            if ($this->arResult['USER_DISCOUNT']['SUMM'] > $this->arResult['RANGES'][array_key_last($this->arResult['RANGES'])]['RANGE_FROM']) {
                $this->arResult['userHasMaxDiscount'] = true;
            }
            $this->includeComponentTemplate();
        }
    }

}