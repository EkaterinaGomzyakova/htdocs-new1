<?global $arTheme;?>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"unified_fixed",
	Array(
		"COMPONENT_TEMPLATE" => "unified_fixed",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"DELAY" => "N",
		"MAX_LEVEL" => $arTheme["MAX_DEPTH_MENU"]["VALUE"],
		"ALLOW_MULTI_SELECT" => "Y",
		"ROOT_MENU_TYPE" => "top_content_multilevel",
		"CHILD_MENU_TYPE" => "left",
		"CACHE_SELECTED_ITEMS" => "N",
		"ALLOW_MULTI_SELECT" => "Y",
		"USE_EXT" => "Y"
	)
);?>