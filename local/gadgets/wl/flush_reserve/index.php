<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
\Bitrix\Main\Page\Asset::getInstance()->addJs('/local/gadgets/wl/flush_reserve/script.js');
?>
<div>
	<form data-form-flush-reserves>
		<p>Убедитесь, что все заказы отгружены перед сбросом резервов.</p>
		<div class="adm-info-message" data-errors-block style="display: none;"></div>
		<div class="adm-info-message" data-success-block style="display: none;">Резервы успешно сброшены</div>
		<input type="submit" class="adm-btn-save" name="flushButton" value="Пересчитать резервы">
	</form>
</div>