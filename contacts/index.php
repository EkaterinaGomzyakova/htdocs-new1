<?php
global $APPLICATION;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты и реквизиты интернет-магазина косметики ClanBeauty");
CNext::ShowPageType('page_contacts');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
