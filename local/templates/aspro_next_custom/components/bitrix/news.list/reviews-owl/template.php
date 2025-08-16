<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<? if (!empty($arResult["ITEMS"])) { ?>
    <h3><?= GetMessage('YOUR_PRODUCTS'); ?></h3>
    <div id="js-reviews-carousel" class="owl-carousel owl-carousel-reviews reviews-carousel">
        <? foreach ($arResult["ITEMS"] as $arItem) { ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div class="owl-carousel-reviews-item">
                <div>
                    <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><img src="<?= $arItem['PREVIEW_PICTURE']['RESIZE_SRC'] ?>" /></a>
                </div>
                <div class="owl-carousel-reviews-wrap">
                    <div class="owl-carousel-reviews-title"><a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><?= $arItem["NAME"] ?></a>
                    </div>
                    <a class="btn btn-default"
                        href="<?= $arItem["DETAIL_PAGE_URL"] ?>?open_review=Y"><?= GetMessage('LEAVE_REVIEW'); ?></a>
                </div>
            </div>
        <? } ?>
    </div>
<? } ?>