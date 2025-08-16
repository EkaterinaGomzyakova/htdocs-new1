<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view",
	"",
	Array(
		"API_KEY" => "4839edff-c2df-45e6-9e9a-ef52c8822aa0",
		"CONTROLS" => array("SMALLZOOM","SCALELINE"),
		"INIT_MAP_TYPE" => "MAP",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:52.603932519153716;s:10:\"yandex_lon\";d:39.584477312081695;s:12:\"yandex_scale\";i:13;s:10:\"PLACEMARKS\";a:2:{i:0;a:3:{s:3:\"LON\";d:39.56261544193;s:3:\"LAT\";d:52.593387916117;s:4:\"TEXT\";s:24:\"пр. Победы, 61Б\";}i:1;a:3:{s:3:\"LON\";d:39.600266498989;s:3:\"LAT\";d:52.611003101024;s:4:\"TEXT\";s:21:\"ул. Зегеля, 2\";}}}",
		"MAP_HEIGHT" => "500",
		"MAP_ID" => "",
		"MAP_WIDTH" => "100%",
		"OPTIONS" => array("ENABLE_DBLCLICK_ZOOM","ENABLE_DRAGGING")
	)
);?>