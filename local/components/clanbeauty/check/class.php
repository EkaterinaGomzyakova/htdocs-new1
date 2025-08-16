<?php

namespace Clanbeauty;

use CIBlockElement;
use CModule;
use CCatalogProduct;
use CSite;
use CIBlockSection;
use CBitrixComponent;
use CIBlock;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use \Bitrix\Sale\Order;

/**
 * @property array $arResult
 */
class Check extends CBitrixComponent implements Controllerable
{
	public function configureActions()
	{
		return [
			'updateSection' => [
				'prefilters' => [new Csrf(), new Authentication()],
			],
		];
	}

	public function updateSectionAction($methodName)
	{
		$result = false;
		$availableMethods = get_class_methods(get_class($this));

		if (in_array($methodName, $availableMethods)) {
			$result = self::{$methodName}();
		}

		return $result;
	}

	public function executeComponent()
	{
		CModule::IncludeModule("iblock");
		CModule::IncludeModule("catalog");
		CModule::includeModule("highloadblock");

		$this->arResult = [];

		$timeStartTotal = microtime(true);


		$timeStart = microtime(true);
		$this->arResult['TEST']["CHECK_WITH_MARKING_NOT_ISSUED"]["NAME"] = "Не напечатан чек для отгрузки с маркировкой";
		$this->arResult['TEST']["CHECK_WITH_MARKING_NOT_ISSUED"]["VALUE"] = $this->checkReceiptsForMarkingCodeShipments();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["CHECK_WITH_MARKING_NOT_ISSUED"]["TIME"] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["UNPUBLISHED_COMMENTS"]["NAME"] = "Неопубликованные комментарии";
		$this->arResult['TEST']["UNPUBLISHED_COMMENTS"]["VALUE"] = $this->checkUnpublishedComments();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["UNPUBLISHED_COMMENTS"]["TIME"] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["PRODUCTS_WITHOUT_PURCHASING_PRICE"]["NAME"] = "Товары без закупочной цены";
		$this->arResult['TEST']["PRODUCTS_WITHOUT_PURCHASING_PRICE"]["VALUE"] = $this->checkProductsWithoutPurchasingPrice();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["PRODUCTS_WITHOUT_PURCHASING_PRICE"]["TIME"] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["ARTICLES_LATE_PUBLISHING"]["NAME"] = "Статьи не публиковались больше недели";
		$this->arResult['TEST']["ARTICLES_LATE_PUBLISHING"]["VALUE"] = $this->checkArticlesLong();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["ARTICLES_LATE_PUBLISHING"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["ORDERS_INCORRECT_SUM"]["NAME"] = "Заказы с неверными суммами оплат и отсутствующим флагом оплачено";
		$this->arResult['TEST']["ORDERS_INCORRECT_SUM"]["VALUE"] = $this->checkOrdersIncorrectSum();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["ORDERS_INCORRECT_SUM"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITHOUT_PRICE"]["NAME"] = "Активные товары без розничных цен";
		$this->arResult['TEST']["GOODS_WITHOUT_PRICE"]["VALUE"] = $this->checkGoodsWithoutPrice();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITHOUT_PRICE"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITH_DUPLICATED_VOLUME"]["NAME"] = "Товары с дублирующимся объемом в наименовании";
		$this->arResult['TEST']["GOODS_WITH_DUPLICATED_VOLUME"]["VALUE"] = $this->checkGoodsWithRepeatingVolumeInName();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITH_DUPLICATED_VOLUME"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITHOUT_DESCRIPTION"]["NAME"] = "Активные товары без детального описания";
		$this->arResult['TEST']["GOODS_WITHOUT_DESCRIPTION"]["VALUE"] = $this->checkGoodsWithoutDescription();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITHOUT_DESCRIPTION"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITHOUT_PICTURE"]["NAME"] = "Активные товары без картинок";
		$this->arResult['TEST']["GOODS_WITHOUT_PICTURE"]["VALUE"] = $this->checkGoodsWithoutPicture();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITHOUT_PICTURE"]['TIME'] = $time;


