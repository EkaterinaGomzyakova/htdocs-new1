<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<form class="form-horizontal wl-form" method="post" target="_top" action="<?= $arResult["AUTH_URL"] ?>">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="action" value="confirm">
    <h4 class="modal-title">Введите код из смс</h4>
    <?if(!empty($arResult['error'])):?>
    <div class="form-group">
        <div class="col-xs-12">
            <div class="alert alert-danger"><?=$arResult['error']?></div>
        </div>
    </div>
    <?endif;?>
    <div class="form-group">
        <label class="col-sm-4 control-label ">Код из смс:</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" name="SMS_CODE" value="<?=$_REQUEST['SMS_CODE']?>" required/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label ">Пароль:</label>
        <div class="col-sm-8">
            <input type="password" class="form-control" name="PASSWORD" required/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label ">Подтверждение пароля:</label>
        <div class="col-sm-8">
            <input type="password" class="form-control" name="CONFIRM_PASSWORD" required/>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <button type="submit" class="btn-main">Сохранить</button>
        </div>
    </div>
</form>
