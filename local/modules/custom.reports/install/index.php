<?

Class custom_reports extends CModule {

    var $MODULE_ID = "custom.reports";
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

        $this->MODULE_NAME = GetMessage("ADEV_REPORTS_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("ADEV_REPORTS_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = GetMessage('ADEV_REPORTS_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('ADEV_REPORTS_MODULE_PARTNER_URI');
    }

    function DoInstall() {
        global $APPLICATION;
        RegisterModule($this->MODULE_ID);
    }

    function DoUninstall() {
        global $APPLICATION;
        UnRegisterModule($this->MODULE_ID);
    }

}

?>