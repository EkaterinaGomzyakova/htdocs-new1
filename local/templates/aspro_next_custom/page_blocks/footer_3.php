<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="footer_inner <?= ($arTheme["SHOW_BG_BLOCK"]["VALUE"] == "Y" ? "fill" : "no_fill"); ?>">

	<div class="wrapper_inner">
		<div class="footer_bottom_inner">
			<div class="left_block">
				<div class="copyright">
					<? include($_SERVER['DOCUMENT_ROOT'] . "/include/footer/copy/copyright.php"); ?>
					<img loading="lazy" class="yandex-rating" src="https://avatars.mds.yandex.net/get-altay/474904/badge_rating_light_4.8/orig" alt="rating"><br>
				</div>
				<div id="bx-composite-banner"></div>
			</div>
			<div class="right_block">
				<div class="middle">
					<div class="row">
						<div class="item_block col-md-9 menus">
							<div class="row">
								<div class="item_block col-xs-6 col-md-4 col-sm-4">
									<? $APPLICATION->IncludeComponent(
										"bitrix:menu",
										"bottom",
										array(
											"ROOT_MENU_TYPE" => "bottom_company",
											"MENU_CACHE_TYPE" => "A",
											"MENU_CACHE_TIME" => "3600000",
											"MENU_CACHE_USE_GROUPS" => "N",
											"CACHE_SELECTED_ITEMS" => "N",
											"MENU_CACHE_GET_VARS" => array(),
											"MAX_LEVEL" => "1",
											"USE_EXT" => "N",
											"DELAY" => "N",
											"ALLOW_MULTI_SELECT" => "N"
										),
										false
									); ?>
								</div>
								<div class="item_block col-xs-6 col-md-4 col-sm-4">
									<? $APPLICATION->IncludeComponent(
										"bitrix:menu",
										"bottom",
										array(
											"ROOT_MENU_TYPE" => "bottom_info",
											"MENU_CACHE_TYPE" => "A",
											"MENU_CACHE_TIME" => "3600000",
											"MENU_CACHE_USE_GROUPS" => "N",
											"CACHE_SELECTED_ITEMS" => "N",
											"MENU_CACHE_GET_VARS" => array(),
											"MAX_LEVEL" => "1",
											"USE_EXT" => "N",
											"DELAY" => "N",
											"ALLOW_MULTI_SELECT" => "N"
										),
										false
									); ?>
								</div>
								<div class="item_block col-xs-12 col-md-4 col-sm-4">
									<? $APPLICATION->IncludeComponent(
										"bitrix:menu",
										"bottom",
										array(
											"ROOT_MENU_TYPE" => "bottom_help",
											"MENU_CACHE_TYPE" => "A",
											"MENU_CACHE_TIME" => "3600000",
											"MENU_CACHE_USE_GROUPS" => "N",
											"CACHE_SELECTED_ITEMS" => "N",
											"MENU_CACHE_GET_VARS" => array(),
											"MAX_LEVEL" => "1",
											"USE_EXT" => "N",
											"DELAY" => "N",
											"ALLOW_MULTI_SELECT" => "N"
										),
										false
									); ?>
								</div>
							</div>
						</div>
						<div class="item_block col-md-3 soc">
							<div class="soc_wrapper">
								<div class="phones">
									<div class="phone_block">
										<?= CNext::ShowHeaderPhones(); ?>
										<? if ($arTheme['SHOW_CALLBACK']['VALUE'] == 'Y') : ?>
											<span class="order_wrap_btn">
												<span class="callback_btn animate-load" data-event="jqm" data-param-form_id="CALLBACK" data-name="callback"><?= GetMessage('CALLBACK') ?></span>
											</span>
										<? endif; ?>
									</div>
								</div>
								<div class="social_wrapper">
									<div class="social">
										<? include($_SERVER['DOCUMENT_ROOT'] . "/include/footer/social.info.next.default.php"); ?>
									</div>
									<p><small>Сайт разработан в "<a href="https://wlagency.ru" target="_blank">WL, digital-агентство</a>"</small></p>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="mobile_copy">
			<? include($_SERVER['DOCUMENT_ROOT'] . "/include/footer/copyright.php"); ?>
		</div>
	</div>
</div>