<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);

$APPLICATION->SetAdditionalCSS("/bitrix/modules/parnas.khayrcomment/libs/rateit.js/1.0.23/rateit.css");
$APPLICATION->AddHeadScript("/bitrix/modules/parnas.khayrcomment/libs/rateit.js/1.0.23/jquery.rateit.js");

function KHAYR_MAIN_COMMENT_ShowTree($arItem, $arParams, $arResult, $isChild = false)
{
	global $APPLICATION;
	?>
	<div class="stock <?= $isChild ? 'stock-child': '';?>">
		<div itemscope="" itemprop="review" itemtype="https://schema.org/Review">
			<div class="userInfo" itemprop="author" itemscope itemtype="https://schema.org/Person">
				<? if($isChild) { ?>
					<span itemprop="name">CLANBEAUTY</span>
				<? } else { ?>
					<span itemprop="name"><?=GetMessage("KHAYR_COMMENT_ID")?><?=$arItem['ID']?></span>
				<? } ?>
			</div>
			
			<span itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating" style="display: none;">
				<span itemprop="ratingValue">5</span>
			</span>

			<span itemprop="name" style="display: none;"><?= $arResult['PRODUCT']['NAME']?></span>
			<span itemprop="datePublished" style="display: none;"><?= (new DateTime($arItem['DATE_CREATE']))->format('Y-m-d')?></span>
			<a href="https://clanbeauty.ru<?= $APPLICATION->GetCurPage();?>" itemprop="url" style="display: none;">https://clanbeauty.ru/<?= $APPLICATION->GetCurPage();?></a>
			<div class="userText" itemprop="reviewBody">
				<div>

					<?if ($arItem["SKIN_TYPE"]) {?>
						<p><b><?=GetMessage("KHAYR_MAIN_COMMENT_SKIN_TYPE")?></b>: <?=$arItem["SKIN_TYPE"]?></p>
					<?}?>
					<?if ($arItem["AGE"]) {?>
						<p><b><?=GetMessage("KHAYR_MAIN_COMMENT_AGE")?></b>: <?=$arItem["AGE"]?></p>
					<?}?>

					<?if ($arItem["DIGNITY"]) {?>
						<p><b><?=GetMessage("KHAYR_MAIN_COMMENT_DIGNITY")?></b>
						<?=$arItem["DIGNITY"]?>
						</p>
					<?}?>
					<?if ($arItem["FAULT"]) {?>
						<p><b><?=GetMessage("KHAYR_MAIN_COMMENT_FAULT")?></b>
						<?=$arItem["FAULT"]?>
						</p>
					<?}?>
					<p><?=$arItem["PUBLISH_TEXT"]?></p>
				</div>
			</div>
		</div>

		<div class='action'>
			<?global $USER;?>
			<?if ($USER->IsAdmin()) {?>
				<a href="javascript:void();" onclick='KHAYR_MAIN_COMMENT_add(this, <?=$arItem["ID"]?>); return false;' title='<?=GetMessage("KHAYR_MAIN_COMMENT_COMMENT")?>'><?=GetMessage("KHAYR_MAIN_COMMENT_COMMENT")?></a>
				&nbsp;|&nbsp;
				<a href="javascript:void();" onclick='KHAYR_MAIN_COMMENT_edit(this, <?=$arItem["ID"]?>); return false;' title="<?=GetMessage("KHAYR_MAIN_COMMENT_EDIT")?>"><?=GetMessage("KHAYR_MAIN_COMMENT_EDIT")?></a>
				&nbsp;|&nbsp;
				<a href='javascript:void(0)' onclick='KHAYR_MAIN_COMMENT_delete(this, <?=$arItem["ID"]?>, "<?=GetMessage("KHAYR_MAIN_COMMENT_DEL_MESS")?>"); return false;' title='<?=GetMessage("KHAYR_MAIN_COMMENT_DELETE")?>'><?=GetMessage("KHAYR_MAIN_COMMENT_DELETE")?></a>
			<? } ?>
			<?if ($arItem["CAN_MODIFY"]) {?>
				<div class="form comment form_for" id='edit_form_<?=$arItem["ID"]?>'<?=($arResult["POST"]["COM_ID"] == $arItem["ID"] && !$arResult["SUCCESS"] ? " style='display: block;'" : "")?>>
					<form enctype="multipart/form-data" action="<?=$GLOBALS["APPLICATION"]->GetCurUri()?>" method='POST' onsubmit='return KHAYR_MAIN_COMMENT_validate(this);'>
						<textarea name="MESSAGE" rows="10" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_MESSAGE")?>'><?=$arItem["~PREVIEW_TEXT"]?></textarea>
						<input type='hidden' name='ACTION' value='update' />
						<input type='hidden' name='COM_ID' value='<?=$arItem["ID"]?>' />
						<input type="submit" class="btn btn-default" value="<?=GetMessage("KHAYR_MAIN_COMMENT_SAVE")?>" />
						<a href="javascript:void(0)" onclick='KHAYR_MAIN_COMMENT_back(); return false;' style='margin-top: -25px; text-decoration: none;'><?=GetMessage("KHAYR_MAIN_COMMENT_BACK_BUTTON")?></a>
					</form>
				</div>
			<?}?>
			<?if ($arItem["CAN_COMMENT"]) {?>
				<div class="form comment form_for" id='add_form_<?=$arItem["ID"]?>'<?=($arResult["POST"]["PARENT"] == $arItem["ID"] && !$arResult["SUCCESS"] ? " style='display: block;'" : "")?>>
					<form enctype="multipart/form-data" action="<?=$GLOBALS["APPLICATION"]->GetCurUri()?>" method='POST' onsubmit='return KHAYR_MAIN_COMMENT_validate(this);'>
						<?if ($arParams["LOAD_DIGNITY"]) {?>
							<textarea name="DIGNITY" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_DIGNITY")?>'><?=$arResult["POST"]["DIGNITY"]?></textarea>
						<?}?>
						<?if ($arParams["LOAD_FAULT"]) {?>
							<textarea name="FAULT" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_FAULT")?>'><?=$arResult["POST"]["FAULT"]?></textarea>
						<?}?>
						<textarea name="MESSAGE" rows="10" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_MESSAGE")?>'></textarea>
						<input type="text" name="SKIN_TYPE" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_SKIN_TYPE")?>' value="<?=$arResult["POST"]["SKIN_TYPE"]?>">
						<input type="text" name="AGE" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_AGE")?>' value="<?=$arResult["POST"]["AGE"]?>">

						<input type='hidden' name='PARENT' value='<?=$arItem["ID"]?>' />
						<input type='hidden' name='ACTION' value='add' />
						<input type='hidden' name='DEPTH' value='<?=($arItem["PROPERTIES"]["DEPTH"]["VALUE"]+1)?>' />
						<?if ($arParams["USE_CAPTCHA"]) {?>
							<div>
								<div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_1")?></div>
								<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>" />
								<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" />
								<div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_2")?></div>
								<input type="text" name="captcha_word" size="30" maxlength="50" value="" />
								<input type='hidden' name='clear_cache' value='Y' />
							</div>
						<?}?>
						<input type="submit" class="btn btn-default" value="<?=GetMessage("KHAYR_MAIN_COMMENT_ADD")?>" />
						<a href="javascript:void(0)" onclick='KHAYR_MAIN_COMMENT_back(); return false;' style='margin-top: -25px; text-decoration: none;'><?=GetMessage("KHAYR_MAIN_COMMENT_BACK_BUTTON")?></a>
					</form>
				</div>
			<?}?>
		</div>

		<?if (!empty($arItem["CHILDS"])) {?>
			<?foreach ($arItem["CHILDS"] as $item) {?>
				<?=KHAYR_MAIN_COMMENT_ShowTree($item, $arParams, $arResult, true)?>
			<?}?>
		<?}?>
	</div>
	<?
}
?>
<div class='khayr_main_comment' id='KHAYR_MAIN_COMMENT_container'>
	<?if (strlen($_POST["ACTION"]) > 0) $GLOBALS["APPLICATION"]->RestartBuffer();?>
	<p style='color: green; display: none;' class='suc'><?=$arResult["SUCCESS"]?></p>
	<p style='color: red; display: none;' class='err'><?=$arResult["ERROR_MESSAGE"]?></p>

	<?if ($arResult["ITEMS"]) {?>
		<?if ($arParams["DISPLAY_TOP_PAGER"]) {?>
			<div class="nav"><?=$arResult["NAV_STRING"]?></div>
		<?}?>
		<div class="comments">
			<?if(count($arResult['ITEMS']) > 0) {?>
				<div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating" style="display: none;">
					<span itemprop="ratingValue">5</span> звезд -
					на основе <span itemprop="reviewCount"><?= count($arResult['ITEMS'])?></span> оценок
				</div>
			<? } ?>
			<?
			foreach ($arResult["ITEMS"] as $k => $arItem)
			{
				echo KHAYR_MAIN_COMMENT_ShowTree($arItem, $arParams, $arResult);
			}
			?>
		</div>
		<?if ($arParams["DISPLAY_BOTTOM_PAGER"]) {?>
			<div class="nav"><?=$arResult["NAV_STRING"]?></div>
		<?}?>
	<?}?>
    <div class="form comment main_form"<?=($arResult["POST"]["PARENT"] > 0 && !$arResult["SUCCESS"] ? " style='display: none;' " : "")?>>
        <div class="wrapper-form-comment">
        <?if ($arResult["CAN_COMMENT"]) {?>
            <p><?=GetMessage("KHAYR_MAIN_COMMENT_WELCOME")?></p>
            <form enctype="multipart/form-data" action="<?=$GLOBALS["APPLICATION"]->GetCurUri()?>" method='POST' onsubmit='return KHAYR_MAIN_COMMENT_validate(this);'>
                <?if ($arParams["LOAD_DIGNITY"]) {?>
                    <textarea name="DIGNITY" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_DIGNITY")?>'><?=$arResult["POST"]["DIGNITY"]?></textarea>
                <?}?>
                <?if ($arParams["LOAD_FAULT"]) {?>
                    <textarea name="FAULT" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_FAULT")?>'><?=$arResult["POST"]["FAULT"]?></textarea>
                <?}?>
                <textarea name="MESSAGE" rows="10" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_MESSAGE")?>'><?=$arResult["POST"]["MESSAGE"]?></textarea>
				<input type="text" name="SKIN_TYPE" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_SKIN_TYPE")?>' value="<?=$arResult["POST"]["SKIN_TYPE"]?>">
				<input type="text" name="AGE" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_AGE")?>' value="<?=$arResult["POST"]["AGE"]?>">
                <input type='hidden' name='PARENT' value='' />
                <input type='hidden' name='ACTION' value='add' />
                <input type='hidden' name='DEPTH' value='1' />
                <?if ($arParams["USE_CAPTCHA"]) {?>
                    <div>
                        <div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_1")?></div>
                        <input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>" />
                        <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" />
                        <div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_2")?></div>
                        <input type="text" name="captcha_word" size="30" maxlength="50" value="" />
                        <input type='hidden' name='clear_cache' value='Y' />
                    </div>
                <?}?>
                <input type="submit" class="btn btn-default" value="<?=GetMessage("KHAYR_MAIN_COMMENT_ADD")?>" />
            </form>
        <?} else {?>
            <p><?=GetMessage("KHAYR_MAIN_COMMENT_DO_AUTH", array("#LINK#" => $arParams["AUTH_PATH"]))?></p>
        <?}?>
        </div>
    </div>
	<?if (strlen($_POST["ACTION"]) > 0) die();?>
</div>