		// $this->arResult['TEST']["GOODSWITHOUTBARCODE"]["NAME"] = "Активные товары без штрихкода";
		// $this->arResult['TEST']["GOODSWITHOUTBARCODE"]["VALUE"] = $this->checkGoodsWithoutBarcode();


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITHOUT_VOLUME"]["NAME"] = "Активные товары без объема";
		$this->arResult['TEST']["GOODS_WITHOUT_VOLUME"]["VALUE"] = $this->checkGoodsWithoutVolume();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITHOUT_VOLUME"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["BRANDS_WITHOUT_DESCRIPTION"]["NAME"] = "Бренды с пустым описанием";
		$this->arResult['TEST']["BRANDS_WITHOUT_DESCRIPTION"]["VALUE"] = $this->checkBrandsWithEmptyDescription();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["BRANDS_WITHOUT_DESCRIPTION"]['TIME'] = $time;


		// $this->arResult['TEST']["GOODS_WITHOUT_MIN_REMAINS"]["NAME"] = "Активные товары без минимального остатка";
		// $this->arResult['TEST']["GOODS_WITHOUT_MIN_REMAINS"]["VALUE"] = $this->checkGoodsWithoutMinRemains();


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITHOUT_SIMILAR"]["NAME"] = "Активные товары без списка (с этим товаров рекомендуют) и (похожие товары)";
		$this->arResult['TEST']["GOODS_WITHOUT_SIMILAR"]["VALUE"] = $this->checkGoodsWithoutSimilar();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITHOUT_SIMILAR"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_IN_STOCK_IN_ARCHIVE_SECTION"]["NAME"] = "Товары в архиве с положительным остатком";
		$this->arResult['TEST']["GOODS_IN_STOCK_IN_ARCHIVE_SECTION"]["VALUE"] = $this->checkGoodsInStockInArchiveSection();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_IN_STOCK_IN_ARCHIVE_SECTION"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITHOUT_SCOPE_OF_APPLICATION"]["NAME"] = "Активные товары, где не заполнена область применения";
		$this->arResult['TEST']["GOODS_WITHOUT_SCOPE_OF_APPLICATION"]["VALUE"] = $this->checkGoodsWithoutScopeOffApplication();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITHOUT_SCOPE_OF_APPLICATION"]['TIME'] = $time;

		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITHOUT_BRAND"]["NAME"] = "Активные товары, где не заполнен бренд";
		$this->arResult['TEST']["GOODS_WITHOUT_BRAND"]["VALUE"] = $this->checkGoodsWithoutBrand();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITHOUT_BRAND"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITHOUT_COMPONENTS"]["NAME"] = "Активные товары, где не заполнен компонент";
		$this->arResult['TEST']["GOODS_WITHOUT_COMPONENTS"]["VALUE"] = $this->checkGoodsWithoutComponent();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITHOUT_COMPONENTS"]["TIME"] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITHOUT_CONTENTS"]["NAME"] = "Активные товары, где не заполнен состав";
		$this->arResult['TEST']["GOODS_WITHOUT_CONTENTS"]["VALUE"] = $this->checkGoodsWithoutContents();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITHOUT_CONTENTS"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITHOUT_SKIN_TYPES"]["NAME"] = "Активные товары, где не заполнен тип кожи";
		$this->arResult['TEST']["GOODS_WITHOUT_SKIN_TYPES"]["VALUE"] = $this->checkGoodsWithoutSkinTypes();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITHOUT_SKIN_TYPES"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["SECTIONS_WITHOUT_DESCRIPTION"]["NAME"] = "У всех активных разделов первого и второго уровня есть описания";
		$this->arResult['TEST']["SECTIONS_WITHOUT_DESCRIPTION"]["VALUE"] = $this->checkSectionsWithoutDescription();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["SECTIONS_WITHOUT_DESCRIPTION"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITH_WRONG_SYMBOLS_IN_NAME"]["NAME"] = "В названии товаров нет запрещенных символов";
		$this->arResult['TEST']["GOODS_WITH_WRONG_SYMBOLS_IN_NAME"]["VALUE"] = $this->checkGoodsWithWrongSymbolsInName();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITH_WRONG_SYMBOLS_IN_NAME"]['TIME'] = $time;


		$timeStart = microtime(true);
		$this->arResult['TEST']["GOODS_WITH_WRONG_STORE_QUANTITY"]["NAME"] = "Товары с неверным доступным количеством или лишним флагом доступности";
		$this->arResult['TEST']["GOODS_WITH_WRONG_STORE_QUANTITY"]["VALUE"] = $this->checkGoodsWithWrongStoreQuantity();
		$time = microtime(true) - $timeStart;
		$this->arResult['TEST']["GOODS_WITH_WRONG_STORE_QUANTITY"]['TIME'] = $time;


		usort($this->arResult['TEST'], function ($a, $b) {
			if ($a["VALUE"]['isSuccess'] == $b["VALUE"]['isSuccess']) {
				return 0;
			}

			return ($a["VALUE"]['isSuccess'] < $b["VALUE"]['isSuccess']) ? -1 : 1;
		});

		$timeStopTotal = microtime(true) - $timeStartTotal;
		$this->arResult['TIME_ELAPSED'] = $timeStopTotal;

		$this->includeComponentTemplate();
	}

	/**
	 * Проверить, что у для всех отгрузок, в которых есть товары с маркировкой, выпущен чек
	 * @return array
	 */
	public static function checkReceiptsForMarkingCodeShipments(): array
	{
		$arItems = [];

		$dbBaskets = \Bitrix\Sale\Basket::getList([
			'filter' => [
				'!MARKING_CODE_GROUP' => false,
				'!ORDER_ID' => false
			],
		]);

		while ($arBasket = $dbBaskets->fetch()) {
			$arShipmentItem = \Bitrix\Sale\ShipmentItem::getList([
				'filter' => ['BASKET_ID' => $arBasket['ID']],
				'select' => ['ORDER_DELIVERY_ID']
			])->fetch();

			$arShipment = \Bitrix\Sale\Shipment::getList([
				'filter' => ['ID' => $arShipmentItem['ORDER_DELIVERY_ID']],
				'select' => ['ID', 'DEDUCTED', 'ORDER_ID']
			])->fetch();

			if ($arShipment['DEDUCTED'] == "Y") {
				$arCheck = \Bitrix\Sale\Cashbox\Internals\CashboxCheckTable::getList([
					'filter' => ['SHIPMENT_ID' => $arShipment['ID'], 'STATUS' => 'Y'],
					'select' => ['ID', 'STATUS']
				])->fetch();

				if (!$arCheck) {
					$arItems[] = ['name' => 'Отгрузка №' . $arShipment['ID'], 'url' => '/bitrix/admin/sale_order_view.php?ID=' . $arShipment['ORDER_ID']];
				}
			}
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Товары где QUANTITY отличается от суммы остакта товаров на складах
	 * @return array
	 */
	public static function checkGoodsWithWrongStoreQuantity(): array
	{
		$arItems = [];
		$arProducts = [];

		$dbElements = CIBlockElement::GetList([], ['ACTIVE' => 'Y', 'IBLOCK_ID' => [GOODS_IBLOCK_ID, SKU_IBLOCK_ID], "TYPE" => [\Bitrix\Catalog\ProductTable::TYPE_PRODUCT, \Bitrix\Catalog\ProductTable::TYPE_OFFER]], false, false, ['ID', 'NAME', 'IBLOCK_ID', 'QUANTITY', 'QUANTITY_RESERVED', 'AVAILABLE']);
		while ($arElement = $dbElements->Fetch()) {
			$arProducts[$arElement['ID']] = $arElement;
		}

		$dbStoreProducts = \CCatalogStoreProduct::GetList([], ['PRODUCT_ID' => array_keys($arProducts)], false, false, ['PRODUCT_ID', 'STORE_ID', 'AMOUNT']);
		while ($arStoreProduct = $dbStoreProducts->Fetch()) {
			$arProducts[$arStoreProduct['PRODUCT_ID']]['STORES'][$arStoreProduct['STORE_ID']] = $arStoreProduct['AMOUNT'];
		}

		foreach ($arProducts as $arProduct) {
			if (!empty($arProduct['STORES']) && $arProduct['QUANTITY'] + $arProduct['QUANTITY_RESERVED'] != \array_sum($arProduct['STORES'])) {
				$arItems[] = ['name' => 'Товар с ID = ' . $arProduct['ID'], 'url' => CIBlock::GetAdminElementEditLink($arProduct['IBLOCK_ID'], $arProduct['ID'])];
			}
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}


	/**
	 * Товары с запрещенными символами в наименовании
	 * @return array
	 */
	public static function checkGoodsWithWrongSymbolsInName(): array
	{
		$arItems = [];

		$dbProducts = CIBlockElement::GetList([], ['IBLOCK_ID' => GOODS_IBLOCK_ID, 'ACTIVE' => 'Y'], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
		while ($arProduct = $dbProducts->Fetch()) {

			if (!preg_match("/^[\w\p{N}&\'\’\-\,:\.\+\№\(\)\%\*\/\[\]\"\#\!\»\« а-яА-Я]+$/iu", $arProduct['NAME'])) {
				$arItems[] = ['name' => $arProduct['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arProduct['IBLOCK_ID'], $arProduct['ID'])];
			}
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Получить неопублкиованные комментарии
	 * @return array
	 */
	public static function checkUnpublishedComments(): array
	{
		$arItems = [];
		$dbComments = CIBlockElement::GetList(['DATE_CREATE' => 'ASC'], ['IBLOCK_ID' => REVIEWS_IBLOCK_ID, 'ACTIVE' => 'N'], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_OBJECT.NAME']);
		while ($arComment = $dbComments->Fetch()) {
			$productName = '';
			if (!empty($arComment['PROPERTY_OBJECT_NAME'])) {
				$productName .= ' [' . $arComment['PROPERTY_OBJECT_NAME'] . ']';
			}

			$arItems[] = ['name' => $arComment['NAME'] . $productName, 'url' => CIBlock::GetAdminElementEditLink($arComment['IBLOCK_ID'], $arComment['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	public static function checkProductsWithoutPurchasingPrice(): array
	{
		$arItems = [];

		$arElements = [];
		$dbElements = CIBlockElement::GetList([], ['IBLOCK_ID' => [GOODS_IBLOCK_ID, SKU_IBLOCK_ID], "ACTIVE" => "Y", "TYPE" => [\Bitrix\Catalog\ProductTable::TYPE_PRODUCT, \Bitrix\Catalog\ProductTable::TYPE_OFFER], 'PURCHASING_PRICE' => false], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
		while ($arElement = $dbElements->Fetch()) {
			$arItems[] = ['name' => $arElement['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arElement['IBLOCK_ID'], $arElement['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}


	/**
	 * Активные Товары без розничных цен
	 * @return array
	 */
	public static function checkGoodsWithoutPrice(): array
	{
		$arItems = [];

		$dbElements = CIBlockElement::GetList(
			[],
			[
				'IBLOCK_ID' => GOODS_IBLOCK_ID,
				"ACTIVE" => "Y",
				'!SECTION_ID' => [167],
				'CATALOG_PRICE_1' => false
			],
			false,
			false,
			['ID', 'IBLOCK_ID', 'NAME']
		);
		while ($arItem = $dbElements->Fetch()) {
			$arItems[] = ['name' => $arItem['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arItem['IBLOCK_ID'], $arItem['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Бренды с пустым описанием
	 * @return array
	 */
	public static function checkBrandsWithEmptyDescription(): array
	{
		$arItems = [];

		$dbBrands = CIBlockElement::GetList([], ['IBLOCK_ID' => BRANDS_IBLOCK_ID, "ACTIVE" => "Y", "DETAIL_TEXT" => false], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_TEXT']);
		while ($arBrand = $dbBrands->Fetch()) {
			$arItems[] = ['name' => $arBrand['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arBrand['IBLOCK_ID'], $arBrand['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Товары с положительным остатком в разделе Архив
	 * @return array
	 */
	public static function checkGoodsInStockInArchiveSection(): array
	{
		$arItems = [];

		$dbProducts = CIBlockElement::GetList([], ['IBLOCK_ID' => GOODS_IBLOCK_ID, "SECTION_ID" => [152], ">QUANTITY" => 0], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
		while ($arProduct = $dbProducts->Fetch()) {
			$arItems[] = ['name' => $arProduct['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arProduct['IBLOCK_ID'], $arProduct['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Товары с дублирующимся свойством Объем в Наименовании
	 * @return array
	 */
	public static function checkGoodsWithRepeatingVolumeInName(): array
	{
		$arItems = [];

		$dbProducts = CIBlockElement::GetList([], ['IBLOCK_ID' => GOODS_IBLOCK_ID, '!PROPERTY_VOLUME' => false, "PROPERTY_NOT_CHECK_VOLUME_IN_NAME" => false, 'ACTIVE' => 'Y', 'SECTION_GLOBAL_ACTIVE' => 'Y'], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_VOLUME']);
		while ($arProduct = $dbProducts->Fetch()) {
			if (intval($arProduct['PROPERTY_VOLUME_VALUE']) > 0) {

				$ipropTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates(GOODS_IBLOCK_ID, $arProduct['ID']);
				$arProduct['SEO_TEMPLATES'] = $ipropTemplates->findTemplates();
				if (strpos($arProduct['NAME'], (string) intval($arProduct['PROPERTY_VOLUME_VALUE'])) && strpos($arProduct['SEO_TEMPLATES']['ELEMENT_META_TITLE']['TEMPLATE'], "this.property.VOLUME")) {
					$arItems[] = ['name' => $arProduct['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arProduct['IBLOCK_ID'], $arProduct['ID'])];
				}
			}
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Имя товара без детального описания
	 * @return array
	 */
	public static function checkGoodsWithoutDescription(): array
	{
		$arItems = [];

		$arSelect = ["ID", "IBLOCK_ID", "NAME", 'DETAIL_PAGE_URL'];
		$arFilter = ["IBLOCK_ID" => GOODS_IBLOCK_ID, "ACTIVE" => "Y", 'SECTION_GLOBAL_ACTIVE' => 'Y', "DETAIL_TEXT" => false, "!SECTION_ID" => [167]];
		$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$arItems[] = ['name' => $arFields['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arFields['IBLOCK_ID'], $arFields['ID'])];
		}
		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Имя товара без картинок
	 * @return array
	 */
	public static function checkGoodsWithoutPicture(): array
	{
		$arItems = [];

		$arSelect = ["ID", "IBLOCK_ID", "NAME", 'DETAIL_PAGE_URL'];
		$arFilter = [
			"IBLOCK_ID" => GOODS_IBLOCK_ID,
			"ACTIVE" => "Y",
			'SECTION_GLOBAL_ACTIVE' => 'Y',
			"DETAIL_PICTURE" => false,
			"!IBLOCK_SECTION_ID" => [167],
			"PROPERTY_NOT_CHECK_DETAIL_PICTURE" => false
		];
		$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$arItems[] = ['name' => $arFields['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arFields['IBLOCK_ID'], $arFields['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Имя товара без штрихкода, кроме раздела Миниатюры
	 * @return array
	 */
	public static function checkGoodsWithoutBarcode(): array
	{
		$arItems = [];

		$arSelect = ["ID", "IBLOCK_ID", "NAME", 'DETAIL_PAGE_URL'];
		$arFilter = ["IBLOCK_ID" => GOODS_IBLOCK_ID, "ACTIVE" => "Y", 'SECTION_GLOBAL_ACTIVE' => 'Y', "PROPERTY_BARCODE" => false];
		$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$dbSections = CIBlockElement::GetElementGroups($arFields['ID']);
			while ($arSection = $dbSections->Fetch()) {
				if (in_array($arSection['ID'], [135])) {
					continue 2;
				}
			}
			$arItems[] = ['name' => $arFields['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arFields['IBLOCK_ID'], $arFields['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Имя товара без объема
	 * @return array
	 */
	public static function checkGoodsWithoutVolume(): array
	{
		$arItems = [];

		$arSelect = ["ID", "IBLOCK_ID", "NAME", 'DETAIL_PAGE_URL'];
		$arFilter = ["IBLOCK_ID" => GOODS_IBLOCK_ID, "ACTIVE" => "Y", 'SECTION_GLOBAL_ACTIVE' => 'Y', "PROPERTY_VOLUME" => false, "PROPERTY_NOT_CHECK_VOLUME" => false];
		$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();

			$dbSections = CIBlockElement::GetElementGroups($arFields['ID']);
			while ($arSection = $dbSections->Fetch()) {
				if (in_array($arSection['ID'], [110, 111, 154, 167, 171, 172, 173, 174, 175])) {
					continue 2;
				}
			}

			$arItems[] = ['name' => $arFields['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arFields['IBLOCK_ID'], $arFields['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Имя товара без минимального остатка
	 * @return array
	 */
	public static function checkGoodsWithoutMinRemains(): array
	{
		$arItems = [];

		$arSelect = ["ID", "IBLOCK_ID", "NAME", 'DETAIL_PAGE_URL'];
		$arFilter = ["IBLOCK_ID" => GOODS_IBLOCK_ID, "ACTIVE" => "Y", 'SECTION_GLOBAL_ACTIVE' => 'Y', "PROPERTY_MIN_REMAINS" => false];
		$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$arItems[] = ['name' => $arFields['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arFields['IBLOCK_ID'], $arFields['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 *  Имя товара без списка "с этим товаров рекомендуют" и "похожие товары"
	 * @return array
	 */
	public static function checkGoodsWithoutSimilar(): array
	{
		$arItems = [];

		$arSelect = ["ID", "IBLOCK_ID", "NAME", 'DETAIL_PAGE_URL'];
		$arFilter = [
			"IBLOCK_ID" => GOODS_IBLOCK_ID,
			"ACTIVE" => "Y",
			"SECTION_GLOBAL_ACTIVE" => "Y",
			"PROPERTY_EXPANDABLES" => false,
			"PROPERTY_ASSOCIATED" => false,
			"!SECTION_ID" => [111, 167,]
		];
		$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$arItems[] = ['name' => $arFields['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arFields['IBLOCK_ID'], $arFields['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Имя товара, где не заполнен тип кожи для всех разделов кроме: Патчи, Архив, Подарочные сертификаты, Аксессуары, Для волос, Для тела
	 * @return array
	 */
	public static function checkGoodsWithoutSkinTypes(): array
	{
		$arItems = [];

		$arSelect = ["ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", 'DETAIL_PAGE_URL'];
		$arFilter = ["IBLOCK_ID" => GOODS_IBLOCK_ID, "ACTIVE" => "Y", "SECTION_GLOBAL_ACTIVE" => "Y", "PROPERTY_TIP_KOJI" => false, 'PROPERTY_NOT_CHECK_SKIN_TYPE' => false];
		$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$dbSections = CIBlockElement::GetElementGroups($arFields['ID']);
			while ($arSection = $dbSections->Fetch()) {
				if (in_array($arSection['ID'], [106, 107, 109, 110, 111, 120, 136, 137, 138, 141, 142, 145, 146, 147, 148, 149, 150, 151, 152, 154, 156, 160, 161, 162, 163, 164, 167, 169, 172, 173, 174, 175])) {
					continue 2;
				}
			}
			$arItems[] = ['name' => $arFields['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arFields['IBLOCK_ID'], $arFields['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Имя товара, где не заполнен бренд
	 * @return array
	 */
	public static function checkGoodsWithoutBrand(): array
	{
		$arItems = [];

		$arSelect = ["ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", 'DETAIL_PAGE_URL'];
		$arFilter = ["IBLOCK_ID" => GOODS_IBLOCK_ID, "ACTIVE" => "Y", "SECTION_GLOBAL_ACTIVE" => 'Y', "PROPERTY_BRAND" => false];
		$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$arItems[] = ['name' => $arFields['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arFields['IBLOCK_ID'], $arFields['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Имя товара, где не заполнена область применения для всех разделов кроме: Патчи, Архив, Подарочные сертификаты, Аксессуары, Для волос, Для тела
	 * @return array
	 */
	public static function checkGoodsWithoutScopeOffApplication(): array
	{
		$arItems = [];

		$excludedSectionsWithChildren = [];

		$arExcludeSectionCodes = [
			'ukhod',
			'dlya_ruk',
			'dlya_polosti_rta',
			'gigiena',
			'molochko_dlya_tela',
			'skraby',
			'burlyashchie_shariki',
			'aksessuary',
			'arkhiv',
			'avtozagar',
			'dlya_gub',
			'dlya_nog',
			'dlya_tela',
			'dlya_volos',
			'geli_dlya_dusha',
			'gidrofilnoe_maslo_balzam',
			'gift-certificates',
			'makiyazh',
			'masla',
			'ochishchayushchaya_voda',
			'patchi',
			'penki_geli_dlya_umyvaniya',
			'probniki',
			'sanskrin',
			'skraby',
			'sol_dlya_vann',
			'universalnye_geli'
		];

		$dbExcludedSections = CIBlockSection::GetList([], ['CODE' => $arExcludeSectionCodes], false, ['ID']);
		while ($arSection = $dbExcludedSections->Fetch()) {
			$excludedSectionsWithChildren[] = $arSection['ID'];
		}

		$dbChildSection = CIBlockSection::GetTreeList(['SECTION_ID' => $excludedSectionsWithChildren], ['ID']);
		while ($arSection = $dbChildSection->Fetch()) {
			$excludedSectionsWithChildren[] = $arSection['ID'];
		}

		$arSelect = ["ID", "IBLOCK_ID", "NAME"];
		$arFilter = ["IBLOCK_ID" => GOODS_IBLOCK_ID, "ACTIVE" => "Y", "SECTION_GLOBAL_ACTIVE" => 'Y', "PROPERTY_SCOPE_VALUE" => false, "PROPERTY_NOT_CHECK_SCOPE" => false, '!SECTION_ID' => $excludedSectionsWithChildren, '!IBLOCK_SECTION_ID' => $excludedSectionsWithChildren];
		$dbElements = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
		while ($arItem = $dbElements->Fetch()) {
			$arItems[] = ['name' => $arItem['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arItem['IBLOCK_ID'], $arItem['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}


	/**
	 * Имя товара, где не заполнены компоненты
	 * @return array
	 */
	public static function checkGoodsWithoutComponent(): array
	{
		$arItems = [];

		$arSelect = ["ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", 'DETAIL_PAGE_URL'];
		$arFilter = ["IBLOCK_ID" => GOODS_IBLOCK_ID, "ACTIVE" => "Y", "SECTION_GLOBAL_ACTIVE" => 'Y', "PROPERTY_COMPONENTS" => false, 'PROPERTY_NOT_CHECK_COMPONENTS' => false];
		$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$dbSections = CIBlockElement::GetElementGroups($arFields['ID']);
			while ($arSection = $dbSections->Fetch()) {
				if (in_array($arSection['ID'], [106, 107, 108, 109, 112, 111, 113, 114, 115, 116, 117, 123, 124, 152, 154, 171, 172, 173, 174, 175])) {
					continue 2;
				}
			}
			$arItems[] = ['name' => $arFields['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arFields['IBLOCK_ID'], $arFields['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Имя товара, где не заполнен состав
	 * @return array
	 */
	public static function checkGoodsWithoutContents(): array
	{
		$arItems = [];

		$arSelect = ["ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", 'DETAIL_PAGE_URL'];
		$arFilter = ["IBLOCK_ID" => GOODS_IBLOCK_ID, "ACTIVE" => "Y", "SECTION_GLOBAL_ACTIVE" => 'Y', "PROPERTY_CONTENTS" => false, "PROPERTY_NOT_CHECK_CONTENTS" => false];
		$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$dbSections = CIBlockElement::GetElementGroups($arFields['ID']);
			while ($arSection = $dbSections->Fetch()) {
				if (in_array($arSection['ID'], [111, 154, 167])) {
					continue 2;
				}
			}
			$arItems[] = ['name' => $arFields['NAME'], 'url' => CIBlock::GetAdminElementEditLink($arFields['IBLOCK_ID'], $arFields['ID'])];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * Статьи, где дата последней публикации старше недели
	 * @return array
	 */
	public static function checkArticlesLong(): array
	{
		$dateLong = 7;
		$arSelect = ["ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "DATE_CREATE"];
		$arFilter = ["IBLOCK_ID" => 21, "ACTIVE" => "Y", ">=DATE_CREATE" => date($GLOBALS['DB']->DateFormatToPHP(CSite::GetDateFormat("FULL")), mktime(0, 0, 0, date('m'), date('d') - $dateLong, date('Y')))];
		$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
		}

		$result = [
			'isSuccess' => !empty($arFields),
			'methodName' => __FUNCTION__,
		];

		return $result;
	}


	/**
	 * Заказы с неверными суммами оплат и отсутствующим флагом оплачено
	 * @return array
	 */
	public static function checkOrdersIncorrectSum(): array
	{
		$arItems = [];

		$arFilter = ["PAYED" => "N", "STATUS_ID" => "F", ">SUM_PAID" => 0, '>DATE_BILL' => date('d.m.Y', strtotime('-1 month'))];
		$arOrders = Order::loadByFilter(['filter' => $arFilter]);
		foreach ($arOrders as $d7order) {
			$arItems[] = ['name' => $d7order->getId(), 'url' => '/bitrix/admin/sale_order_view.php?ID=' . $d7order->getId()];
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}

	/**
	 * У всех активных разделов первого и второго уровня есть описания
	 * @return array
	 */
	public static function checkSectionsWithoutDescription(): array
	{
		$arItems = [];

		$arSelect = [];
		$arFilter = ["IBLOCK_ID" => GOODS_IBLOCK_ID, "ACTIVE" => "Y", "GLOBAL_ACTIVE" => 'Y', "<=DEPTH_LEVEL" => 2, "DESCRIPTION" => false];
		$res_section = CIBlockSection::GetList([], $arFilter, false, $arSelect, false);
		while ($ob = $res_section->GetNextElement()) {
			$arFields = $ob->GetFields();
			if (empty($arFields['DESCRIPTION']) == true) {
				$arItems[] = ['name' => $arFields['NAME'], 'url' => '/bitrix/admin/iblock_section_edit.php?IBLOCK_ID=2&type=catalog&lang=ru&ID=' . $arFields['ID']];
			}
		}

		$result = [
			'isSuccess' => empty($arItems),
			'items' => $arItems,
			'methodName' => __FUNCTION__,
		];

		return $result;
	}
}
