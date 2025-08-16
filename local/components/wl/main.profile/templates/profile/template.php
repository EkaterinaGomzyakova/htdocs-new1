<? use Bitrix\Main\Localization\Loc;

    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="module-form-block-wr lk-page border_block">

<?ShowError($arResult["strProfileError"]);?>
<?if( $arResult['DATA_SAVED'] == 'Y' ) {?><?ShowNote(GetMessage('PROFILE_DATA_SAVED'))?><br /><?; }?>
<script>
	$(document).ready(function()
	{
		$(".form-block-wr form").validate({rules:{ EMAIL: { email: true }}	});
	})
</script>
	<?global $arTheme?>
	<div class="form-block-wr">
		<form method="post" name="form1" class="main" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
			<?=$arResult["BX_SESSION_CHECK"]?>
			<input type="hidden" name="lang" value="<?=LANG?>" />
			<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
			<?if($arTheme["PERSONAL_ONEFIO"]["VALUE"] == "Y"):?>
				<div class="form-control">
					<div class="wrap_md">
						<div class="iblock label_block">
							<label><?=GetMessage("PERSONAL_FIO")?><span class="star">*</span></label>
							<?
							$arName = array();
							if(!$arResult["strProfileError"])
							{
								if($arResult["arUser"]["LAST_NAME"]){
									$arName[] = $arResult["arUser"]["LAST_NAME"];
								}
								if($arResult["arUser"]["NAME"]){
									$arName[] = $arResult["arUser"]["NAME"];
								}
								if($arResult["arUser"]["SECOND_NAME"]){
									$arName[] = $arResult["arUser"]["SECOND_NAME"];
								}
							}
							else
								$arName[] = htmlspecialcharsbx($_POST["NAME"]);
							?>
							<input required type="text" name="NAME" maxlength="50" value="<?=implode(' ', $arName);?>" />
						</div>
						<div class="iblock text_block">
							<?=GetMessage("PERSONAL_NAME_DESCRIPTION")?>
						</div>
					</div>
				</div>
			<?else:?>
				<div class="form-control">
					<div class="wrap_md">
						<div class="iblock label_block">
							<label><?=GetMessage("PERSONAL_LASTNAME")?></label>
							<input type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"];?>" />
						</div>
					</div>
				</div>
				<div class="form-control">
					<div class="wrap_md">
						<div class="iblock label_block">
							<label><?=GetMessage("PERSONAL_NAME")?></label>
							<input type="text" name="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"];?>" />
						</div>
					</div>
				</div>
				<div class="form-control">
					<div class="wrap_md">
						<div class="iblock label_block">
							<label><?=GetMessage("PERSONAL_SECONDNAME")?></label>
							<input type="text" name="SECOND_NAME" maxlength="50" value="<?=$arResult["arUser"]["SECOND_NAME"];?>" />
						</div>
					</div>
				</div>
			<?endif;?>			
			<div class="form-control">
				<div class="wrap_md">
					<div class="iblock label_block">
						<label><?=GetMessage("PERSONAL_PHONE")?><span class="star">*</span></label>
						<?
						$mask = \Bitrix\Main\Config\Option::get('aspro.next', 'PHONE_MASK', '+7 (999) 999-99-99');
						if(strpos($arResult["arUser"]["PERSONAL_PHONE"], '+') === false && strpos($mask, '+') !== false)
						{
							$arResult["arUser"]["PERSONAL_PHONE"] = '+'.$arResult["arUser"]["PERSONAL_PHONE"];
						}
						?>
						<input required type="tel" name="PERSONAL_PHONE" class="phone" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_PHONE"]?>" />
					</div>
					<div class="iblock text_block">
						<?=GetMessage("PERSONAL_PHONE_DESCRIPTION")?>
					</div>
				</div>
			</div>
			<div class="form-control">
				<div class="wrap_md">
					<div class="iblock label_block">
						<label><?=GetMessage("PERSONAL_EMAIL")?><span class="star">*</span></label>
						<input required type="text" name="EMAIL" maxlength="50" placeholder="name@company.ru" value="<? echo $arResult["arUser"]["EMAIL"]?>" />
					</div>
					<div class="iblock text_block">
						<?=GetMessage("PERSONAL_EMAIL_DESCRIPTION")?>
					</div>
				</div>
			</div>
            <div class="form-control">
                <div class="wrap_md">
                    <div class="iblock label_block">
                        <label><?=GetMessage("PERSONAL_BIRTHDAY")?></label>
                        <? if (empty($arResult["arUser"]["PERSONAL_BIRTHDAY"])) { ?>
                            <input required type="date" id="PERSONAL_BIRTHDAY" name="PERSONAL_BIRTHDAY" value="<? echo $arResult["arUser"]["PERSONAL_BIRTHDAY"]?>" />
                        <? } else { ?>
                            <span class="birthday-date"><? echo $arResult["arUser"]["PERSONAL_BIRTHDAY"]?></span>
                        <? } ?>
                    </div>
                    <div class="iblock text_block">
                        <? if (empty($arResult["arUser"]["PERSONAL_BIRTHDAY"])) { ?>
                            <?=GetMessage("PERSONAL_BIRTHDAY_DESCRIPTION")?>
                        <? } else { ?>
                            <?=GetMessage("PERSONAL_BIRTHDAY_DESCRIPTION_COMPLETED")?>
                        <? } ?>
                    </div>
                </div>
            </div>
			<div class="form-check">
				<div class="iblock label_block">
					<input type="hidden" name="UF_DENY_SMS" value="0" />
					<input class="form-check-input" type="checkbox" name="UF_DENY_SMS" id="UF_DENY_SMS" value="1" <? if($arResult["arUser"]["UF_DENY_SMS"]) {?>checked<?}?> />
					<label class="form-check-label"  for="UF_DENY_SMS"><?=GetMessage("USER_UNSUBSCRIBE_FROM_SMS")?></label>
				</div>
			</div>
			<div class="but-r">
				<button class="btn btn-default" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE_TITLE") : GetMessage("MAIN_ADD_TITLE"))?>"><span><?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE_TITLE") : GetMessage("MAIN_ADD_TITLE"))?></span></button>
			</div>
			
		</form>
		<? if($arResult["SOCSERV_ENABLED"]){ $APPLICATION->IncludeComponent("bitrix:socserv.auth.split", "main", array("SUFFIX"=>"form", "SHOW_PROFILES" => "Y","ALLOW_DELETE" => "Y"),false);}?>
	</div>
</div>