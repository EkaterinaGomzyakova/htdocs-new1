<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var \CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var array $templateData */
/** @var \CBitrixComponent $component */
$this->setFrameMode(true);
$componentID = uniqid('cmp');
?>
<? if ($arResult['ITEM']['ID']) { ?>
    <div id="<?= $componentID ?>_popup" class="popup_message">
        <div class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                        <? if ($arResult['ITEM']['PREVIEW_TEXT']) { ?>
                            <div class="msg-content">
                                <? if (!empty($arResult['ITEM']['PREVIEW_PICTURE'])) { ?>
                                    <div class="msg-content__img">
                                        <img src="<?= $arResult['ITEM']['PREVIEW_PICTURE'] ?>" alt="" loading="lazy">
                                    </div>
                                <? } ?>

                                <p class="msg-content__title"><strong><?= $arResult['ITEM']['NAME'] ?></strong></p>
                                <div class="msg-content__text">
                                    <?= $arResult['ITEM']['PREVIEW_TEXT'] ?>
                                </div>

                                <? if (!empty($arResult['ITEM']['PROPERTIES']['BUTTON']['VALUE']) && !empty($arResult['ITEM']['PROPERTIES']['BUTTON']['DESCRIPTION'])) { ?>
                                    <div class="msg-content__btn-wrap">
                                        <a href="<?= $arResult['ITEM']['PROPERTIES']['BUTTON']['VALUE'] ?>" class="btn button <?= $componentID ?>-confirm"><?= $arResult['ITEM']['PROPERTIES']['BUTTON']['DESCRIPTION'] ?></a>
                                    </div>
                                <? } ?>
                            </div>

                        <? } else { ?>
                            <div class="msg-content-img mt-10">
                                <img src="<?= $arResult['ITEM']['PREVIEW_PICTURE'] ?>" alt="" loading="lazy">
                            </div>
                        <? } ?>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>

    <script>
        new PopupMessage({
            component_id: '<?= $componentID ?>',
            id: '<?= $arResult['ITEM']['ID'] ?>',
            sign_params: '<?= $arResult['SIGN_PARAMS'] ?>'
        });
    </script>
<? } ?>