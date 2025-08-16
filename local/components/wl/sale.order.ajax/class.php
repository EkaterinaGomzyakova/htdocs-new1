<?php

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\Order;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Shipment;
use Bitrix\Sale\Result;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

/**
 * @var $APPLICATION CMain
 * @var $USER CUser
 */

Loc::loadMessages(__FILE__);

if (!Loader::includeModule("sale")) {
    ShowError(Loc::getMessage("SOA_MODULE_NOT_INSTALL"));

    return;
}
CBitrixComponent::includeComponentClass("bitrix:sale.order.ajax");

class WLSaleOrderAjax extends \SaleOrderAjax
{
    protected function getInnerPaySystemInfo(Order $order, $recalculate = false)
    {
        $arResult =& $this->arResult;
        $sumToSpend = 0;
        $arPaySystemServices = [];

        if ($this->arParams['PAY_FROM_ACCOUNT'] === 'Y' && $order->isAllowPay()) {
            $innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();
            $innerPayment = $order->getPaymentCollection()->getInnerPayment();

            if (!$innerPayment) {
                $innerPayment = $order->getPaymentCollection()->createInnerPayment();
            }

            if (!$innerPayment) {
                return [0, $arPaySystemServices];
            }

            $this->loadUserAccount($order);
            $userBudget = (float)$arResult['USER_ACCOUNT']['CURRENT_BUDGET'];

            // finding correct inner pay system price ranges to setField()
            $sumRange = Sale\Services\PaySystem\Restrictions\Manager::getPriceRange($innerPayment, $innerPaySystemId);
            if (!empty($sumRange)) {
                if (
                    (empty($sumRange['MIN']) || $sumRange['MIN'] <= $userBudget)
                    && (empty($sumRange['MAX']) || $sumRange['MAX'] >= $userBudget)
                ) {
                    $sumToSpend = $userBudget;
                }

                if (!empty($sumRange['MAX']) && $sumRange['MAX'] <= $userBudget) {
                    $sumToSpend = $sumRange['MAX'];
                }
            } else {
                $sumToSpend = $userBudget;
            }

            $sumToSpend = $sumToSpend >= $order->getPrice() ? $order->getPrice() : $sumToSpend;

            if ($this->arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] === 'Y' && $sumToSpend < $order->getPrice()) {
                $sumToSpend = 0;
            }

            if (!empty($arResult['USER_ACCOUNT']) && $sumToSpend > 0) {
                // setting inner payment price
                $innerPayment->setField('SUM', $sumToSpend);
                // getting allowed pay systems by restrictions
                $arPaySystemServices = PaySystem\Manager::getListWithRestrictions($innerPayment);
                // delete inner pay system if restrictions has not passed
                if (!isset($arPaySystemServices[$innerPaySystemId])) {
                    $innerPayment->delete();
                    $sumToSpend = 0;
                }
            } else {
                $innerPayment->delete();
            }
        }

