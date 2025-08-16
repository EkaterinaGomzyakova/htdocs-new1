<?php
global $APPLICATION;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;

$request = Application::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);
$lang = LANGUAGE_ID;
$url = "{$APPLICATION->getCurPage()}?mid={$module_id}&lang={$lang}";
Loader::includeModule($module_id);
Asset::getInstance()->addJs('/local/modules/wl.delivery_area/assets/js/options.js');
if (Option::get($module_id, 'yandex_api_key') && Option::get($module_id, 'yandex_suggests_api_key')) {
    Asset::getInstance()->addString('<script src="https://api-maps.yandex.ru/2.1/?apikey=' . Option::get($module_id, 'yandex_api_key') . '&lang=ru_RU" type="text/javascript"></script>');
}

$aTabs = [
    [
        "DIV" => "wl_delivery_area_tab_1",
        "TAB" => "Основные настройки",
        "TITLE" => "Основные настройки",
        "OPTIONS" => [
            'map' => ['yandex_coordinates', 'Координаты области', '', ['text']],
            ['price_in_area', 'Стоимость доставки внутри зоны:', '', ['text', 10]],
            ['price_out_area', 'Стоимость доставки вне зоны:', '', ['text', 10]],
        ]
    ],
    [
        "DIV" => "wl_delivery_area_tab_2",
        "TAB" => "Служебные данные",
        "TITLE" => "Служебные данные",
        "OPTIONS" => [
            ['yandex_api_key', 'Ключ Yandex.Карты:', '', ['text', 80]],
            ['yandex_suggests_api_key', 'Ключ Подсказок Yandex.Карты:', '', ['text', 80]],
            ['property_address_delivery', 'Символьный код свойства с адресом доставки', 'WL_DELIVERY_ADDRESS', ['text', 80]],
        ]
    ]
];

if ($request->isPost() && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        __AdmSettingsSaveOptions($module_id, $aTab['OPTIONS']);
    }
    LocalRedirect($url);
}

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>
    <form method="post" enctype="multipart/form-data" action="<?= $url ?>">
        <?php echo bitrix_sessid_post(); ?>
        <?php foreach ($aTabs as $aTab) {
            $tabControl->beginNextTab();
            foreach ($aTab['OPTIONS'] as $code => $option) {

                if ($code === 'map') {
                    if (Option::get($module_id, 'yandex_api_key')) {
                        ?>
                        <tr>
                            <td colspan="2">
                                <div>
                                    <div class="map" id="ya_map" style="height: 600px"></div>
                                    <input type="hidden" name="yandex_coordinates" value="<?=Option::get($module_id, 'yandex_coordinates')?>"
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    __AdmSettingsDrawRow($module_id, $option);
                }
            }

        } ?>
        <?php $tabControl->Buttons(); ?>
        <input type="submit" name="apply" value="Сохранить" class="adm-btn-save">
    </form>
<?php $tabControl->End(); ?>