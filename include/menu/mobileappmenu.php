<? require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<? global $arBasketPrices; ?>
<div class="mobile-app-menu">
    <i class="icon icon-index"><a href="/offers/discount/"></a>Sale</i>
    <i class="icon icon-catalog"><a href="/catalog/"></a>Каталог</i>
    <i class="icon icon-basket"><a href="/basket/"></a><span class="count"><?= $arBasketPrices['BASKET_COUNT']?></span>Корзина</i>
    
    <? $APPLICATION->IncludeComponent(
        "wl:wishlist",
        "bottom_menu",
        [],
        false
    ); ?>
    
    <i class="icon icon-orders"><a href="/personal/orders/"></a>Заказы</i>
    <i class="icon icon-profile"><a href="/personal/"></a>Профиль</i>
</div>