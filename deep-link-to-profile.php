<?include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?

$link ="";

$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");

if($iPod || $iPhone)
    $link = "instagram://user?username=" . \COption::GetOptionString("askaron.settings", "UF_INSTAGRAM_NAME");
else if($Android)
	$link = 'intent://instagram.com/_u/' . \COption::GetOptionString("askaron.settings", "UF_INSTAGRAM_NAME") . '/#Intent;package=com.instagram.android;scheme=https;end';
else
	$link = "https://instagram.com/" . \COption::GetOptionString("askaron.settings", "UF_INSTAGRAM_NAME") . "/";

header("Location: ".$link);