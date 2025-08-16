<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="border_block">
    <div class="module-form-block-wr lk-page">
        <div class="form-block">
            <form name="bform" method="post" target="_top" class="bf" action="<?= SITE_DIR ?>auth/forgot-password/">
                <input type="hidden" name="action" value="send">
                <p>Если вы забыли пароль, введите телефон.
                    Контрольная строка для смены пароля, а также ваши регистрационные данные, будут высланы вам по sms.</p>
                <?if(!empty($arResult['ERROR'])):?>
                    <div class="alert alert-danger"><?=$arResult['ERROR']?></div>
                <?endif;?>
                <div class="form-control">
                    <label>Телефон <span class="star">*</span></label>
                    <input type="tel" class="phone" name="PHONE" required="required" maxlength="255" value="<?=$arResult['VALUES']['PHONE']?>"/>
                </div>
                <? if ($arResult["USE_CAPTCHA"]): ?>
                    <div class="form-control captcha-row clearfix forget_block">
                        <label><? echo GetMessage("system_auth_captcha") ?></label>
                        <div class="captcha_image">
                            <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA"/>
                            <div class="captcha_reload"></div>
                        </div>
                        <div class="captcha_input">
                            <input type="text" name="captcha_word" maxlength="50" value=""/>
                        </div>
                    </div>
                <? endif ?>
                <div class="but-r">
                    <button class="btn btn-default vbig_btn wides" type="submit" name="send_account_info" value=""><span>Восстановить</span></button>
                </div>
            </form>
        </div>
        <script type="text/javascript">
            InitPhoneMask();
        </script>
    </div>
</div>

