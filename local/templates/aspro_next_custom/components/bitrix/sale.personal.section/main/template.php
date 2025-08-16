<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
$availablePages = [];

$availablePages[] = array(
	"path" => "/personal/wishlist/",
	"name" => Loc::getMessage("SPS_WISHLIST"),
	"icon" => '<i class="whishlist"></i>'
);

$availablePages[] = array(
	"path" => "/personal/my-reviews/",
	"name" => Loc::getMessage("SPS_MY_REVIEWS"),
	"icon" => '<i class="reviews"></i>'
);

$availablePages[] = array(
	"path" => "/personal/shopping-list/",
	"name" => Loc::getMessage("SPS_MY_PRODUCTS"),
	"icon" => '<i class="cur_orders"></i>'
);

if ($arParams['SHOW_ORDER_PAGE'] === 'Y')
{
	$availablePages[] = array(
		"path" => $arResult['PATH_TO_ORDERS'],
		"name" => Loc::getMessage("SPS_ORDER_PAGE_NAME"),
		"icon" => '<i class="cur_orders"></i>'
	);
}

if ($arParams['SHOW_ACCOUNT_PAGE'] === 'Y')
{
	$availablePages[] = array(
		"path" => $arResult['PATH_TO_ACCOUNT'],
		"name" => Loc::getMessage("SPS_ACCOUNT_PAGE_NAME"),
		"icon" => '<i class="bill"></i>'
	);
}

if ($arParams['SHOW_PRIVATE_PAGE'] === 'Y')
{
	$availablePages[] = array(
		"path" => $arResult['PATH_TO_PRIVATE'],
		"name" => Loc::getMessage("SPS_PERSONAL_PAGE_NAME"),
		"icon" => '<i class="personal"></i>'
	);
}

/*if ($arParams['SHOW_ORDER_PAGE'] === 'Y')
{

	$delimeter = ($arParams['SEF_MODE'] === 'Y') ? "?" : "&";
	$availablePages[] = array(
		"path" => $arResult['PATH_TO_ORDERS'].$delimeter."filter_history=Y",
		"name" => Loc::getMessage("SPS_ORDER_PAGE_HISTORY"),
		"icon" => '<i class="filter_orders"></i>'
	);
}*/

if ($arParams['SHOW_PROFILE_PAGE'] === 'Y')
{
	$availablePages[] = array(
		"path" => $arResult['PATH_TO_PROFILE'],
		"name" => Loc::getMessage("SPS_PROFILE_PAGE_NAME"),
		"icon" => '<i class="profile"></i>'
	);
}

if ($arParams['SHOW_BASKET_PAGE'] === 'Y')
{
	$availablePages[] = array(
		"path" => $arParams['PATH_TO_BASKET'],
		"name" => Loc::getMessage("SPS_BASKET_PAGE_NAME"),
		"icon" => '<i class="cart"></i>'
	);
}

if ($arParams['SHOW_SUBSCRIBE_PAGE'] === 'Y')
{
	$availablePages[] = array(
		"path" => $arResult['PATH_TO_SUBSCRIBE'],
		"name" => Loc::getMessage("SPS_SUBSCRIBE_PAGE_NAME"),
		"icon" => '<i class="subscribe"></i>'
	);
}

if ($arParams['SHOW_CONTACT_PAGE'] === 'Y')
{
	$availablePages[] = array(
		"path" => $arParams['PATH_TO_CONTACT'],
		"name" => Loc::getMessage("SPS_CONTACT_PAGE_NAME"),
		"icon" => '<i class="contact"></i>'
	);
}

$customPagesList = \Bitrix\Main\Web\Json::decode(htmlspecialchars_decode($arParams['CUSTOM_PAGES']));
if ($customPagesList)
{
	foreach ($customPagesList as $page)
	{
		$availablePages[] = array(
			"path" => $page[0],
			"name" => $page[1],
			"icon" => (strlen($page[2])) ? '<i class="fa '.htmlspecialcharsbx($page[2]).'"></i>' : ""
		);
	}
}

if (empty($availablePages))
{
	ShowError(Loc::getMessage("SPS_ERROR_NOT_CHOSEN_ELEMENT"));
}
else
{
	?>
	
	<div class="personal_wrapper">
        <? if ($arResult["REFERAL_COUPON"]) { ?>
            <div class="referral-program-wrapper">
                <h3 class="referral-program-title"><?= Loc::getMessage('REFERRAL-PROGRAM') ?></h3>
                <div class="referral-program-wrapper-flex">
                    <div class="referral-program-title"><p><?= Loc::getMessage('REFERRAL-PROGRAM-TITLE') ?></p></div>
                    <div class="referral-program-title"><strong><?= $arResult["REFERAL_COUPON"] ?></strong></div>
                </div>
            </div>
        <? } ?>
		<div class="row sale-personal-section-row-flex">
			<?
			foreach ($availablePages as $blockElement)
			{
				?>
				<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
					<div class="sale-personal-section-index-block bx-theme-<?=$theme?>">
						<a class="sale-personal-section-index-block-link" href="<?=htmlspecialcharsbx($blockElement['path'])?>">
						<span class="sale-personal-section-index-block-ico">
							<?=$blockElement['icon']?>
						</span>
							<h2 class="sale-personal-section-index-block-name">
								<?=htmlspecialcharsbx($blockElement['name'])?>
							</h2>
						</a>
					</div>
				</div>
				<?
			}
			?>
		</div>
	</div>
	<?
}
?>
