<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<? $APPLICATION->IncludeComponent(
	"wl:menu.sections.from.property",
	"buttons",
	[
		'IBLOCK_ID' => GOODS_IBLOCK_ID,
		'PROPERTY_CODE' => 'SCOPE',
		'URL_TEMPLATE' => '',
		'CACHE_TIME' => 3600
	]
); ?>