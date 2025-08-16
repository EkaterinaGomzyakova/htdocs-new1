<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
?>

<div class="section_block">
    <div class="sections_wrapper brands-filter">
        <div class="list items">
            <div class="row margin0">
                <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                    <div class="col-md-3 col-sm-4 col-xs-6">
                        <div class="item <?php if ($arItem['ACTIVE']) echo 'active'; ?>" style="height: 80px;">
                            <div class="name">
                                <a href="<?= $arItem['LINK'] ?>" class="dark_link"><?= $arItem['NAME'] ?></a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
