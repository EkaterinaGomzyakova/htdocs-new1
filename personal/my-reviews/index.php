<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Мои отзывы");
if (!$USER->IsAuthorized()) {
    LocalRedirect('/auth/');
} else { ?>
    <?$APPLICATION->IncludeComponent('clanbeauty:reviews', '',
        [
            "REVIEWS_IBLOCK_ID" => "32",
            "CATALOG_IBLOCK_ID" => "2",
        ], false)?>
<? } ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>