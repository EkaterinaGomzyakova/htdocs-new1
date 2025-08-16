<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>
<? \Bitrix\Main\Localization\Loc::loadMessages(__FILE__); ?>
<? $APPLICATION->AddChainItem(GetMessage("TITLE")); ?>
<? $APPLICATION->SetTitle(GetMessage("TITLE")); ?>
<? $APPLICATION->SetPageProperty("TITLE_CLASS", "center"); ?>
<style type="text/css">
    .left-menu-md,
    body .container.cabinte-page .maxwidth-theme .left-menu-md,
    .right-menu-md,
    body .container.cabinte-page .maxwidth-theme .right-menu-md {
        display: none !important;
    }

    .content-md {
        width: 100%;
    }
</style>
<? global $USER, $APPLICATION;
if (!$USER->IsAuthorized()) {
    if (isset($_REQUEST['REGISTER']['PERSONAL_PHONE'])) {
        $_REQUEST['REGISTER']['LOGIN'] = '+' . preg_replace("/[^0-9]/", '', $_REQUEST['REGISTER']['PERSONAL_PHONE']);
        $_REQUEST['REGISTER']['PHONE_NUMBER'] = $_REQUEST['REGISTER']['LOGIN'];
        $_REQUEST['REGISTER']['PERSONAL_PHONE'] = $_REQUEST['REGISTER']['LOGIN'];
    }
    ?>
    <?
    if (!empty($_REQUEST['REGISTER']['PERSONAL_BIRTHDAY'])) {
        $_REQUEST['REGISTER']['PERSONAL_BIRTHDAY'] = \CDatabase::FormatDate($_REQUEST['REGISTER']['PERSONAL_BIRTHDAY'], "YYYY-MM-DD", "DD.MM.YYYY");
    }
    ?>
    <? $APPLICATION->IncludeComponent(
        "bitrix:main.register",
        "main",
        array(
            "SHOW_FIELDS" => array("LOGIN", "LAST_NAME", "NAME", "SECOND_NAME", "EMAIL", "PERSONAL_PHONE", "PERSONAL_BIRTHDAY"),
            "REQUIRED_FIELDS" => array("NAME", "PERSONAL_PHONE", "EMAIL"),
            "AUTH" => "Y",
            "USE_BACKURL" => "Y",
            "SUCCESS_PAGE" => "",
            "SET_TITLE" => "N",
            "USER_PROPERTY" => array("UF_DENY_SMS"),
            "USER_PROPERTY_NAME" => "",
        )
    ); ?>
<? } else {
    LocalRedirect($arParams["SEF_FOLDER"]);
} ?>