
<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\UI\Extension::load("ui.fonts.ruble");

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $templateFolder
 * @var string $templateName
 * @var CMain $APPLICATION
 * @var CBitrixBasketComponent $component
 * @var CBitrixComponentTemplate $this
 * @var array $giftParameters
 */

$documentRoot = Main\Application::getDocumentRoot();

if (empty($arParams['TEMPLATE_THEME']))
{
	$arParams['TEMPLATE_THEME'] = Main\ModuleManager::isModuleInstalled('bitrix.eshop') ? 'site' : 'blue';
}

if ($arParams['TEMPLATE_THEME'] === 'site')
{
	$templateId = Main\Config\Option::get('main', 'wizard_template_id', 'eshop_bootstrap', $component->getSiteId());
	$templateId = preg_match('/^eshop_adapt/', $templateId) ? 'eshop_adapt' : $templateId;
	$arParams['TEMPLATE_THEME'] = Main\Config\Option::get('main', 'wizard_'.$templateId.'_theme_id', 'blue', $component->getSiteId());
}

if (!empty($arParams['TEMPLATE_THEME']))
{
	if (!is_file($documentRoot.'/bitrix/css/main/themes/'.$arParams['TEMPLATE_THEME'].'/style.css'))
	{
		$arParams['TEMPLATE_THEME'] = 'blue';
	}
}

if (!isset($arParams['DISPLAY_MODE']) || !in_array($arParams['DISPLAY_MODE'], array('extended', 'compact')))
{
	$arParams['DISPLAY_MODE'] = 'extended';
}

