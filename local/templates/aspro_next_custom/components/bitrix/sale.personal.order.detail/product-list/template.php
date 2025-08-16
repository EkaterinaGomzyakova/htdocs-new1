<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Page\Asset;

if ($arParams['GUEST_MODE'] !== 'Y') {
	Asset::getInstance()->addJs("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/script.js");
	Asset::getInstance()->addCss("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/style.css");
}
$this->addExternalCss("/bitrix/css/main/bootstrap.css");

CJSCore::Init(array('clipboard', 'fx'));

$APPLICATION->SetTitle("");

if (!empty($arResult['ERRORS']['FATAL'])) {
	foreach ($arResult['ERRORS']['FATAL'] as $error) {
		ShowError($error);
	}

	$component = $this->__component;

	if ($arParams['AUTH_FORM_IN_TEMPLATE'] && isset($arResult['ERRORS']['FATAL'][$component::E_NOT_AUTHORIZED])) {
		$APPLICATION->AuthForm('', false, false, 'N', false);
	}
} else {
	if (!empty($arResult['ERRORS']['NONFATAL'])) {
		foreach ($arResult['ERRORS']['NONFATAL'] as $error) {
			ShowError($error);
		}
	}
?>
	<h3>Товары в заказе</h3>
	<div class="row sale-order-detail-payment-options-order-content">
		<div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-order-content-container">
			<div class="row">
				<div class="sale-order-detail-order-section bx-active">
					<div class="sale-order-detail-order-section-content container-fluid">
						<div class="sale-order-detail-order-table-fade sale-order-detail-order-table-fade-right">
							<div style="width: 100%; overflow-x: auto; overflow-y: hidden;">
								<div class="sale-order-detail-order-item-table">
									<?
									foreach ($arResult['BASKET'] as $basketItem) {
									?>
										<div class="sale-order-detail-order-item-tr sale-order-detail-order-basket-info sale-order-detail-order-item-tr-first">
											<div class="sale-order-detail-order-item-td" style="min-width: 300px;">
												<div class="sale-order-detail-order-item-block">
													<div class="sale-order-detail-order-item-img-block">
														<a href="<?= $basketItem['DETAIL_PAGE_URL'] ?>">
															<?
															if ($basketItem['PICTURE']['SRC'] <> '') {
																$imageSrc = $basketItem['PICTURE']['SRC'];
															} else {
																$imageSrc = $this->GetFolder() . '/images/no_photo.png';
															}
															?>
															<div class="sale-order-detail-order-item-imgcontainer" style="background-image: url(<?= $imageSrc ?>);
																		background-image: -webkit-image-set(url(<?= $imageSrc ?>) 1x,
																		url(<?= $imageSrc ?>) 2x)">
															</div>
														</a>
													</div>
													<div class="sale-order-detail-order-item-content">
														<div class="sale-order-detail-order-item-title">
															<a href="<?= $basketItem['DETAIL_PAGE_URL'] ?>">
																<?= htmlspecialcharsbx($basketItem['NAME']) ?>
															</a>
														</div>
														<?
														if (isset($basketItem['PROPS']) && is_array($basketItem['PROPS'])) {
															foreach ($basketItem['PROPS'] as $itemProps) {
														?>
																<div class="sale-order-detail-order-item-color">
																	<span class="sale-order-detail-order-item-color-name">
																		<?= htmlspecialcharsbx($itemProps['NAME']) ?>:</span>
																	<span class="sale-order-detail-order-item-color-type">
																		<?= htmlspecialcharsbx($itemProps['VALUE']) ?></span>
																</div>
														<?
															}
														}
														?>
													</div>
												</div>
											</div>
											<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
												<div class="sale-order-detail-order-item-td-title col-xs-7 col-sm-5 visible-xs visible-sm">
													<?= Loc::getMessage('SPOD_PRICE') ?>
												</div>
												<div class="sale-order-detail-order-item-td-text">
													<strong class="bx-price"><?= $basketItem['BASE_PRICE_FORMATED'] ?></strong>
												</div>
											</div>
											<?
											if ($basketItem["DISCOUNT_PRICE_PERCENT_FORMATED"] <> '') {
											?>
												<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
													<div class="sale-order-detail-order-item-td-title col-xs-7 col-sm-5 visible-xs visible-sm">
														<?= Loc::getMessage('SPOD_DISCOUNT') ?>
													</div>
													<div class="sale-order-detail-order-item-td-text">
														<strong class="bx-price"><?= $basketItem['DISCOUNT_PRICE_PERCENT_FORMATED'] ?></strong>
													</div>
												</div>
											<?
											} elseif (mb_strlen($arResult["SHOW_DISCOUNT_TAB"])) {
											?>
												<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
													<div class="sale-order-detail-order-item-td-title col-xs-7 col-sm-5 visible-xs visible-sm">
														<?= Loc::getMessage('SPOD_DISCOUNT') ?>
													</div>
													<div class="sale-order-detail-order-item-td-text">
														<strong class="bx-price"></strong>
													</div>
												</div>
											<?
											}
											?>
											<div class="sale-order-detail-order-item-nth-4p1"></div>
											<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
												<div class="sale-order-detail-order-item-td-title col-xs-7 col-sm-5 visible-xs visible-sm">
													<?= Loc::getMessage('SPOD_QUANTITY') ?>
												</div>
												<div class="sale-order-detail-order-item-td-text">
													<span><?= $basketItem['QUANTITY'] ?>&nbsp;
														<?
														if ($basketItem['MEASURE_NAME'] <> '') {
															echo htmlspecialcharsbx($basketItem['MEASURE_NAME']);
														} else {
															echo Loc::getMessage('SPOD_DEFAULT_MEASURE');
														}
														?></span>
												</div>
											</div>
											<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
												<div class="sale-order-detail-order-item-td-title col-xs-7 col-sm-5 visible-xs visible-sm"><?= Loc::getMessage('SPOD_ORDER_PRICE') ?></div>
												<div class="sale-order-detail-order-item-td-text">
													<strong class="bx-price all"><?= $basketItem['FORMATED_SUM'] ?></strong>
												</div>
											</div>
										</div>
									<?
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<? if (!empty($arResult['ROULETE_PRIZES'])) { ?>
		<h3><?= Loc::getMessage('ROULETE_PRIZES'); ?></h3>
		<div class="row roulete-prizes">
			<? foreach ($arResult['ROULETE_PRIZES'] as $arPrize) { ?>
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 roulete-prizes__item">
					<div class="col-xs-3">
						<img style="max-width: 100%;" class="sale-order-detail-order-item-block" src="<?= CFile::GetPath($arPrize['PREVIEW_PICTURE']) ?>" />
					</div>
					<div class="col-xs-9">
						<div class="sale-order-detail-order-item-title"><?= $arPrize['NAME'] ?></div>
					</div>
				</div>
			<? } ?>
		</div>
	<? } ?>

	<div class="row sale-order-detail-total-payment">
		<div class="col-md-7 col-md-offset-5 col-sm-12 col-xs-12 sale-order-detail-total-payment-container">
			<div class="row">
				<ul class="col-md-8 col-sm-6 col-xs-6 sale-order-detail-total-payment-list-left">
					<?
					if (floatval($arResult["ORDER_WEIGHT"])) {
					?>
						<li class="sale-order-detail-total-payment-list-left-item">
							<?= Loc::getMessage('SPOD_TOTAL_WEIGHT') ?>:
						</li>
					<?
					}

					if ($arResult['PRODUCT_SUM_FORMATED'] != $arResult['PRICE_FORMATED'] && !empty($arResult['PRODUCT_SUM_FORMATED'])) {
					?>
						<li class="sale-order-detail-total-payment-list-left-item">
							<?= Loc::getMessage('SPOD_COMMON_SUM') ?>:
						</li>
					<?
					}

					if ($arResult["PRICE_DELIVERY_FORMATED"] <> '') {
					?>
						<li class="sale-order-detail-total-payment-list-left-item">
							<?= Loc::getMessage('SPOD_DELIVERY') ?>:
						</li>
					<?
					}

					if ((float)$arResult["TAX_VALUE"] > 0) {
					?>
						<li class="sale-order-detail-total-payment-list-left-item">
							<?= Loc::getMessage('SPOD_TAX') ?>:
						</li>
					<?
					}
					?>
					<li class="sale-order-detail-total-payment-list-left-item"><?= Loc::getMessage('SPOD_SUMMARY') ?>:</li>
				</ul>
				<ul class="col-md-4 col-sm-6 col-xs-6 sale-order-detail-total-payment-list-right">
					<?
					if (floatval($arResult["ORDER_WEIGHT"])) {
					?>
						<li class="sale-order-detail-total-payment-list-right-item"><?= $arResult['ORDER_WEIGHT_FORMATED'] ?></li>
					<?
					}

					if ($arResult['PRODUCT_SUM_FORMATED'] != $arResult['PRICE_FORMATED'] && !empty($arResult['PRODUCT_SUM_FORMATED'])) {
					?>
						<li class="sale-order-detail-total-payment-list-right-item"><?= $arResult['PRODUCT_SUM_FORMATED'] ?></li>
					<?
					}

					if ($arResult["PRICE_DELIVERY_FORMATED"] <> '') {
					?>
						<li class="sale-order-detail-total-payment-list-right-item"><?= $arResult["PRICE_DELIVERY_FORMATED"] ?></li>
					<?
					}

					if ((float)$arResult["TAX_VALUE"] > 0) {
					?>
						<li class="sale-order-detail-total-payment-list-right-item"><?= $arResult["TAX_VALUE_FORMATED"] ?></li>
					<?
					}
					?>
					<li class="sale-order-detail-total-payment-list-right-item"><?= $arResult['PRICE_FORMATED'] ?></li>
				</ul>
			</div>
		</div>
	</div>
<?
}
?>