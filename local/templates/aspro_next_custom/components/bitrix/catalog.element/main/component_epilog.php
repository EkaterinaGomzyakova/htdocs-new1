<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
__IncludeLang($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/lang/" . LANGUAGE_ID . "/template.php");

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

?>
<? if ($arResult["ID"]): ?>
    <div id="reviews_content">
        <? $APPLICATION->IncludeComponent(
            "khayr:main.comment",
            "clanbeauty",
            array(
                "OBJECT_ID" => $arResult["ID"],
                "COUNT" => "50",
                "MAX_DEPTH" => "2",
                "JQUERY" => "N",
                "MODERATE" => "Y",
                "LEGAL" => "N",
                "LEGAL_TEXT" => "Я согласен с правилами размещения сообщений на сайте.",
                "CAN_MODIFY" => "N",
                "NON_AUTHORIZED_USER_CAN_COMMENT" => "N",
                "REQUIRE_EMAIL" => "N",
                "USE_CAPTCHA" => "Y",
                "AUTH_PATH" => "/auth/",
                "ACTIVE_DATE_FORMAT" => "j F Y, G:i",
                "LOAD_AVATAR" => "N",
                "LOAD_MARK" => "Y",
                "LOAD_DIGNITY" => "Y",
                "LOAD_FAULT" => "Y",
                "ADDITIONAL" => ["AGE", "SKIN_TYPE"],
                "ALLOW_RATING" => "N",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "Y",
                "PAGER_TITLE" => "",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => "",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N"
            )
        ); ?>
    </div>
    <script>
        $("#reviews_content").appendTo($('#js-review-target'));
    </script>

    <? if (($arParams["SHOW_ASK_BLOCK"] == "Y") && (intVal($arParams["ASK_FORM_ID"]))): ?>
        <div id="ask_block_content">
            <? $APPLICATION->IncludeComponent(
                "bitrix:form.result.new",
                "inline",
                array(
                    "WEB_FORM_ID" => $arParams["ASK_FORM_ID"],
                    "IGNORE_CUSTOM_TEMPLATE" => "N",
                    "USE_EXTENDED_ERRORS" => "N",
                    "SEF_MODE" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600000",
                    "LIST_URL" => "",
                    "EDIT_URL" => "",
                    "SUCCESS_URL" => "?send=ok",
                    "CHAIN_ITEM_TEXT" => "",
                    "CHAIN_ITEM_LINK" => "",
                    "VARIABLE_ALIASES" => array("WEB_FORM_ID" => "WEB_FORM_ID", "RESULT_ID" => "RESULT_ID"),
                    "AJAX_MODE" => "Y",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "Y",
                    "AJAX_OPTION_HISTORY" => "N",
                    "SHOW_LICENCE" => CNext::GetFrontParametrValue('SHOW_LICENCE'),
                    "HIDE_SUCCESS" => "Y",
                )
            ); ?>
        </div>
    <? endif; ?>
    <script type="text/javascript">
        if ($(".specials_tabs_section.specials_slider_wrapp").length && $("#reviews_content").length) {
            $("#reviews_content").after($(".specials_tabs_section.specials_slider_wrapp"));
        }
        if ($("#ask_block_content").length && $("#ask_block").length) {
            $("#ask_block_content").appendTo($("#ask_block"));
        }
        if ($(".gifts").length && $("#reviews_content").length) {
            $(".gifts").insertAfter($("#reviews_content"));
        }
        if ($("#reviews_content").length && !$(".tabs .tab-content .active").length) {
            $(".shadow.common").hide();
            $("#reviews_content").show();
        }
        if (!$(".stores_tab").length) {
            $('.item-stock .store_view').removeClass('store_view');
        }
    </script>
<? endif; ?>
<? if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY'])) {
    $loadCurrency = false;
    if (!empty($templateData['CURRENCIES']))
        $loadCurrency = Loader::includeModule('currency');
    CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
    if ($loadCurrency) {
        ?>
        <script type="text/javascript">
            BX.Currency.setCurrencies(<? echo $templateData['CURRENCIES']; ?>);
        </script>
    <?
    }
} ?>
<script type="text/javascript">
    var viewedCounter = {
        path: '/bitrix/components/bitrix/catalog.element/ajax.php',
        params: {
            AJAX: 'Y',
            SITE_ID: "<?= SITE_ID ?>",
            PRODUCT_ID: "<?= $arResult['ID'] ?>",
            PARENT_ID: "<?= $arResult['ID'] ?>"
        }
    };
    BX.ready(
        BX.defer(function () {
            $('body').addClass('detail_page');
            <?//if(!isset($templateData['JS_OBJ'])){ ?>
            BX.ajax.post(
                viewedCounter.path,
                viewedCounter.params
            );
            <?//} ?>
            if ($('.stores_tab').length) {
                var objUrl = parseUrlQuery(),
                    add_url = '';
                if ('clear_cache' in objUrl) {
                    if (objUrl.clear_cache == 'Y')
                        add_url = '?clear_cache=Y';
                }
                $.ajax({
                    type: "POST",
                    url: arNextOptions['SITE_DIR'] + "ajax/productStoreAmount.php" + add_url,
                    data: <?= CUtil::PhpToJSObject($templateData["STORES"], false, true, true) ?>,
                    success: function (data) {
                        var arSearch = parseUrlQuery();
                        $('.tab-content .tab-pane .stores_wrapp').html(data);
                        if ("oid" in arSearch)
                            $('.stores_tab .sku_stores_' + arSearch.oid).show();
                        else
                            $('.stores_tab .stores_wrapp > div:first').show();

                    }
                });
            }
        })
    );
</script>
<? if ($_REQUEST && isset($_REQUEST['formresult'])): ?>
    <script>
        $(document).ready(function () {
            if ($('#ask_block .form_result').length) {
                $('.product_ask_tab').trigger('click');
            }
        });
    </script>
<? endif; ?>
<? if (isset($_GET["RID"])) { ?>
    <? if ($_GET["RID"]) { ?>
        <script>
            $(document).ready(function () {
                $("<div class='rid_item' data-rid='<?= htmlspecialcharsbx($_GET["RID"]); ?>'></div>").appendTo($('body'));
            });
        </script>
    <? } ?>
<? } ?>

<? $wishItem = \WL\WishList::checkItem($arResult['ID']);
?>
<script>
    $(document).ready(function () {
        <? if (!empty($wishItem)): ?>
            $('.catalog_detail .wish_item').addClass('added');
            $('.catalog_detail .wish_item .value').hide();
            $('.catalog_detail .wish_item .value.added').show();
        <? endif; ?>
    });
</script>

<? if ($_GET['open_review'] == "Y") { ?>
    <script>
        $('.product_reviews_tab>a').click();
        $('html, body').animate({
            scrollTop: $("#review").offset().top
        }, 600);
    </script>
<? } ?>