$arParams['USE_DYNAMIC_SCROLL'] = isset($arParams['USE_DYNAMIC_SCROLL']) && $arParams['USE_DYNAMIC_SCROLL'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_FILTER'] = isset($arParams['SHOW_FILTER']) && $arParams['SHOW_FILTER'] === 'N' ? 'N' : 'Y';

$arParams['PRICE_DISPLAY_MODE'] = isset($arParams['PRICE_DISPLAY_MODE']) && $arParams['PRICE_DISPLAY_MODE'] === 'N' ? 'N' : 'Y';

if (!isset($arParams['TOTAL_BLOCK_DISPLAY']) || !is_array($arParams['TOTAL_BLOCK_DISPLAY']))
{
	$arParams['TOTAL_BLOCK_DISPLAY'] = array('top');
}

if (empty($arParams['PRODUCT_BLOCKS_ORDER']))
{
	$arParams['PRODUCT_BLOCKS_ORDER'] = 'props,sku,columns';
}

if (is_string($arParams['PRODUCT_BLOCKS_ORDER']))
{
	$arParams['PRODUCT_BLOCKS_ORDER'] = explode(',', $arParams['PRODUCT_BLOCKS_ORDER']);
}

$arParams['USE_PRICE_ANIMATION'] = isset($arParams['USE_PRICE_ANIMATION']) && $arParams['USE_PRICE_ANIMATION'] === 'N' ? 'N' : 'Y';
$arParams['EMPTY_BASKET_HINT_PATH'] = isset($arParams['EMPTY_BASKET_HINT_PATH']) ? (string)$arParams['EMPTY_BASKET_HINT_PATH'] : '/';
$arParams['USE_ENHANCED_ECOMMERCE'] = isset($arParams['USE_ENHANCED_ECOMMERCE']) && $arParams['USE_ENHANCED_ECOMMERCE'] === 'Y' ? 'Y' : 'N';
$arParams['DATA_LAYER_NAME'] = isset($arParams['DATA_LAYER_NAME']) ? trim($arParams['DATA_LAYER_NAME']) : 'dataLayer';
$arParams['BRAND_PROPERTY'] = isset($arParams['BRAND_PROPERTY']) ? trim($arParams['BRAND_PROPERTY']) : '';

if ($arParams['USE_GIFTS'] === 'Y')
{
	$arParams['GIFTS_BLOCK_TITLE'] = isset($arParams['GIFTS_BLOCK_TITLE']) ? trim((string)$arParams['GIFTS_BLOCK_TITLE']) : Loc::getMessage('SBB_GIFTS_BLOCK_TITLE');

	CBitrixComponent::includeComponentClass('bitrix:sale.products.gift.basket');

	$giftParameters = array(
		'SHOW_PRICE_COUNT' => 1,
		'PRODUCT_SUBSCRIPTION' => 'N',
		'PRODUCT_ID_VARIABLE' => 'id',
		'USE_PRODUCT_QUANTITY' => 'N',
		'ACTION_VARIABLE' => 'actionGift',
		'ADD_PROPERTIES_TO_BASKET' => 'Y',
		'PARTIAL_PRODUCT_PROPERTIES' => 'Y',

		'BASKET_URL' => $APPLICATION->GetCurPage(),
		'APPLIED_DISCOUNT_LIST' => $arResult['APPLIED_DISCOUNT_LIST'],
		'FULL_DISCOUNT_LIST' => $arResult['FULL_DISCOUNT_LIST'],

		'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
		'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_SHOW_VALUE'],
		'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],

		'BLOCK_TITLE' => $arParams['GIFTS_BLOCK_TITLE'],
		'HIDE_BLOCK_TITLE' => $arParams['GIFTS_HIDE_BLOCK_TITLE'],
		'TEXT_LABEL_GIFT' => $arParams['GIFTS_TEXT_LABEL_GIFT'],

		'DETAIL_URL' => isset($arParams['GIFTS_DETAIL_URL']) ? $arParams['GIFTS_DETAIL_URL'] : null,
		'PRODUCT_QUANTITY_VARIABLE' => $arParams['GIFTS_PRODUCT_QUANTITY_VARIABLE'],
		'PRODUCT_PROPS_VARIABLE' => $arParams['GIFTS_PRODUCT_PROPS_VARIABLE'],
		'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
		'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
		'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
		'MESS_BTN_BUY' => $arParams['GIFTS_MESS_BTN_BUY'],
		'MESS_BTN_DETAIL' => $arParams['GIFTS_MESS_BTN_DETAIL'],
		'CONVERT_CURRENCY' => $arParams['GIFTS_CONVERT_CURRENCY'],
		'HIDE_NOT_AVAILABLE' => $arParams['GIFTS_HIDE_NOT_AVAILABLE'],

		'PRODUCT_ROW_VARIANTS' => '',
		'PAGE_ELEMENT_COUNT' => 0,
		'DEFERRED_PRODUCT_ROW_VARIANTS' => \Bitrix\Main\Web\Json::encode(
			SaleProductsGiftBasketComponent::predictRowVariants(
				$arParams['GIFTS_PAGE_ELEMENT_COUNT'],
				$arParams['GIFTS_PAGE_ELEMENT_COUNT']
			)
		),
		'DEFERRED_PAGE_ELEMENT_COUNT' => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],

		'ADD_TO_BASKET_ACTION' => 'BUY',
		'PRODUCT_DISPLAY_MODE' => 'Y',
		'PRODUCT_BLOCKS_ORDER' => isset($arParams['GIFTS_PRODUCT_BLOCKS_ORDER']) ? $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'] : '',
		'SHOW_SLIDER' => isset($arParams['GIFTS_SHOW_SLIDER']) ? $arParams['GIFTS_SHOW_SLIDER'] : '',
		'SLIDER_INTERVAL' => isset($arParams['GIFTS_SLIDER_INTERVAL']) ? $arParams['GIFTS_SLIDER_INTERVAL'] : '',
		'SLIDER_PROGRESS' => isset($arParams['GIFTS_SLIDER_PROGRESS']) ? $arParams['GIFTS_SLIDER_PROGRESS'] : '',
		'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],

		'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
		'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
		'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY']
	);
}

\CJSCore::Init(array('fx', 'popup', 'ajax'));

