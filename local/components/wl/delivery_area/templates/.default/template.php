<? use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if ($arParams['SHOW_TEMPLATE']) {
    ?>
    <a href="#"
       class="btn btn-default btn-md"
       id="wl_delivery_show_map_btn"
       onclick="wlAreaDelivery.showMap({
               area: <?= Option::get('wl.delivery_area', 'yandex_coordinates') ?>
               }); return false;"
    >Указать адрес доставки</a>
    <div class="selected-address" id="wl_delivery_area_selected_text">
        <input type="hidden" id="wl_delivery_area_zone" name="ORDER_PROP_<?= $arParams['PROPERTY_DELIVERY_ZONE']['ID'] ?>"
               value="<?= $arParams['PROPERTY_DELIVERY_ZONE']['VALUE'][0] ?>">
        <input type="hidden" id="wl_delivery_area_address" name="ORDER_PROP_<?= $arParams['PROPERTY_DELIVERY_ADDRESS']['ID'] ?>"
               value="<?= $arParams['PROPERTY_DELIVERY_ADDRESS']['VALUE'][0] ?>">
        <p class="wl-delivery-selected-address">Адрес доставки: <span
                    id="wl_delivery_area_address_display"><?= $arParams['PROPERTY_DELIVERY_ADDRESS']['VALUE'][0] ?></span></p>
    </div>
    <?php
}