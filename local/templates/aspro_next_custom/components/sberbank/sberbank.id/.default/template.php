<?php
IncludeModuleLangFile(__FILE__);

if ($arResult['MODULE_SETTINGS']['BUTTON_SIZE'] == 'MEDIUM') {
	$button_size = 'md';
} else if ($arResult['MODULE_SETTINGS']['BUTTON_SIZE'] == 'SMALL') {
	$button_size = 'xs';
} else if ($arResult['MODULE_SETTINGS']['BUTTON_SIZE'] == 'LARGE') {
	$button_size = 'xl';
}

$button_type = 'default';

if ($arResult['MODULE_SETTINGS']['BUTTON_STYLE'] == 'FLAT') {
	$button_theme = 'default';
} else if ($arResult['MODULE_SETTINGS']['BUTTON_STYLE'] == 'BORDER') {
	$button_theme = 'light';
}
$container_id = $arResult['CONTAINER_ID'] ? '' . $arResult['CONTAINER_ID'] : 'sber_id_' . rand(1, 1000);
?>

<div class="sber-id-container <?= $container_id; ?>" id="<?= $container_id; ?>"></div>
<script>
	const oidcParams = {
		response_type: 'code',
		client_type: 'PRIVATE',
		client_id: '<?= $arResult['AUTH_PARAMS']['client_id']; ?>',
		redirect_uri: '<?= $arResult['AUTH_PARAMS']['redirect_uri']; ?>',
		scope: '<?= $arResult['AUTH_PARAMS']['scope']; ?>',
		nonce: '<?= $arResult['AUTH_PARAMS']['nonce']; ?>',
		state: '<?= $arResult['AUTH_PARAMS']['state']; ?>',
		app: false
	};
	const sa = {
		enable: true,
		init: 'auto',
		clientId: '<?= $arResult['AUTH_PARAMS']['client_id']; ?>',
	};
	let params = {
		debug: false,
		baseUrl: '<?= $arResult['BASE_URL']; ?>',
		oidc: oidcParams,
		container: '.sber-id-container<?= '.' . $container_id ?>',
		display: '<?= $arResult['MODULE_SETTINGS']['TYPE_OPEN_AUTH'] == 'popup' ? 'popup' : 'desktop'; ?>',
		generateState: true,
		onSuccessCallback,
		onErrorCallback,
		sa: sa,
		buttonProps: {
			theme: '<?= $button_theme; ?>',
			size: '<?= $button_size; ?>',
		}
	}


	<? /* WL Custom */
		unset($arResult['MODULE_SETTINGS']['NOTIFICATION']);
	?>

	<?php if ($arResult['MODULE_SETTINGS']['NOTIFICATION']) { ?>
		const notification = {
			enable: true,
			serviceName: "",
			autoClose: false,
			autoCloseDelay: 90,
			position: '<?= $arResult['MODULE_SETTINGS']['NOTIFICATION_POSITION']; ?>',
			theme: '<?= $arResult['MODULE_SETTINGS']['NOTIFICATION_THEME']; ?>',
			textType: '<?= $arResult['MODULE_SETTINGS']['NOTIFICATION_TEXT_TYPE']; ?>',
		}
	<?php } else { ?>
		const notification = {
			enable: false,
		}
	<?php } ?>

	<?php if ($arResult['MODULE_SETTINGS']['BUTTON_FAST'] === 'Y') { ?>
		params.fastLogin = {
			enable: true,
			timeout: 1000,
			mode: 'default',
		};
	<?php } ?>

	<?php if ($arResult['MODULE_SETTINGS']['BUTTON_PERSONAL'] === 'Y') { ?>
		params.personalization = true;
		params.changeUser = true;
	<?php } ?>

	<?php if ($arResult['MODULE_SETTINGS']['USE_WEB2APP'] === 'Y') { ?>
		params.mweb2app = true;
	<?php } ?>

	function onSuccessCallback(result) {
		const params = {
			nonce: oidcParams.nonce,
			code: result.code,
		}

		fetch('<?= $arResult['AUTH_PARAMS']['redirect_uri_base']; ?>?FAST_LOGIN=1&' + new URLSearchParams(params)).then(function (response) {
			return response.json();
		}).then(function (params) {
			if (params.auth_status == 1) {
				location.reload();
			} else if (params.auth_status == 'login') {
				window.location.replace("<?= $arResult['AUTH_PARAMS']['redirect_uri_base']; ?>?continue_auth=1");

			} else if (params.auth_status == 'email') {
				window.location.replace("<?= $arResult['AUTH_PARAMS']['redirect_uri_base']; ?>?continue_auth=email");
			} else {
				console.log('cms: auth error')
			}
		});
	}

	function onErrorCallback(result) {
		console.log('Что-то пошло не так: ', result)
	}

	function sberIdInit() {
		var sbSDK = new SberidSDK({ ...params, notification }).init();
	};
</script>