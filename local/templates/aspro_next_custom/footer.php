<? CNext::checkRestartBuffer(); ?>
<? IncludeTemplateLangFile(__FILE__); ?>
<? if (!$isIndex): ?>
    <? if ($isBlog): ?>
        </div> <? // class=col-md-9 col-sm-9 col-xs-8 content-md?>
        <div class="col-md-3 col-sm-3 hidden-xs hidden-sm right-menu-md">
            <div class="sidearea">
                <? $APPLICATION->ShowViewContent('under_sidebar_content'); ?>
                <? CNext::get_banners_position('SIDE', 'Y'); ?>
                <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "sect", "AREA_FILE_SUFFIX" => "sidebar", "AREA_FILE_RECURSIVE" => "Y"), false); ?>
            </div>
        </div>
        </div><? endif; ?>
    <? if ($isHideLeftBlock): ?>
        </div> <? // .maxwidth-theme?>
    <? endif; ?>
    </div> <? // .container?>
<? else: ?>
    <? CNext::ShowPageType('indexblocks'); ?>
<? endif; ?>
<? CNext::get_banners_position('CONTENT_BOTTOM'); ?>
</div> <? // .middle?>
<? //if(!$isHideLeftBlock && !$isBlog):?>
<? if (($isIndex && $isShowIndexLeftBlock) || (!$isIndex && !$isHideLeftBlock) && !$isBlog): ?>
    </div> <? // .right_block?>
    <? if ($APPLICATION->GetProperty("HIDE_LEFT_BLOCK") != "Y" && !defined("ERROR_404")): ?>
        <div class="left_block">
            <? CNext::ShowPageType('left_block'); ?>
        </div>
    <? endif; ?>
<? endif; ?>
<? if ($isIndex): ?>
    </div>
<? elseif (!$isWidePage): ?>
    </div> <? // .wrapper_inner?>
<? endif; ?>
</div> <? // #content?>
<? CNext::get_banners_position('FOOTER'); ?>
</div><? // .wrapper?>
<footer id="footer">
    <? if ($APPLICATION->GetProperty("viewed_show") == "Y" || $is404): ?>
        <? include($_SERVER['DOCUMENT_ROOT'] . "/include/footer/comp_viewed.php");?>
    <? endif; ?>
    <? CNext::ShowPageType('footer'); ?>
</footer>
<div class="bx_areas">
    <? CNext::ShowPageType('bottom_counter'); ?>
</div>
<? CNext::ShowPageType('search_title_component'); ?>
<? CNext::setFooterTitle();
CNext::showFooterBasket(); ?>
<script>
    let USER_AUTH = <?=($USER->IsAuthorized()) ? 'true' : 'false'?>;
    <?if ($USER->IsAuthorized()) {
        echo "localStorage.setItem('user_auth', true)";
    } else {
        echo "localStorage.setItem('user_auth', false)";
    }?>
</script>
<? global $APPLICATION;
if($APPLICATION->GetCurPage() === "/" || strpos($APPLICATION->GetCurPage(), '/catalog/') === 0) {
?>
    <? CModule::IncludeModule("wl.snailshop");?>
    <? $APPLICATION->IncludeComponent('wl:popup_messages', '', ['IBLOCK_ID' => WL\IblockUtils::getIdByCode('popup_messages')], false); ?>
<? } ?>

<div id="toast-container"></div>


<?
CModule::IncludeModule('wl.snailshop');

$APPLICATION->IncludeComponent(
	"wl:sticker.colors",
	"css",
	Array(
		"IBLOCK_ID" => WL\IBlock::getIblockIDByCode('sticker_colors'),
	)
);
?>

<div class="fly-btn-feedback" id="fly-btn-feedback-container">
    <a rel="nofollow" target="_blank" href="https://api.whatsapp.com/send?phone=79202488898" class="fly-btn-feedback__item fly-btn-feedback__item--whatsapp"></a>
</div>

</body>
</html>

<?
    if (empty($APPLICATION->GetPageProperty('canonical'))) {
        $APPLICATION->SetPageProperty('canonical', "https://" . $_SERVER['HTTP_HOST'] . $APPLICATION->GetCurDir());
    } else {
        $cur_canonical = $APPLICATION->GetPageProperty('canonical');
        if(stripos($cur_canonical, 'clear_cache=Y') || stripos($cur_canonical, 'PAGEN_')) {
            $APPLICATION->SetPageProperty('canonical', "https://" . $_SERVER['HTTP_HOST'] . $APPLICATION->GetCurDir());
        }
    }
?>