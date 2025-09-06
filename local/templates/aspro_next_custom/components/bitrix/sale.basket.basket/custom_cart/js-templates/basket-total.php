<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
    <div class="checkout-card" data-entity="basket-checkout-aligner">

        <? if ($arParams['HIDE_COUPON'] !== 'Y') { ?>
        <div class="checkout-card_promo">
            <input type="text" class="form-control" placeholder="Промокод" data-entity="basket-coupon-input">
            <div class="basket-coupon-alert-section">
                <div class="basket-coupon-alert-inner">
                    {{#COUPON_LIST}}
                    <div class="basket-coupon-alert text-{{CLASS}}">
                        <span class="basket-coupon-text">
                            <strong>{{COUPON}}</strong> - <?=Loc::getMessage('SBB_COUPON')?> {{JS_CHECK_CODE}}
                        </span>
                        <span class="close-link" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}">
                            <?=Loc::getMessage('SBB_DELETE')?>
                        </span>
                    </div>
                    {{/COUPON_LIST}}
                </div>
            </div>
        </div>
        <? } ?>

        <div class="checkout-card_items-count">
            <span>{{{BASKET_ITEMS_COUNT}}} {{{GOODS_WORD}}}</span>
        </div>

        <div class="checkout-card_total">
            <div class="checkout-card_line total">
                <span>Итого</span>
                <span data-entity="basket-total-price">{{{PRICE_FORMATED}}}</span>
            </div>
        </div>

        <div class="checkout-card_bonus">
            <span>Будет начислено {{BONUS_POINTS}} баллов</span>
        </div>

        <div class="checkout-card_button-wrapper">
            <button class="btn btn-checkout{{#DISABLE_CHECKOUT}} disabled{{/DISABLE_CHECKOUT}}"
                    data-entity="basket-checkout-button">
                Перейти к оформлению
            </button>
        </div>

    </div>
</script>