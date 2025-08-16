<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
$this->setFrameMode(true);

/** @var array $arParams */
/** @var array $arResult */
?>
<div class="row brands-switch-list">
    <?php
    foreach ($arResult['FILTER'] as $key => $filter) {
        ?>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
            <div class="item">
                <div class="name">
                    <a href="#" class="js-brands-nav-filter" data-filter="<?= $key ?>">
                        <?= $filter['CAPTION'] ?><img class="brand-icon" src="<?= $filter['ICON'] ?>"
                            alt="<?= $filter['CAPTION'] ?>" />
                    </a>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<ul class="brands-first-char-list">
    <li><a href="#" class="js-brands-start" data-symb="all">Все бренды</a></li>
    <?php
    foreach ($arResult['NAV_START_EN'] as $symb => $count) {
        ?>
        <li class="js-brand-nav-en">
            <?php
            if ($count > 0) {
                ?>
                <a href="#" class="js-brands-start" data-symb="<?= $symb ?>"><?= $symb ?></a>
                <?php
            } else {
                ?>
                <span><?= $symb ?></span>
                <?php
            }
            ?>
        </li>
        <?php
    }
    foreach ($arResult['NAV_START_RU'] as $symb => $count) {
        ?>
        <li class="js-brand-nav-ru" style="display: none">
            <?php
            if ($count > 0) {
                ?>
                <a href="#" class="js-brands-start" data-symb="<?= $symb ?>"><?= $symb ?></a>
                <?php
            } else {
                ?>
                <span><?= $symb ?></span>
                <?php
            }
            ?>
        </li>
        <?php
    }
    ?>
    <li><a href="#" class="js-brands-nav-ru">А - Я</a></li>
    <li><a href="#" class="js-brands-nav-en" style="display: none">A - Z</a></li>
</ul>
<?php
if ($arResult["ITEMS"]) {
    ?>
    <div data-brand-not-found style="display: none">
        К сожалению, по вашему запросу ничего не найдено.
    </div>
    <div class="brand_wrapper" data-brand-is-found>
        <?php
        if ($arParams["TITLE_BLOCK"] || $arParams["TITLE_BLOCK_ALL"]) {
            ?>
            <div class="top_block">
                <h3 class="title_block"><?= $arParams["TITLE_BLOCK"]; ?></h3>
                <a href="<?= SITE_DIR . $arParams["ALL_URL"]; ?>"><?= $arParams["TITLE_BLOCK_ALL"]; ?></a>
            </div>
            <?php
        }
        ?>
        <div class="margin0">
            <div class="brands_list brands_list--with-headers">
                <?php
                foreach ($arResult["ITEMS"] as $arItem) {
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    if ($arItem['START_GROUP']) {
                        ?>
                        <div class="group" data-partner-group><?= $arItem['START_GROUP'] ?></div>
                        <?php
                    }
                    ?>
                    <div class="item js-partner-item partner-item" data-fiter-set="<?= $arItem['FILTER_DATA'] ?>">
                        <div id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                            <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                                <span><?= $arItem["NAME"] ?></span><?php
                                  foreach ($arItem['FILTER'] as $filter) {
                                      if ($arResult['FILTER'][$filter]) {
                                          ?><img class="brand-icon"
                                            src="<?= $arResult['FILTER'][$filter]['ICON'] ?>"
                                            alt="<?= $arResult['FILTER'][$filter]['CAPTION'] ?>" /><?php
                                      }
                                  }
                                  ?>
                            </a>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}
?>