<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
global $USER;
global $arTheme, $arRegion;
$arRegions = CNextRegionality::getRegions();
if ($arRegion)
    $bPhone = ($arRegion['PHONES'] ? true : false);
else
    $bPhone = ((int)$arTheme['HEADER_PHONES'] ? true : false);
$logoClass = ($arTheme['COLORED_LOGO']['VALUE'] !== 'Y' ? '' : ' colored');
?>
<div class="top-block top-block-v1">
    <div class="maxwidth-theme">
        <div class="row">
            <div class="pull-left">
                <? $APPLICATION->IncludeComponent(
                    "aspro:social.info.next",
                    "top",
                    array(
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "3600000",
                        "CACHE_GROUPS" => "N",
                        "COMPONENT_TEMPLATE" => "top"
                    ),
                    false
                ); ?>
            </div>
            <div class="col-md-6">
            <? include($_SERVER['DOCUMENT_ROOT'] .  "/include/menu/menu.topest.php"); ?>
            </div>
            <div class="top-block-item pull-right show-fixed top-ctrl">
                <div class="personal_wrap">
                    <div class="personal top login twosmallfont <?if(!$USER->isAuthorized()){?>unauthorized<?}?>">
                        <?= CNext::ShowCabinetLink(true, true); ?>
                    </div>
                </div>
            </div>
            <? if ($arTheme['ORDER_BASKET_VIEW']['VALUE'] == 'NORMAL'): ?>
                <div class="top-block-item pull-right">
                    <div class="phone-block">
                        <? if ($bPhone): ?>
                            <div class="inline-block">
                                <? CNext::ShowHeaderPhones(); ?>
                            </div>
                        <? endif ?>
                        <? if ($arTheme['SHOW_CALLBACK']['VALUE'] == 'Y'): ?>
                            <div class="inline-block">
                                <span class="callback-block animate-load twosmallfont colored" data-event="jqm" data-param-form_id="CALLBACK"
                                      data-name="callback"><?= GetMessage("CALLBACK") ?></span>
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            <? endif; ?>
            <div class="top-block-item pull-right show-fixed">
                <div class="personal_wrap">
                    <div class="personal top login twosmallfont">
                        <? if ($USER->IsAuthorized()): ?>
                            <? $APPLICATION->IncludeComponent("bitrix:sale.personal.account", "header", array(), false); ?>
                        <? endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="header-v3 header-wrapper">
    <div class="logo_and_menu-row">
        <div class="logo-row">
            <div class="maxwidth-theme">
                <div class="row">

                    <!-- Блок каталога слева -->
                    <div class="catalog-menu-left" style="min-width: 100px;"> <!-- Добавил min-width для стабильности -->
                        <nav class="mega-menu sliced">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:menu",
                                "top_catalog_wide", // Шаблон для кнопки "Каталог" с выпадающим меню
                                array(
                                    "ROOT_MENU_TYPE" => "top_catalog",
                                    "MENU_CACHE_TYPE" => "A",
                                    "MENU_CACHE_TIME" => "36000000",
                                    "MENU_CACHE_USE_GROUPS" => "Y",
                                    "MENU_CACHE_GET_VARS" => array(),
                                    "MAX_LEVEL" => "2",
                                    "CHILD_MENU_TYPE" => "left",
                                    "USE_EXT" => "Y",
                                    "DELAY" => "N",
                                    "ALLOW_MULTI_SELECT" => "N",
                                    "COMPONENT_TEMPLATE" => "top_catalog_wide"
                                ),
                                false
                            );?>
                        </nav>
                    </div>

                    <!-- Центральный блок с логотипом -->
                    <div class="logo-block" style="text-align: center; flex-grow: 1;"> <!-- flex-grow для занятия центрального места -->
                        <div class="logo<?= $logoClass ?>">
                            <?= CNext::ShowLogo(); ?>
                        </div>
                    </div>

                    <!-- Блок с иконками справа -->
                    <div class="icons-block" style="display: flex; align-items: center; justify-content: flex-end; min-width: 200px;"> <!-- Добавил min-width для симметрии -->
                        <? if ($arTheme['ORDER_BASKET_VIEW']['VALUE'] !== 'NORMAL'): ?>
                            <div class="pull-right block-link">
                                <div class="phone-block with_btn">
                                    <? if ($bPhone): ?>
                                        <div class="inner-table-block">
                                            <? CNext::ShowHeaderPhones(); ?>
                                            <div class="schedule">
                                                <? include($_SERVER['DOCUMENT_ROOT'] . "/include/header-schedule.php"); ?>
                                            </div>
                                        </div>
                                    <? endif ?>
                                    <? if ($arTheme['SHOW_CALLBACK']['VALUE'] == 'Y'): ?>
                                        <div class="inner-table-block">
                                            <span class="callback-block animate-load twosmallfont colored white btn-default btn" data-event="jqm" data-param-form_id="CALLBACK" data-name="callback"><?= GetMessage("CALLBACK") ?></span>
                                        </div>
                                    <? endif; ?>
                                </div>
                            </div>
                        <? endif; ?>

                        <div class="pull-right block-link">
                            <div class="wrap_icon inner-table-block big-padding">
                                <?= CNext::ShowCabinetLink(true, true); ?>
                            </div>
                        </div>

                        <div class="pull-right block-link">
                            <div class="wrap_icon inner-table-block baskets big-padding">
                                <? if ($USER->IsAuthorized()): ?>
                                    <? $APPLICATION->IncludeComponent('wl:wishlist', '', []) ?>
                                <? endif; ?>
                            </div>
                            <?= CNext::ShowBasketWithCompareLink('with_price', 'big', true, 'wrap_icon inner-table-block baskets big-padding'); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div><? // class=logo-row?>
    </div>
    <div class="menu-row middle-block bg<?= strtolower($arTheme["MENU_COLOR"]["VALUE"]); ?>">
        <div class="maxwidth-theme">
            <div class="row">
                <div class="col-md-12">
                    <div class="menu-only">
                        <nav class="mega-menu sliced">
                        <? include($_SERVER['DOCUMENT_ROOT'] . "/include/menu/menu.top_catalog_wide.php"); ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="line-row visible-xs"></div>
</div>