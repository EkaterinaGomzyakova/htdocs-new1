<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Yandex.Zen RSS");
?>
<?$APPLICATION->IncludeComponent(
	"dev2fun:yandex.zen",
	"",
	Array(
		"COUNT" => "100",
		"FILTER_NAME" => "",
		"IBLOCK_ID" => [21],
		"SORT_FIELD" => "created",
		"SORT_ORDER" => "desc"
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>