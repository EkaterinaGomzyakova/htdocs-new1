<?
global $arTheme, $arRegion;
$logoClass = ($arTheme['COLORED_LOGO']['VALUE'] !== 'Y' ? '' : ' colored');
global $USER;

$arBackParametrs = CNext::GetBackParametrsValues(SITE_ID);
$phone = $arBackParametrs['HEADER_PHONES_array_PHONE_VALUE_0'];
$href = 'tel:'.str_replace(array(' ', '-', '(', ')'), '', $phone);
?>
<div class="mobileheader-v1">
    <div class="burger pull-left">
        <?= CNext::showIconSvg("burger dark", SITE_TEMPLATE_PATH . "/images/svg/Burger_big_white.svg"); ?>
        <?= CNext::showIconSvg("close dark", SITE_TEMPLATE_PATH . "/images/svg/Close.svg"); ?>
    </div>
    <div class="logo-block pull-left">
        <div class="logo<?= $logoClass ?>">
            <?= CNext::ShowLogo(); ?>
        </div>
    </div>
    <div class="right-icons pull-right">
        <div class="pull-right <? if ($USER->IsAuthorized()) {?>hidden-xs<?}?>">
            <div class="wrap_icon">
                <button class="top-btn inline-search-show twosmallfont">
                    <?= CNext::showIconSvg("search big", SITE_TEMPLATE_PATH . "/images/svg/Search_big_black.svg"); ?>
                </button>
            </div>
        </div>
        <div class="pull-right">
            <div class="wrap_icon wrap_basket">
                <?= CNext::ShowBasketWithCompareLink('', 'big', false, false, true); ?>
            </div>
        </div>
        <? if (!$USER->IsAuthorized()){ ?>
            <div class="pull-right">
                <div class="wrap_icon wrap_cabinet">
                    <?= CNext::showCabinetLink(true, false, 'big'); ?>
                </div>
            </div>
        <? } ?>
        <div class="pull-right">
            <? if ($USER->IsAuthorized()) {
                ?>
                <div class="wrap_icon wrap_cabinet wrap_icon--favorites">
                    <? $APPLICATION->IncludeComponent('wl:wishlist', '', []) ?>
                </div>
            <? } ?>
            <div class="wrap_icon wrap_cabinet wrap_icon--phone">
                <a rel="nofollow" href="<?= $href ?>"><img
                            class="wrap_icon__phone icon-phone"
                            data-toggle="tooltip"
                            data-placement="bottom"
                            title=""
                            data-original-title="<?= $phone ?>" src="/local/templates/aspro_next_custom/images/svg/Phone_black.svg"></a>
            </div>
        </div>
        <?php
        if ($USER->IsAuthorized()): ?>
            <div class="pull-right">
                <div class="wrap_icon">
                    <? $APPLICATION->IncludeComponent("bitrix:sale.personal.account", "header", array(), false); ?>
                </div>
            </div>
        <? endif; ?>
    </div>

</div>