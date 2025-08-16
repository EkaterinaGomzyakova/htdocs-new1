<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

// получаем идентификатор модуля
$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);
// подключаем наш модуль
Loader::includeModule($module_id);

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("WL_TELEGRAM_BOT_MESSENGER_MAIN_TAB_SET"),
        "OPTIONS" => array(
            Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_COMMON"),
            array(
                "api_key",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_API_KEY"),
                "1777104266:AAFzqHiFvyRSfFQCMegEx60j5JsssGJo-94",
                array("text", 20)
            ),
            array(
                "name",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_NAME_BOT"),
                "jahenewtestmel_bot",
                array("text", 20)
            ),
            array(
                "chat_id",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_CHAT_ID"),
                "1187407404",
                array("text", 20)
            ),
        )
    ),
   );

if($request->isPost() && check_bitrix_sessid()){

    foreach($aTabs as $aTab){

        foreach($aTab["OPTIONS"] as $arOption){

            if(!is_array($arOption)){

                continue;
            }

            if($arOption["note"]){

                continue;
            }

            if ($request["apply"]) {

                $optionValue = $request->getPost($arOption[0]);
                if ($arOption[0] == "switch_on") {

                    if ($optionValue == "") {

                        $optionValue = "N";
                    }
                }
               Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
            } elseif ($request["default"]) {

                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANG);
}
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();?>
<form action="<? echo($APPLICATION->GetCurPage()); ?>?mid=<? echo($module_id); ?>&lang=<?= LANGUAGE_ID; ?>" method="post">
    <?= bitrix_sessid_post(); ?>
    <?
    foreach($aTabs as $aTab){

        if($aTab["OPTIONS"]){

            $tabControl->BeginNextTab();

            __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        }
    }

    $tabControl->Buttons();
    ?>

    <input type="submit" name="apply" value="<? echo(Loc::GetMessage("FALBAR_TOTOP_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />
    <input type="submit" name="default" value="<? echo(Loc::GetMessage("FALBAR_TOTOP_OPTIONS_INPUT_DEFAULT")); ?>" />

    <?
    echo(bitrix_sessid_post());
    ?>

</form>
<?$tabControl->EndTab();?>

