<?php
$bUseMap = CNext::GetFrontParametrValue('CONTACTS_USE_MAP', SITE_ID) != 'N';
$bUseFeedback = CNext::GetFrontParametrValue('CONTACTS_USE_FEEDBACK', SITE_ID) != 'N';

if ($bUseMap) {
    ?>
    <div class="maxwidth-theme" style="border-radius: 0px 0px 10px 10px; overflow: hidden; padding: 0;">
        <?php
        $APPLICATION->IncludeFile(SITE_DIR . "include/contacts-site-map.php", array(), array("MODE" => "html", "TEMPLATE" => "include_area.php", "NAME" => "Карта"));
        ?>
    </div>
    <?php
}
?>

    <div class="contacts contacts-page-map-overlay maxwidth-theme" itemscope itemtype="http://schema.org/Organization">
        <div class="contacts-wrapper">
            <div class="row">
                <div class="col-md-3 col-sm-3 print-6">
                    <table cellpadding="0" cellspasing="0">
                        <tr>
                            <td align="left" valign="top"><i class="fa big-icon s45 fa-map-marker"></i></td>
                            <td align="left" valign="top"><span class="dark_table">Адрес</span>
                                <br/>
                                <span itemprop="address">
                                    <?php
                                    $APPLICATION->IncludeFile(SITE_DIR . "include/contacts-site-address.php", array(), array("MODE" => "html", "NAME" => "Address"));
                                    ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-3 col-sm-3 print-6">
                    <table cellpadding="0" cellspasing="0">
                        <tr>
                            <td align="left" valign="top"><i class="fa big-icon s45 fa-phone"></i></td>
                            <td align="left" valign="top"><span class="dark_table">Телефон</span>
                                <br>
                                <span itemprop="telephone">
                                    <?php
                                    $APPLICATION->IncludeFile(SITE_DIR . "include/contacts-site-phone.php", array(), array("MODE" => "html", "NAME" => "Phone"));
                                    ?>
                                </span>
                                <br>
                                <span itemprop="telephone">+7 920 248 88-98</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-3 col-sm-3 print-6">
                    <table cellpadding="0" cellspasing="0">
                        <tr>
                            <td align="left" valign="top"><i class="fa big-icon s45 fa-envelope"></i></td>
                            <td align="left" valign="top"><span class="dark_table">E-mail</span>
                                <br/>
                                <span itemprop="email">
                                    <?php
                                    $APPLICATION->IncludeFile(SITE_DIR . "include/contacts-site-email.php", array(), array("MODE" => "html", "NAME" => "Email"));
                                    ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-3 col-sm-3 print-6">
                    <table cellpadding="0" cellspasing="0">
                        <tr>
                            <td align="left" valign="top"><i class="fa big-icon s45 fa-clock-o"></i></td>
                            <td align="left" valign="top"><span class="dark_table">Режим работы</span>
                                <br/>
                                <?php
                                $APPLICATION->IncludeFile(SITE_DIR . "include/contacts-site-schedule.php", array(), array("MODE" => "html", "NAME" => "Schedule"));
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-xs-12 move-to-catalog">
                    <a class="btn social-btn social-btn-vk" target="_blank" href="https://vk.com/clanbeauty">VK</a>
                    <a class="btn social-btn social-btn-tg" target="_blank" href="https://t.me/clanbeauty">Telegram</a>
                    <a class="btn social-btn social-btn-wa" target="_blank" href="https://api.whatsapp.com/send?phone=79202488898">WhatsApp</a>
                </div>
            </div>
        </div>
    </div>

<div class="maxwidth-theme <?= $bUseMap ? 'top-cart' : ''?>" style="padding: 0px;">
	<div class="contacts-reviews">
		<iframe frameborder="0" width="100%" height="400px" src="https://www.tinkoff.ru/feedback-widget/?publicId=ME7npgqvDEhEBRlw&feedbacksCount=5&slideCapacity=12&ratingAlign=left"></iframe>
	</div>

	<div itemprop="description" class="mt-5">
		<h1 class="h3 mb-0 pb-0">Интернет-магазин косметики ClanBeauty.ru</h1>
		<p>
            <?php
            $APPLICATION->IncludeFile(SITE_DIR . "include/contacts-about.php", array(), array("MODE" => "html", "NAME" => "Contacts about"));
            ?>
        </p>
	</div>

	<div class="mt-5">
		<h3>ИП Ануфриева Ирина Сергеевна</h3>
		<div>
			ИНН: 482614116612<br>
			ОГРНИП: 320482700034770 от 09.09.2020<br>
			Адрес осуществления деятельности: г. Липецк, пр. Победы, 61Б
		</div>
	</div>
</div>

<?php
if ($bUseFeedback) {
    Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("contacts-form-block");
    global $arTheme;
    $APPLICATION->IncludeComponent(
		"bitrix:form.result.new",
		"inline",
		array(
			"WEB_FORM_ID" => "3",
			"IGNORE_CUSTOM_TEMPLATE" => "N",
			"USE_EXTENDED_ERRORS" => "Y",
			"SEF_MODE" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600000",
			"LIST_URL" => "",
			"EDIT_URL" => "",
			"SUCCESS_URL" => "?send=ok",
			"SHOW_LICENCE" => $arTheme["SHOW_LICENCE"]["VALUE"],
			"HIDDEN_CAPTCHA" => CNext::GetFrontParametrValue('HIDDEN_CAPTCHA'),
			"CHAIN_ITEM_TEXT" => "",
			"CHAIN_ITEM_LINK" => "",
			"VARIABLE_ALIASES" => array(
				"WEB_FORM_ID" => "WEB_FORM_ID",
				"RESULT_ID" => "RESULT_ID"
			)
		)
	);
    Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("contacts-form-block", "");
}
