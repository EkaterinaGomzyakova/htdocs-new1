<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

Class wl_onec_loyalty extends CModule {

    var $MODULE_ID = "wl.onec_loyalty";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function __construct() {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        $path = substr($path, 0, strlen($path) - strlen("/install"));
        @include(GetLangFileName($path . "/lang/", "/install/index.php"));
        IncludeModuleLangFile($path . "/install/index.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = "Clanbeauty 1C: Сервер Лояльности";
        $this->MODULE_DESCRIPTION = "Clanbeauty 1C: Сервер Лояльности";
        $this->PARTNER_NAME = "WL";
        $this->PARTNER_URI = "https://wlagency.ru";
    }

    function DoInstall() {
        RegisterModule($this->MODULE_ID);
        return true;
    }

    function DoUninstall() {
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

}
?>
