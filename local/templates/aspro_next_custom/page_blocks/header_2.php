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
                   <!-- Блок каталога слева (теперь кастомная картинка-кнопка) -->
                    <div class="header-catalog-button-wrapper">
                        <a href="/catalog/" class="header-catalog-button" title="Перейти в каталог"></a>
                    </div>

                    <!-- Центральный блок с логотипом -->
                    <div class="logo-block" style="text-align: center; flex-grow: 1;"> <!-- flex-grow для занятия центрального места -->
                        <div class="logo<?= $logoClass ?>">
                            <?= CNext::ShowLogo(); ?>
                        </div>
                    </div>

                    <!-- Блок с иконками справа -->
                    <div class="icons-block" style="display: flex; align-items: center; justify-content: flex-end; min-width: 200px;"> <!-- Добавил min-width для симметрии -->
                         <!-- НАЧАЛО БЛОКА ВЫПАДАЮЩЕГО ПОИСКА -->
                    <div class="search-container-relative">
                        <div class="header-search-popup-wrapper">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:search.title",
                                "visual",
                                array(
                                    "NUM_CATEGORIES" => "1",
                                    "TOP_COUNT" => "5",
                                    "ORDER" => "date",
                                    "USE_LANGUAGE_GUESS" => "Y",
                                    "CHECK_DATES" => "Y",
                                    "SHOW_OTHERS" => "N",
                                    "PAGE" => SITE_DIR."catalog/",
                                    "SHOW_INPUT" => "Y",
                                    "INPUT_ID" => "title-search-input-popup",
                                    "CONTAINER_ID" => "title-search-popup",
                                    "CATEGORY_0_TITLE" => "Товары",
                                    "CATEGORY_0" => array(
                                        0 => "iblock_catalog",
                                    ),
                                    "CATEGORY_0_iblock_catalog" => array(
                                        0 => "all",
                                    ),
                                    "PRICE_CODE" => array( 0 => "BASE", ),
                                    "PRICE_VAT_INCLUDE" => "Y",
                                    "PREVIEW_TRUNCATE_LEN" => "",
                                    "SHOW_PREVIEW" => "Y",
                                    "CONVERT_CURRENCY" => "N",
                                ),
                                false
                            );?>
                        </div>

                        <!-- 2. ИКОНКА-ТРИГГЕР, которая включает поиск -->
                        <div class="pull-right block-link search-icon-container">
                            <div class="wrap_icon inner-table-block">
                               <div class="search-icon-wrapper" title="Поиск по сайту"></div>
                            </div>
                        </div>
                    </div> <!-- КОНЕЦ ОБЩЕГО КОНТЕЙНЕРА -->
                        <!-- КОНЕЦ НОВОГО БЛОКА -->
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

<script>
$(document).ready(function(){
    // Находим иконку-триггер и сам блок поиска
    var searchIcon = $('.search-icon-wrapper');
    var searchPopup = $('.header-search-popup-wrapper');
    
    // Клик по иконке поиска
    searchIcon.on('click', function(e) {
        e.stopPropagation(); // Останавливаем всплытие, чтобы клик по иконке не закрывал сразу же открытый блок
        
        searchPopup.toggleClass('show');
        
        // Если блок стал видимым, ставим фокус в поле ввода
        if (searchPopup.hasClass('show')) {
            searchPopup.find('#title-search-input-popup').focus();
        }
    });
    
    // Клик где угодно на документе
    $(document).on('click', function(e) {
        // Если поиск открыт и мы кликнули НЕ по нему и НЕ по его иконке-триггеру
        if (searchPopup.hasClass('show') && 
            !$(e.target).closest('.header-search-popup-wrapper').length && 
            !$(e.target).closest('.search-icon-wrapper').length) 
        {
            searchPopup.removeClass('show'); // Прячем поиск
        }
    });
});
</script>