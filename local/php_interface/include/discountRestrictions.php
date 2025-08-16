<?php

use Bitrix\Main\Loader;
Loader::includeModule('catalog');

AddEventHandler("sale", "OnCondSaleControlBuildList", Array("CatalogCondCtrlOrderSum", "GetControlDescr"));
AddEventHandler("catalog", "OnCondCatControlBuildList", Array("CatalogCondCtrlOrderSum", "GetControlDescr"));

class CatalogCondCtrlOrderSum extends CGlobalCondCtrlComplex
{
    public static function GetClassName()
    {
        return __CLASS__;
    }


    public static function GetControlID()
    {
        return array('CondOrderSum');
    }

    public static function GetControlShow($arParams)
    {
        $arControls = static::GetControls();
        $arResult = array(
            'controlgroup' => true,
            'group' =>  false,
            'label' => 'Условие',
            'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
            'children' => array()
        );
        foreach ($arControls as &$arOneControl)
        {
            $arResult['children'][] = array(
                'controlId' => $arOneControl['ID'],
                'group' => false,
                'label' => $arOneControl['LABEL'],
                'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
                'control' => array(
                    array(
                        'id' => 'prefix',
                        'type' => 'prefix',
                        'text' => $arOneControl['PREFIX']
                    ),
                    static::GetLogicAtom($arOneControl['LOGIC']),
                    static::GetValueAtom($arOneControl['JS_VALUE'])
                )
            );
        }
        if (isset($arOneControl))
            unset($arOneControl);

        return $arResult;
    }

    public static function GetControls($strControlID = false)
    {
		$arPreviousOrderSum['123123'] = "= 0";
		
        $arControlList = array(
			'CondOrderSum' => array(
				'ID' => 'CondOrderSum',
				'FIELD' => 'TIME_ID',
				'FIELD_TYPE' => 'int',
				'LABEL' => 'Сумма оплаченных заказов',
				'PREFIX' => 'Сумма оплаченных заказов',
				'LOGIC' => static::GetLogic(array(BT_COND_LOGIC_EQ)),
				'JS_VALUE' => array(
					'type' => 'input',
				),
				'PHP_VALUE' => ''
			),
        );

        foreach ($arControlList as &$control)
        {
            if (!isset($control['PARENT']))
                $control['PARENT'] = true;

            $control['MULTIPLE'] = 'N';
        }
        unset($control);

        if ($strControlID === false)
        {
            return $arControlList;
        }
        elseif (isset($arControlList[$strControlID]))
        {
            return $arControlList[$strControlID];
        }
        else
        {
            return false;
        }
    }
    
    public static function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        $strResult = '';
		
		if($arOneCondition['logic']=='Equal')
		{
			$logic='true';
		}
		else
		{
			$logic='false';
		}
		
		$strResult  = '(CatalogCondCtrlOrderSum::checkOrderSum($arProduct, '.$arOneCondition["value"].'))=='.$logic;

        return  $strResult;		
		
    }	

	public static function checkOrderSum($arProduct, $arCondition=array())
	{
        global $USER;

        $userId = $USER->getId();
        $orderSumByUser = 0;

        if(intval($userId) > 0) {
            $filter = [
                'USER_ID' => $userId,
            ];
    
            $rsBuyerStatistic = \Bitrix\Sale\Internals\BuyerStatisticTable::getList([
                'select' => ['USER_ID', 'LAST_ORDER_DATE', 'SUM_PAID'],
                'count_total' => false,
                'filter' => $filter,
                'limit' => "1",
            ]);
            $arBuyerStatistic = $rsBuyerStatistic->fetchAll();
            $orderSumByUser = $arBuyerStatistic[0]['SUM_PAID'];
        } else {
            $orderSumByUser = 0;
        }

        if($orderSumByUser <= $arCondition) {
            return true;
        } else {
            return false;
        }
	}
}



AddEventHandler("sale", "OnCondSaleControlBuildList", Array("CatalogCondCtrlTimeRange", "GetControlDescr"));//корзина
AddEventHandler("catalog", "OnCondCatControlBuildList", Array("CatalogCondCtrlTimeRange", "GetControlDescr"));//каталог

class CatalogCondCtrlTimeRange extends CGlobalCondCtrlComplex
{
    public static function GetClassName()
    {
        return __CLASS__;
    }
    /**
     * @return string|array
     */
    public static function GetControlID()
    {
        return array('CondTime');
    }

    public static function GetControlShow($arParams)
    {
        $arControls = static::GetControls();
        $arResult = array(
            'controlgroup' => true,
            'group' =>  false,
            'label' => 'Диапазоны',
            'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
            'children' => array()
        );
        foreach ($arControls as &$arOneControl)
        {
            $arResult['children'][] = array(
                'controlId' => $arOneControl['ID'],
                'group' => false,
                'label' => $arOneControl['LABEL'],
                'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
                'control' => array(
                    array(
                        'id' => 'prefix',
                        'type' => 'prefix',
                        'text' => $arOneControl['PREFIX']
                    ),
                    static::GetLogicAtom($arOneControl['LOGIC']),
                    static::GetValueAtom($arOneControl['JS_VALUE'])
                )
            );
        }
        if (isset($arOneControl))
            unset($arOneControl);

        return $arResult;
    }

    /**
     * @param bool|string $strControlID
     * @return bool|array
     */
    public static function GetControls($strControlID = false)
    {
		$arTimeRanges['1419'] = "10:00 - 11:00";
		
        $arControlList = array(
			'CondTime' => array(
				'ID' => 'CondTime',
				'FIELD' => 'TIME_ID',
				'FIELD_TYPE' => 'int',
				'LABEL' => 'Диапазон времени активности',
				'PREFIX' => 'Диапазон времени активности',
				'LOGIC' => static::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ)),
				'JS_VALUE' => array(
					'type' => 'select',
					'multiple' => 'N',
					'values' => $arTimeRanges,
					'show_value' => 'Y'
				),
				'PHP_VALUE' => array(
					'VALIDATE' => 'list'
				)
			),
        );

        foreach ($arControlList as &$control)
        {
            if (!isset($control['PARENT']))
                $control['PARENT'] = true;

            $control['MULTIPLE'] = 'N';
        }
        unset($control);

        if ($strControlID === false)
        {
            return $arControlList;
        }
        elseif (isset($arControlList[$strControlID]))
        {
            return $arControlList[$strControlID];
        }
        else
        {
            return false;
        }
    }
    
    public static function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        $strResult = '';
        $resultValues = array();
        $arValues = false;
		
		if($arOneCondition['logic']=='Equal')
		{
			$logic='true';
		}
		else
		{
			$logic='false';
		}
		
		$timeArrStr = 'Array(';
		foreach($arOneCondition["value"] as $k => $v){
			$timeArrStr .= "$k=>$v,";
		}
		$timeArrStr .= ')';
		
		$strResult  = '(CatalogCondCtrlTimeRange::checkTime($arProduct,'.$timeArrStr.'))=='.$logic;

        return  $strResult;		
		
    }	
	/**
	* @param array|array
	* @return bool
	*/
	public static function checkTime($arProduct,$arrTime=array())
	{
        if(strtotime(date('H:i:00')) > strtotime("10:00:00") && strtotime(date('H:i:00')) < strtotime("11:00:00")) {
            return true;
        }
        return false;
	}
}