<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?
global $USER;
?>
<? if ($USER->isAdmin()) { ?>
    <? $APPLICATION->IncludeComponent(
        "clanbeauty:roulete.for.public.events",
        "",
        [
            "IBLOCK_ID" => WL\Iblock::getIblockIDByCode("roulete"),
        ],
        false
    ); ?>
<? } else { ?>
    <? ShowMessage('Авторизуйтесь, чтобы посмотреть что внутри!'); ?>
<? } ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>