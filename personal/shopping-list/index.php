<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Список покупок");
if (!$USER->isAuthorized()) {
    LocalRedirect(SITE_DIR . 'auth');
} else { ?>
    <?$APPLICATION->IncludeComponent('clanbeauty:shopping.list', '', [], false)?>
<? } ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>