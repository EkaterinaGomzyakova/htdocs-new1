<?

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

// получаем идентификатор модуля
$request = Application::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);
$lang = LANGUAGE_ID;
$url = "{$APPLICATION->getCurPage()}?mid={$module_id}&lang={$lang}";
// подключаем наш модуль
Loader::includeModule($module_id);

$aTabs = [
    [
        "DIV"     => "edit1",
        "TAB"     => "Настройки",
        "OPTIONS" => [
            Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_COMMON"),
            [
                "server_url",
                "Адрес Сервера лояльности",
                "http://onec.clanbeauty.ru:6080/UNF_main/hs/loyaltyservice",
                ["text", 100],
            ],
            [
                "user_name",
                "Имя пользователя",
                "",
                ["text", 20],
            ],
            [
                "user_password",
                "Пароль",
                "",
                ["text", 20],
            ],
            [
                "sync_relevance_period",
                "Время релевантности бонусов (минут)",
                "5",
                ["text", 20],
            ],
            [
                "count_attempt",
                "Количество попыток",
                "60",
                ["text", 20],
            ],
        ],
    ],
];

if ($request->isPost() && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        __AdmSettingsSaveOptions($module_id, $aTab['OPTIONS']);
    }
    LocalRedirect($url);
}

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin(); ?>
<form action="<? echo ($APPLICATION->GetCurPage()); ?>?mid=<? echo ($module_id); ?>&lang=<?= LANGUAGE_ID; ?>"
    method="post">
    <?= bitrix_sessid_post(); ?>
    <?
    foreach ($aTabs as $aTab) {
        if ($aTab["OPTIONS"]) {
            $tabControl->BeginNextTab();
            __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        }
    }

    $tabControl->Buttons();
    ?>

    <input type="submit" name="apply" value="Сохранить" class="adm-btn-save" />


    </form>
<? $tabControl->EndTab(); ?>