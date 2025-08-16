<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>

<? CModule::IncludeModule("wl.snailshop");?>
<? if (\WL\SnailShop::userIsStaff() && $_REQUEST['USER_ID'] > 0) { ?>
    <? $APPLICATION->IncludeComponent(
        "clanbeauty:roulete",
        "",
        [
            "IBLOCK_ID" => WL\Iblock::getIblockIDByCode("roulete"),
            "USER_ID" => $_REQUEST['USER_ID'],
        ],
        false
    ); ?>
<? } else { ?>
    Вы не сотрудник или не указан пользователь
<? } ?>

<style>
    #header,
    .page-top-wrapper {
        display: none;
    }

    body {
        background-image: url('/local/components/clanbeauty/roulete/templates/.default/img/background.jpg') !important;
        background-position: top center !important;
        background-size: cover !important;
    }

    #panel {
        display: none;
    }

    .wraps>.wrapper_inner {
        background: transparent !important;
    }
</style>