$this->addExternalCss('/bitrix/css/main/bootstrap.css');
$this->addExternalCss($templateFolder.'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css');

$this->addExternalJs($templateFolder.'/js/mustache.js');
$this->addExternalJs($templateFolder.'/js/action-pool.js');
$this->addExternalJs($templateFolder.'/js/filter.js');
$this->addExternalJs($templateFolder.'/js/component.js');

$mobileColumns = isset($arParams['COLUMNS_LIST_MOBILE'])
	? $arParams['COLUMNS_LIST_MOBILE']
	: $arParams['COLUMNS_LIST'];
$mobileColumns = array_fill_keys($mobileColumns, true);

$jsTemplates = new Main\IO\Directory($documentRoot.$templateFolder.'/js-templates');
/** @var Main\IO\File $jsTemplate */
foreach ($jsTemplates->getChildren() as $jsTemplate)
{
	include($jsTemplate->getPath());
}

$displayModeClass = $arParams['DISPLAY_MODE'] === 'compact' ? ' basket-items-list-wrapper-compact' : '';

if (empty($arResult['ERROR_MESSAGE']))
{
	if ($arParams['USE_GIFTS'] === 'Y' && $arParams['GIFTS_PLACE'] === 'TOP')
	{
		?>
		<div data-entity="parent-container">
			<div class="catalog-block-header"
					data-entity="header"
					data-showed="false"
					style="display: none; opacity: 0;">
				<?=$arParams['GIFTS_BLOCK_TITLE']?>
			</div>
			<?
			$APPLICATION->IncludeComponent(
				'bitrix:sale.products.gift.basket',
				'.default',
				$giftParameters,
				$component
			);
			?>
		</div>
		<?
	}

	if ($arResult['BASKET_ITEM_MAX_COUNT_EXCEEDED'])
	{
		?>
		<div id="basket-item-message">
			<?=Loc::getMessage('SBB_BASKET_ITEM_MAX_COUNT_EXCEEDED', array('#PATH#' => $arParams['PATH_TO_BASKET']))?>
		</div>
		<?
	}
	?>
	<div id="basket-root" class="bx-basket bx-<?=$arParams['TEMPLATE_THEME']?> bx-step-opacity" style="opacity: 0;">

    <div class="row">
        <div class="col-xs-9" style="width: 85%;">
			<?
			// --- НАЧАЛО НОВОГО БЛОКА ---

			// Получаем количество товаров
			$goodsCount = count($arResult['GRID']['ROWS']);

			// Функция для правильного склонения слова "товар"
			function getGoodsMessage($count)
			{
				$count = $count % 100;
				if ($count >= 11 && $count <= 19) {
					return 'товаров';
				}
				$lastDigit = $count % 10;
				if ($lastDigit == 1) {
					return 'товар';
				}
				if ($lastDigit >= 2 && $lastDigit <= 4) {
					return 'товара';
				}
				return 'товаров';
			}

			$goodsMessage = getGoodsMessage($goodsCount);
			?>
			<div class="basket-main-header">
				<h2 class="basket-main-header__title">Корзина</h2>
				<? if ($goodsCount > 0): ?>
					<span class="basket-main-header__count">
						<?= $goodsCount ?> <?= $goodsMessage ?>
					</span>
				<? endif; ?>
			</div>

			<? // --- КОНЕЦ НОВОГО БЛОКА --- ?>

            <div id="basket-items-list-wrapper">
                <div class="basket-items-list-container">
                    <div class="basket-items-list" id="basket-item-list">
                        <table class="basket-items-list-table" id="basket-item-table">
                            </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3" style="width: 15%;"> 
			<div data-entity="basket-total-block">
            </div>

			<? $APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR . "include/basket_bottom_description.php",
							"EDIT_TEMPLATE" => "include_area.php"
						)
					); ?>	
    	</div>
	</div>
	</div>
	<?
	$basketCommentFieldIsActive = \Bitrix\Main\Config\Option::get('wl.snailshop', 'basket_comment_is_active');
	?>
	<? if($basketCommentFieldIsActive == "Y") { ?>
		<div>
			<div class="form-group">
				<label for="santa-request">«А у &#127877;Деда Мороза я попрошу…»<br><small><a style="font-weight: 400; text-decoration: underline;" href="/promo/pismo_dedu_morozu/" target="_blank">Условия акции</a></small></label>
				<textarea id="santa-request"></textarea>
			</div>
		</div>
	<? } ?>

	<?
	if (!empty($arResult['CURRENCIES']) && Main\Loader::includeModule('currency'))
	{
		CJSCore::Init('currency');

		?>
		<script>
			BX.Currency.setCurrencies(<?=CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true)?>);
		</script>
		<?
	}

	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$signedTemplate = $signer->sign($templateName, 'sale.basket.basket');
	$signedParams = $signer->sign(base64_encode(serialize($arParams)), 'sale.basket.basket');
	$messages = Loc::loadLanguageFile(__FILE__);
	?>
	<script>
		BX.message(<?=CUtil::PhpToJSObject($messages)?>);
		BX.Sale.BasketComponent.init({
			result: <?=CUtil::PhpToJSObject($arResult, false, false, true)?>,
			params: <?=CUtil::PhpToJSObject($arParams)?>,
			template: '<?=CUtil::JSEscape($signedTemplate)?>',
			signedParamsString: '<?=CUtil::JSEscape($signedParams)?>',
			siteId: '<?=CUtil::JSEscape($component->getSiteId())?>',
			siteTemplateId: '<?=CUtil::JSEscape($component->getSiteTemplateId())?>',
			templateFolder: '<?=CUtil::JSEscape($templateFolder)?>'
		});
	</script>
	<?
	if ($arParams['USE_GIFTS'] === 'Y' && $arParams['GIFTS_PLACE'] === 'BOTTOM')
	{
		?>
		<div data-entity="parent-container">
			<div class="catalog-block-header"
					data-entity="header"
					data-showed="false"
					style="display: none; opacity: 0;">
				<?=$arParams['GIFTS_BLOCK_TITLE']?>
			</div>
			<?
			$APPLICATION->IncludeComponent(
				'bitrix:sale.products.gift.basket',
				'.default',
				$giftParameters,
				$component
			);
			?>
		</div>
		<?
	}
}
elseif ($arResult['EMPTY_BASKET'])
{
	include(Main\Application::getDocumentRoot().$templateFolder.'/empty.php');
}
else
{
	ShowError($arResult['ERROR_MESSAGE']);
}?>

