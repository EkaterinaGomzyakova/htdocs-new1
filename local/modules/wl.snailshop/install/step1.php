<?php

if (!check_bitrix_sessid()) return;

IncludeModuleLangFile(__FILE__);

if ($ex = $APPLICATION->GetException())
{
	echo CAdminMessage::ShowMessage(array(
		'TYPE'    => 'ERROR',
		'MESSAGE' => "Ошибка",
		'DETAILS' => $ex->GetString(),
		'HTML'    => true,
	));
}
else
{
	echo CAdminMessage::ShowNote('Модуль успешно установлен');
}

?>

<div style="font-size: 12px;"></div>
<br>
<form action="<?=$APPLICATION->GetCurPage(); ?>">
	<input type="hidden" name="lang" value="<?=LANG; ?>">
	<input type="submit" name="" value="Назад">
</form>
