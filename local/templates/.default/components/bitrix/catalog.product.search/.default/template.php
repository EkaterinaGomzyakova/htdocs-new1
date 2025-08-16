<?php
CModule::IncludeModule("wl.snailshop");

if ($arResult['CALLER'] == 'order_edit') {
    require('order_page.php');
} else {
    require('default.php');
}