<? //custom
$curPage = $APPLICATION->GetCurPage().'?'.$arParams["ACTION_VARIABLE"].'=';
$arUrls = array(
    "delete" => $curPage."delete&id=#ID#",
    "delay" => $curPage."delay&id=#ID#",
    "add" => $curPage."add&id=#ID#",
);
unset($curPage);
 
$arBasketJSParams = array(
    'SALE_DELETE' => GetMessage("SALE_DELETE"),
    'SALE_DELAY' => GetMessage("SALE_DELAY"),
    'SALE_TYPE' => GetMessage("SALE_TYPE"),
    'TEMPLATE_FOLDER' => $templateFolder,
    'DELETE_URL' => $arUrls["delete"],
    'DELAY_URL' => $arUrls["delay"],
    'ADD_URL' => $arUrls["add"]
);
?>
<script type="text/javascript">
    var basketJSParams = <?=CUtil::PhpToJSObject($arBasketJSParams);?>
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var totalBlock = document.querySelector('[data-entity="basket-total-block"]');
            var promoSection = totalBlock ? totalBlock.querySelector('.basket-coupon-section') : null;
            var promoContainer = document.getElementById('promo-code-container-custom');

            if (promoSection && promoContainer) {
                promoContainer.appendChild(promoSection);
            }
        }, 500);
        
        // Инициализация состояния избранного в корзине
        function updateWishlistButtons() {
            console.log('Проверяем кнопки избранного в корзине...');
            
            // Получаем все кнопки избранного в корзине
            var wishlistButtons = document.querySelectorAll('.basket-item-wishlist-buttons .wish_item');
            console.log('Найдено кнопок избранного:', wishlistButtons.length);
            
            var productIds = [];
            
            // Собираем ID товаров из кнопок избранного
            wishlistButtons.forEach(function(button) {
                var productId = button.getAttribute('data-item');
                var basketItemId = button.getAttribute('data-basket-id');
                console.log('Найден товар с ID:', productId, 'Basket ID:', basketItemId);
                
                // Для товаров с SKU нужно получить ID основного товара
                if (productId && basketItemId && window.BX && window.BX.Sale && window.BX.Sale.BasketComponent) {
                    var basketComponent = window.BX.Sale.BasketComponent;
                    if (basketComponent.items && basketComponent.items[basketItemId]) {
                        var itemData = basketComponent.items[basketItemId];
                        console.log('Данные товара:', itemData);
                        
                        // Для товаров с SKU используем PRODUCT_ID (основной товар)
                        // OFFER_ID - это ID предложения, а нам нужен основной товар
                        if (itemData.PRODUCT_ID) {
                            productId = itemData.PRODUCT_ID;
                            console.log('Используем PRODUCT_ID (основной товар):', productId);
                        } else if (itemData.OFFER_ID) {
                            // Если нет PRODUCT_ID, используем OFFER_ID
                            productId = itemData.OFFER_ID;
                            console.log('Используем OFFER_ID:', productId);
                        }
                    }
                }
                
                if (productId && productIds.indexOf(productId) === -1) {
                    productIds.push(productId);
                }
            });
            
            console.log('Список ID товаров для проверки:', productIds);
            
            if (productIds.length > 0) {
                // AJAX запрос для получения списка избранных товаров
                fetch('/ajax/wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=check&component=wishlist&mode=ajax&ids=' + productIds.join(',')
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.wishlist_ids) {
                        console.log('Найдены товары в избранном:', data.wishlist_ids);
                        // Обновляем состояние кнопок
                        data.wishlist_ids.forEach(function(productId) {
                            var toButton = document.querySelector('.basket-item-wishlist-buttons .wish_item.to[data-item="' + productId + '"]');
                            var inButton = document.querySelector('.basket-item-wishlist-buttons .wish_item.in[data-item="' + productId + '"]');
                            
                            if (toButton && inButton) {
                                console.log('Обновляем состояние кнопок для товара:', productId);
                                toButton.style.display = 'none';
                                inButton.style.display = 'flex';
                            } else {
                                console.log('Кнопки не найдены для товара:', productId);
                            }
                        });
                    } else {
                        console.log('Нет товаров в избранном или ошибка:', data);
                    }
                })
                .catch(error => {
                    console.error('Ошибка при загрузке избранного:', error);
                });
            }
        }
        
        // Обновляем состояние при загрузке страницы
        setTimeout(updateWishlistButtons, 1000);
        setTimeout(updateWishlistButtons, 2000); // Дополнительная проверка через 2 секунды
        setTimeout(updateWishlistButtons, 3000); // Еще одна проверка через 3 секунды
        
        // Обновляем состояние при изменении корзины
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    setTimeout(updateWishlistButtons, 500);
                }
            });
        });
        
        var basketContainer = document.getElementById('basket-item-list');
        if (basketContainer) {
            observer.observe(basketContainer, {
                childList: true,
                subtree: true
            });
        }
        
        // Дополнительная проверка при изменении видимости корзины
        var basketRoot = document.getElementById('basket-root');
        if (basketRoot) {
            var basketObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                        var opacity = basketRoot.style.opacity;
                        if (opacity === '1' || opacity === '') {
                            setTimeout(updateWishlistButtons, 100);
                        }
                    }
                });
            });
            basketObserver.observe(basketRoot, {
                attributes: true,
                attributeFilter: ['style']
            });
        }
    });
</script>