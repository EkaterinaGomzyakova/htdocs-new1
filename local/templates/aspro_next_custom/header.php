<?

use WL\SnailShop;

 if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
IncludeTemplateLangFile(__FILE__);
global $APPLICATION, $arSite, $arTheme;
$arSite = CSite::GetByID(SITE_ID)->Fetch();
$htmlClass = ($_REQUEST && isset($_REQUEST['print']) ? 'print' : false);
\Bitrix\Main\Loader::includeModule("aspro.next");
\Bitrix\Main\Loader::includeModule("wl.snailshop");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= LANGUAGE_ID ?>" lang="<?= LANGUAGE_ID ?>" <?= ($htmlClass ? 'class="' . $htmlClass . '"' : '') ?>>

<head>
	<title><? $APPLICATION->ShowTitle() ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<? $APPLICATION->ShowMeta("HandheldFriendly"); ?>
	<? $APPLICATION->ShowMeta("SKYPE_TOOLBAR"); ?>

	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />


	<link rel="icon" type="image/x-icon" href="/favicon.svg" />

	<meta name="theme-color" content="#285b4d" />
	<meta name="msapplication-navbutton-color" content="#285b4d">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-status-bar-style" content="black-translucent">

	<link rel="apple-touch-icon" sizes="57x57" href="/favicons/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/favicons/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/favicons/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/favicons/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/favicons/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/favicons/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/favicons/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/favicons/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192" href="/favicons/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/favicons/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
	<link rel="manifest" href="/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/favicons/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">

	<? $APPLICATION->ShowHead(); ?>
	<? $APPLICATION->AddHeadString('<script>BX.message(' . CUtil::PhpToJSObject($MESS, false) . ')</script>', true); ?>
	<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/fastclick.js'); ?>
	<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/bootstrap.toast.js'); ?>
	<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/owl.carousel.js'); ?>
	<? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/owl.carousel.css'); ?>
	<? CNext::Start(SITE_ID); ?>
	
</head>

<body class="fill_bg_<?= strtolower(CNext::GetFrontParametrValue('SHOW_BG_BLOCK')) ?>" id="main">
	<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>

	<? $arTheme = $APPLICATION->IncludeComponent("aspro:theme.next", ".default", array("COMPONENT_TEMPLATE" => ".default"), false, array("HIDE_ICONS" => "Y")); ?>
	<? include_once('defines.php'); ?>
	<? CNext::SetJSOptions(); ?>
	<? SnailShop::getOptionsValuesJS(['basket_comment_is_active']); ?>

	<? include($_SERVER['DOCUMENT_ROOT'] . "/include/menu/mobileappmenu.php"); ?>
	

	<? if ($APPLICATION->GetCurDir() == "/") { ?>
		<? $APPLICATION->IncludeComponent(
			"clanbeauty:roulete",
			"",
			[
				"IBLOCK_ID" => WL\Iblock::getIblockIDByCode("roulete"),
				"USER_ID" => $USER->getId(),
			],
			false
		); ?>
	<? } ?>

	<div class="wrapper1 <?= ($isIndex && $isShowIndexLeftBlock ? "with_left_block" : ""); ?> <?= CNext::getCurrentPageClass(); ?> <?= CNext::getCurrentThemeClasses(); ?>">
		<? CNext::get_banners_position('TOP_HEADER'); ?>

		<div class="header_wrap visible-lg visible-md title-v<?= $arTheme["PAGE_TITLE"]["VALUE"]; ?><?= ($isIndex ? ' index' : '') ?>">
			<header id="header">
				<? CNext::ShowPageType('header'); ?>
			</header>
		</div>
		<? CNext::get_banners_position('TOP_UNDERHEADER'); ?>

		<? if ($arTheme["TOP_MENU_FIXED"]["VALUE"] == 'Y') : ?>
			<div id="headerfixed">
				<? CNext::ShowPageType('header_fixed'); ?>
			</div>
		<? endif; ?>
		<?
		global $USER;
		$userId = $USER->GetId();
		$userTotalOrderCount = 0;

		if (intval($userId) > 0) {
			$dbBuyerStatistic = \Bitrix\Sale\Internals\BuyerStatisticTable::getList([
				'select' => ['USER_ID'],
				'count_total' => true,
				'filter' => ['USER_ID' => $userId],
				'limit' => 1
			]);
			$userTotalOrderCount = $dbBuyerStatistic->getCount();
		}
		?>

		<? if (
			(!$USER->isAuthorized() || $userTotalOrderCount == 0) &&
			!\WL\SnailShop::userIsStaff()
		) { ?>
			<div class="top-mobile-banner">
				<span><?= GetMessage('DISCOUNT_COUPON_FIRST'); ?></span>
			</div>
		<? } ?>
		<?/*
		<div class="top-mobile-banner">
			<span><?= GetMessage('IMPORTANT_INFO');?></span>
		</div>
		*/ ?>

		<div id="mobileheader" class="visible-xs visible-sm">
			<? CNext::ShowPageType('header_mobile'); ?>
			<div id="mobilemenu" class="<?= ($arTheme["HEADER_MOBILE_MENU_OPEN"]["VALUE"] == '1' ? 'leftside' : 'dropdown') ?>">
				<? CNext::ShowPageType('header_mobile_menu'); ?>
			</div>
		</div>

		<?
		if ($isIndex) {
			$GLOBALS['arrPopularSections'] = array('UF_POPULAR' => 1);
			$GLOBALS['arrFrontElements'] = array('PROPERTY_SHOW_ON_INDEX_PAGE_VALUE' => 'Y');
		} ?>

		<div class="wraps hover_<?= $arTheme["HOVER_TYPE_IMG"]["VALUE"]; ?>" id="content" style="background-color: <? $APPLICATION->ShowViewContent("section_color") ?>">
			<? if (!$is404 && !$isForm && !$isIndex) : ?>
				<? $APPLICATION->ShowViewContent('section_bnr_content'); ?>
				<? if ($APPLICATION->GetProperty("HIDETITLE") !== 'Y') : ?>
					<!--title_content-->
					<? CNext::ShowPageType('page_title'); ?>
					<!--end-title_content-->
				<? endif; ?>
				<? $APPLICATION->ShowViewContent('top_section_filter_content'); ?>
			<? endif; ?>

			<? if ($isIndex) : ?>
				<div class="wrapper_inner front <?= ($isShowIndexLeftBlock ? "" : "wide_page"); ?>">
				<? elseif (!$isWidePage) : ?>
					<div class="wrapper_inner <?= ($isHideLeftBlock ? "wide_page" : ""); ?>">
					<? endif; ?>

					<? if (($isIndex && $isShowIndexLeftBlock) || (!$isIndex && !$isHideLeftBlock) && !$isBlog) : ?>
						<div class="right_block <?= (defined("ERROR_404") ? "error_page" : ""); ?> wide_<?= CNext::ShowPageProps("HIDE_LEFT_BLOCK"); ?>">
						<? endif; ?>
						<div class="middle <?= ($is404 ? 'error-page' : ''); ?>">
							<? CNext::get_banners_position('CONTENT_TOP'); ?>
							<? if (!$isIndex) : ?>
								<div class="container">
									<? //h1
									?>
									<? if ($isHideLeftBlock && !$isWidePage) : ?>
										<div class="maxwidth-theme">
										<? endif; ?>
										<? if ($isBlog) : ?>
											<div class="row">
												<div class="col-md-9 col-sm-12 col-xs-12 content-md <?= CNext::ShowPageProps("ERROR_404"); ?>">
												<? endif; ?>
											<? endif; ?>
											<? CNext::checkRestartBuffer(); ?>
