<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
    <div class="checkout-card" data-entity="basket-checkout-aligner">

        <div class="checkout-card_header">
            <span>Ваш заказ</span>
        </div>

        <div class="checkout-card_details">
            <div class="checkout-card_line">
                <span>Товары, шт</span>
                <span>{{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}</span>
            </div>

            {{#DISCOUNT_PRICE_FORMATED}}
            <div class="checkout-card_line discount">
                <span>Скидка</span>
                <span>- {{{DISCOUNT_PRICE_FORMATED}}}</span>
            </div>
            {{/DISCOUNT_PRICE_FORMATED}}
            
            <div class="checkout-card_line delivery">
                <span>Доставка</span>
                <span>Рассчитывается далее</span>
            </div>
        </div>

        <? if ($arParams['HIDE_COUPON'] !== 'Y') { ?>
        <div class="checkout-card_promo">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Введите промокод" data-entity="basket-coupon-input">
                <button class="btn btn-promo" data-entity="basket-coupon-apply-button">Применить</button>
            </div>
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

        <div class="checkout-card_total">
            <div class="checkout-card_line total">
                <span>Итого</span>
                <span data-entity="basket-total-price">{{{PRICE_FORMATED}}}</span>
            </div>
        </div>
        <div class="checkout-card_button-wrapper">
            <button class="btn btn-checkout{{#DISABLE_CHECKOUT}} disabled{{/DISABLE_CHECKOUT}}"
                    data-entity="basket-checkout-button">
                Оформить заказ
            </button>
        </div>

    </div>
</script>