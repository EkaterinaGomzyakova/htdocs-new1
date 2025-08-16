<?
CModule::AddAutoloadClasses(
	'aspro.next',
	array(
		'CNextCache' => 'classes/general/CNextCache.php',
		'CNext' => 'classes/general/CNext.php',
		'CNextTools' => 'classes/general/CNextTools.php',
		'CNextEvents' => 'classes/general/CNextEvents.php',
		'CNextRegionality' => 'classes/general/CNextRegionality.php',
		'Aspro\\Solution\\CAsproMarketing' => 'classes/general/CAsproMarketing.php',
		'Aspro\\Functions\\CAsproSku' => 'lib/functions/CAsproSku.php', //for general sku functions
		'Aspro\\Functions\\CAsproItem' => 'lib/functions/CAsproItem.php', //for general item functions
		'Aspro\\Functions\\CAsproNext' => 'lib/functions/CAsproNext.php', //for only solution functions
		'Aspro\\Functions\\CAsproNextCustom' => 'lib/functions/CAsproNextCustom.php', //for user custom functions
		'Aspro\\Functions\\CAsproNextReCaptcha' => 'lib/functions/CAsproNextReCaptcha.php', //for google reCaptcha
	)
);