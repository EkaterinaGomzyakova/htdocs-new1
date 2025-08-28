<? $APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"front_jor", // <-- Название папки с новым шаблоном
	array(
		"IBLOCK_TYPE" => "aspro_next_content",
		"IBLOCK_ID" => "21", // Ваш инфоблок "Журнал"
		"NEWS_COUNT" => "4", // 1 главная + 3 в сетке
		
		"SORT_BY1" => "SORT",       // Сортируем по полю "Сортировка"
		"SORT_ORDER1" => "ASC",     // Чем меньше число, тем выше элемент
		"SORT_BY2" => "ACTIVE_FROM",
		"SORT_ORDER2" => "DESC",

		"FIELD_CODE" => array("NAME", "PREVIEW_PICTURE", ""), // <-- ОБЯЗАТЕЛЬНО!

		// Остальные параметры без изменений
		"ACTIVE_DATE_FORMAT" => "j F Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"TITLE_BLOCK" => "Журнал Clanbeauty", // <-- Название блока
		"TITLE_BLOCK_ALL" => "Все", // <-- Текст ссылки
		"ALL_URL" => "articles/",         // <-- Адрес ссылки
	),
	false
); ?>