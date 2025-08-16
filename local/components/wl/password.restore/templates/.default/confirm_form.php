<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="border_block">
    <div class="module-form-block-wr lk-page">
        <div class="form-block">
            <form name="bform" method="post" target="_top" class="bf">
                <input type="hidden" name="action" value="confirm">
                <input type="hidden" name="PHONE" value="<?=$arResult['VALUES']['PHONE']?>">
                <p>Введите код из смс</p>
                <?if(!empty($arResult['ERROR'])):?>
                    <div class="alert alert-danger"><?=$arResult['ERROR']?></div>
                <?endif;?>
                <div class="form-control">
                    <label>Код из смс <span class="star">*</span></label>
                    <input type="text" name="CODE" required="required" autocomplete="one-time-code" maxlength="255" value="<?=$arResult['VALUES']['CODE']?>"/>
                </div>
                <div class="form-control">
                    <label>Пароль <span class="star">*</span></label>
                    <input type="password" name="NEW_PASSWORD" required="required" maxlength="255" />
                </div>
                <div class="form-control">
                    <label>Подтверждение пароля <span class="star">*</span></label>
                    <input type="password" name="PASSWORD_CONFIRM" required="required" maxlength="255" />
                </div>
                <div class="but-r">
                    <button class="btn btn-default vbig_btn wides" type="submit" name="send_account_info" value=""><span>Восстановить</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