        if ($sumToSpend > 0) {
            $request = Main\Application::getInstance()->getContext()->getRequest();
            $basket = $order->getBasket();
            $price = $basket->getPrice();
            $maxPayPoints = round($price / 100 * MAX_PERCENT_POINTS_PAY);
            if($sumToSpend < $maxPayPoints){
                $maxPayPoints = $sumToSpend;
            }

            if($_REQUEST['order']['PAY_FROM_ACCOUNT_POINTS'] > 0){
                $sumToSpend = $_REQUEST['order']['PAY_FROM_ACCOUNT_POINTS'];
            }

            if($_REQUEST['PAY_FROM_ACCOUNT_POINTS'] > 0){
                $sumToSpend = $_REQUEST['PAY_FROM_ACCOUNT_POINTS'];
            }

            if ($sumToSpend > $maxPayPoints) {
                $sumToSpend = $maxPayPoints;
            }

            $arResult['JS_DATA']['CURRENT_PAY_POINTS'] = $sumToSpend;
            $arResult['JS_DATA']['MAX_PAY_POINTS'] = $maxPayPoints;
            $arResult['JS_DATA']['MAX_PERCENT_POINTS_PAY'] = MAX_PERCENT_POINTS_PAY;
            $arResult['PAY_FROM_ACCOUNT'] = 'Y';
            $arResult['CURRENT_BUDGET_FORMATED'] = SaleFormatCurrency($arResult['USER_ACCOUNT']['CURRENT_BUDGET'], $order->getCurrency());
        } else {
            $arResult['PAY_FROM_ACCOUNT'] = 'N';
            unset($arResult['CURRENT_BUDGET_FORMATED']);
        }
        return [$sumToSpend, $arPaySystemServices];
    }

    protected function initDelivery(Shipment $shipment)
	{
		$deliveryId = intval($this->arUserResult['DELIVERY_ID']);
		$this->initDeliveryServices($shipment);
		/** @var Sale\ShipmentCollection $shipmentCollection */
		$shipmentCollection = $shipment->getCollection();
		$order = $shipmentCollection->getOrder();

		if (!empty($this->arDeliveryServiceAll))
		{
			if (isset($this->arDeliveryServiceAll[$deliveryId]))
			{
				$deliveryObj = $this->arDeliveryServiceAll[$deliveryId];
			}
			else
			{
				$deliveryObj = reset($this->arDeliveryServiceAll);
				$deliveryId = $deliveryObj->getId();
			}

			if ($deliveryObj->isProfile())
			{
				$name = $deliveryObj->getNameWithParent();
			}
			else
			{
				$name = $deliveryObj->getName();
			}

			$order->isStartField();

			$shipment->setFields([
				'DELIVERY_ID' => $deliveryId,
				'DELIVERY_NAME' => $name,
				'CURRENCY' => $order->getCurrency(),
			]);
			$this->arUserResult['DELIVERY_ID'] = $deliveryId;

			$deliveryStoreList = Delivery\ExtraServices\Manager::getStoresList($deliveryId);
			if (!empty($deliveryStoreList))
			{
				if ($this->arUserResult['BUYER_STORE'] <= 0 || !in_array($this->arUserResult['BUYER_STORE'], $deliveryStoreList))
				{
					$this->arUserResult['BUYER_STORE'] = current($deliveryStoreList);
				}

				$shipment->setStoreId($this->arUserResult['BUYER_STORE']);
			}

			$deliveryExtraServices = $this->arUserResult['DELIVERY_EXTRA_SERVICES'] ?? null;
			if (!empty($deliveryExtraServices[$deliveryId]) && is_array($deliveryExtraServices))
			{
				$shipment->setExtraServices($deliveryExtraServices[$deliveryId]);
				$deliveryObj->getExtraServices()->setValues($deliveryExtraServices[$deliveryId]);
			}

			$shipmentCollection->calculateDelivery();

			$order->doFinalAction(true);
		}
		else
		{
			$service = Delivery\Services\Manager::getById(
				Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId()
			);
			$shipment->setFields([
				'DELIVERY_ID' => $service['ID'],
				'DELIVERY_NAME' => $service['NAME'],
				'CURRENCY' => $order->getCurrency(),
			]);
		}
	}

    protected function obtainTotal() {
        parent::obtainTotal();
        $this->arResult['JS_DATA']['COUNT_BONUS_POINTS'] = ceil($this->arResult['ORDER_TOTAL_PRICE'] / 100  * COUNT_BONUS_POINTS);
    }

    protected function obtainPaySystem() {
        parent::obtainPaySystem();
        
        foreach($this->arResult['PAY_SYSTEM'] as $key => $paysystem) {
            if(in_array($paysystem['CODE'], ["DOLYAMI", "CHASTYAMI"])) {
                $this->arResult['PAY_SYSTEM'][$key]['CAN_PAY_BONUS'] = "N";
            } else {
                $this->arResult['PAY_SYSTEM'][$key]['CAN_PAY_BONUS'] = "Y";
            }
        }
    }

    /**
	 * Initialization of inner/external payment objects with first/selected pay system services.
	 *
	 * @param Order $order
	 * @throws Main\ObjectNotFoundException
	 */
	protected function initPayment(Order $order)
	{
		[$sumToSpend, $innerPaySystemList] = $this->getInnerPaySystemInfo($order);

		if ($sumToSpend > 0)
		{
			$innerPayment = $this->getInnerPayment($order);
			if (!empty($innerPayment))
			{
				if ($this->arUserResult['PAY_CURRENT_ACCOUNT'] === 'Y')
				{
					$innerPayment->setField('SUM', $sumToSpend);
				}
				else
				{
					$innerPayment->delete();
					$innerPayment = null;
				}

				$this->arPaySystemServiceAll = $this->arActivePaySystems = $innerPaySystemList;
			}
		}

		$innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();
		$extPaySystemId = (int)$this->arUserResult['PAY_SYSTEM_ID'];

		$paymentCollection = $order->getPaymentCollection();
		$remainingSum = $order->getPrice() - $paymentCollection->getSum();
		if ($remainingSum > 0 || $order->getPrice() == 0)
		{
			$extPayment = $paymentCollection->createItem();
			$extPayment->setField('SUM', $remainingSum);

			$extPaySystemList = PaySystem\Manager::getListWithRestrictions($extPayment);

			// we already checked restrictions for inner pay system (could be different by price restrictions)
			if (empty($innerPaySystemList[$innerPaySystemId]))
			{
				unset($extPaySystemList[$innerPaySystemId]);
			}
			elseif (empty($extPaySystemList[$innerPaySystemId]))
			{
				$extPaySystemList[$innerPaySystemId] = $innerPaySystemList[$innerPaySystemId];
			}

			$this->arPaySystemServiceAll = $this->arActivePaySystems = $extPaySystemList;

			if ($extPaySystemId !== 0 && array_key_exists($extPaySystemId, $this->arPaySystemServiceAll))
			{
				$selectedPaySystem = $this->arPaySystemServiceAll[$extPaySystemId];
			}
			else
			{
				reset($this->arPaySystemServiceAll);

				if (key($this->arPaySystemServiceAll) == $innerPaySystemId)
				{
					if (count($this->arPaySystemServiceAll) > 1)
					{
						next($this->arPaySystemServiceAll);
					}
					elseif ($sumToSpend > 0)
					{
						$extPayment->delete();
						$extPayment = null;

						/** @var Payment $innerPayment */
						$innerPayment = $this->getInnerPayment($order);
						if (empty($innerPayment))
						{
							$innerPayment = $paymentCollection->getInnerPayment();
							if (!$innerPayment)
							{
								$innerPayment = $paymentCollection->createInnerPayment();
							}
						}

						$sumToPay = $remainingSum > $sumToSpend ? $sumToSpend : $remainingSum;
						$innerPayment->setField('SUM', $sumToPay);
					}
					else
					{
						unset($this->arActivePaySystems[$innerPaySystemId]);
						unset($this->arPaySystemServiceAll[$innerPaySystemId]);
					}
				}

				$selectedPaySystem = current($this->arPaySystemServiceAll);
			}

			if (!empty($selectedPaySystem))
			{
				if ($selectedPaySystem['ID'] != $innerPaySystemId)
				{
					$extPayment->setFields([
						'PAY_SYSTEM_ID' => $selectedPaySystem['ID'],
						'PAY_SYSTEM_NAME' => $selectedPaySystem['NAME'],
					]);

					$this->arUserResult['PAY_SYSTEM_ID'] = $selectedPaySystem['ID'];
				}
			}
			elseif (!empty($extPayment))
			{
				$extPayment->delete();
				$extPayment = null;
			}
		}

		if (empty($this->arPaySystemServiceAll))
		{
			$this->addError(Loc::getMessage('SOA_ERROR_PAY_SYSTEM'), self::PAY_SYSTEM_BLOCK);
		}

		if (!empty($this->arUserResult['PREPAYMENT_MODE']))
		{
			$this->showOnlyPrepaymentPs($this->arUserResult['PAY_SYSTEM_ID']);
		}
	}

	/**
	 * Recalculates payment prices which could change due to shipment/discounts.
	 *
	 * @param Order $order
	 * @throws Main\ObjectNotFoundException
	 */
	protected function recalculatePayment(Order $order)
	{
		$res = $order->getShipmentCollection()->calculateDelivery();

		if (!$res->isSuccess())
		{
			$shipment = $this->getCurrentShipment($order);

			if (!empty($shipment))
			{
				$errMessages = '';
				$errors = $res->getErrorMessages();

				if (!empty($errors))
				{
					foreach ($errors as $message)
					{
						$errMessages .= $message.'<br />';
					}
				}
				else
				{
					$errMessages = Loc::getMessage('SOA_DELIVERY_CALCULATE_ERROR');
				}

				$r = new Result();
				$r->addError(new Sale\ResultWarning(
					$errMessages,
					'SALE_DELIVERY_CALCULATE_ERROR'
				));

				Sale\EntityMarker::addMarker($order, $shipment, $r);
				$shipment->setField('MARKED', 'Y');
			}
		}

		[$sumToSpend, $innerPaySystemList] = $this->getInnerPaySystemInfo($order, true);

		$innerPayment = $this->getInnerPayment($order);
		if (!empty($innerPayment))
		{
			if ($this->arUserResult['PAY_CURRENT_ACCOUNT'] === 'Y' && $sumToSpend > 0)
			{
				$innerPayment->setField('SUM', $sumToSpend);
			}
			else
			{
				$innerPayment->delete();
				$innerPayment = null;
			}

			if ($sumToSpend > 0)
			{
				$this->arPaySystemServiceAll = $innerPaySystemList;
				$this->arActivePaySystems += $innerPaySystemList;
			}
		}

		/** @var Payment $innerPayment */
		$innerPayment = $this->getInnerPayment($order);
		/** @var Payment $extPayment */
		$extPayment = $this->getExternalPayment($order);

		$remainingSum = empty($innerPayment) ? $order->getPrice() : $order->getPrice() - $innerPayment->getSum();
		if ($remainingSum > 0 || $order->getPrice() == 0)
		{
			$paymentCollection = $order->getPaymentCollection();
			$innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();
			$extPaySystemId = (int)$this->arUserResult['PAY_SYSTEM_ID'];

			if (empty($extPayment))
			{
				$extPayment = $paymentCollection->createItem();
			}

			$extPayment->setField('SUM', $remainingSum);

			$extPaySystemList = PaySystem\Manager::getListWithRestrictions($extPayment);
			// we already checked restrictions for inner pay system (could be different by price restrictions)
			if (empty($innerPaySystemList[$innerPaySystemId]))
			{
				unset($extPaySystemList[$innerPaySystemId]);
			}
			elseif (empty($extPaySystemList[$innerPaySystemId]))
			{
				$extPaySystemList[$innerPaySystemId] = $innerPaySystemList[$innerPaySystemId];
			}

			$this->arPaySystemServiceAll = $extPaySystemList;
			$this->arActivePaySystems += $extPaySystemList;

			if ($extPaySystemId !== 0 && array_key_exists($extPaySystemId, $this->arPaySystemServiceAll))
			{
				$selectedPaySystem = $this->arPaySystemServiceAll[$extPaySystemId];
			}
			else
			{
				reset($this->arPaySystemServiceAll);

				if (key($this->arPaySystemServiceAll) == $innerPaySystemId)
				{
					if (count($this->arPaySystemServiceAll) > 1)
					{
						next($this->arPaySystemServiceAll);
					}
					elseif ($sumToSpend > 0)
					{
						$extPayment->delete();
						$extPayment = null;

						/** @var Payment $innerPayment */
						$innerPayment = $this->getInnerPayment($order);
						if (empty($innerPayment))
						{
							$innerPayment = $paymentCollection->getInnerPayment();
							if (!$innerPayment)
							{
								$innerPayment = $paymentCollection->createInnerPayment();
							}
						}

						$sumToPay = $remainingSum > $sumToSpend ? $sumToSpend : $remainingSum;
						$innerPayment->setField('SUM', $sumToPay);

						if ($order->getPrice() - $paymentCollection->getSum() > 0)
						{
							$this->addWarning(Loc::getMessage('INNER_PAYMENT_BALANCE_ERROR'), self::PAY_SYSTEM_BLOCK);

							$r = new Result();
							$r->addError(new Sale\ResultWarning(
								Loc::getMessage('INNER_PAYMENT_BALANCE_ERROR'),
								'SALE_INNER_PAYMENT_BALANCE_ERROR'
							));

							Sale\EntityMarker::addMarker($order, $innerPayment, $r);
							$innerPayment->setField('MARKED', 'Y');
						}
					}
					else
					{
						unset($this->arActivePaySystems[$innerPaySystemId]);
						unset($this->arPaySystemServiceAll[$innerPaySystemId]);
					}
				}

				$selectedPaySystem = current($this->arPaySystemServiceAll);
			}

			if (!array_key_exists((int)$selectedPaySystem['ID'], $this->arPaySystemServiceAll))
			{
				$this->addError(Loc::getMessage('P2D_CALCULATE_ERROR'), self::PAY_SYSTEM_BLOCK);
				$this->addError(Loc::getMessage('P2D_CALCULATE_ERROR'), self::DELIVERY_BLOCK);
			}

			if (!empty($selectedPaySystem))
			{
				if ($selectedPaySystem['ID'] != $innerPaySystemId)
				{
					$codSum = 0;
					$service = PaySystem\Manager::getObjectById($selectedPaySystem['ID']);
					if ($service !== null)
					{
						$codSum = $service->getPaymentPrice($extPayment);
					}

					$extPayment->setFields([
						'PAY_SYSTEM_ID' => $selectedPaySystem['ID'],
						'PAY_SYSTEM_NAME' => $selectedPaySystem['NAME'],
						'PRICE_COD' => $codSum,
					]);

					$this->arUserResult['PAY_SYSTEM_ID'] = $selectedPaySystem['ID'];
				}
			}
			elseif (!empty($extPayment))
			{
				$extPayment->delete();
				$extPayment = null;
			}

			if (!empty($this->arUserResult['PREPAYMENT_MODE']))
			{
				$this->showOnlyPrepaymentPs($this->arUserResult['PAY_SYSTEM_ID']);
			}
		}

		if (!empty($innerPayment) && !empty($extPayment) && $remainingSum == 0)
		{
			$extPayment->delete();
			$extPayment = null;
		}
	}
}