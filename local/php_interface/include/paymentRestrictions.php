<?
use Bitrix\Sale\Services\Base;
use Bitrix\Sale\Internals\Entity;
use Bitrix\Sale\Payment;

Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'onSalePaySystemRestrictionsClassNamesBuildList',
    'displayOnlyInAdminPanelFunction'
);

function displayOnlyInAdminPanelFunction() {
    return new \Bitrix\Main\EventResult(
        \Bitrix\Main\EventResult::SUCCESS,
        array(
            '\DisplayOnlyInAdminPanel' => '/local/php_interface/include/paymentRestrictions.php',
        )
    );
}

class DisplayOnlyInAdminPanel extends Base\Restriction
{
    public static function getClassTitle()
    {
        return 'показывать только в админке';
    }

    public static function getClassDescription()
    {
        return 'Показывать только в админке';
    }

    /**
     * @param       $params
     * @param array $restrictionParams
     * @param int   $serviceId
     *
     * @return bool
     */
    public static function check($params, array $restrictionParams, $serviceId = 0)
    {
        if(SITE_ID == LANGUAGE_ID) {
            return true;
        }

        return false;
    }

    /**
     * @param Entity $entity
     *
     * @return array
     */
    protected static function extractParams(Entity $entity)
    {
        return array(
            'IS_ADMIN_PANEL' => "Y",
        );
    }

    /**
     * @param $entityId
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getParamsStructure($entityId = 0)
    {
        return array(
            "IS_ADMIN_PANEL" => array(
                "TYPE"     => "ENUM",
                'MULTIPLE' => 'N',
                "LABEL"    => "Показывать только в админке",
                "OPTIONS"  => ["Y" => "Да"],
            ),
        );
    }
}


Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'onSalePaySystemRestrictionsClassNamesBuildList',
    'displayOnlyForAdmin'
);

function displayOnlyForAdmin() {
    return new \Bitrix\Main\EventResult(
        \Bitrix\Main\EventResult::SUCCESS,
        array(
            '\DisplayOnlyForAdmin' => '/local/php_interface/include/paymentRestrictions.php',
        )
    );
}

class DisplayOnlyForAdmin extends Base\Restriction
{
    public static function getClassTitle()
    {
        return 'показывать только для админов';
    }

    public static function getClassDescription()
    {
        return 'Показывать только для админов';
    }

    /**
     * @param       $params
     * @param array $restrictionParams
     * @param int   $serviceId
     *
     * @return bool
     */
    public static function check($params, array $restrictionParams, $serviceId = 0)
    {
        global $USER;
        CModule::IncludeModule("wl.snailshop");

        if($USER->isAdmin() || WL\SnailShop::userIsStaff()) {
            return true;
        }

        return false;
    }

    /**
     * @param Entity $entity
     *
     * @return array
     */
    protected static function extractParams(Entity $entity)
    {
        return array(
            'IS_FOR_ADMIN' => "Y",
        );
    }

    /**
     * @param $entityId
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getParamsStructure($entityId = 0)
    {
        return array(
            "IS_FOR_ADMIN" => array(
                "TYPE"     => "ENUM",
                'MULTIPLE' => 'N',
                "LABEL"    => "Показывать только для админов",
                "OPTIONS"  => ["Y" => "Да"],
            ),
        );
    }
}

