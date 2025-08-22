<? if(!empty($arResult['ITEMS'])) {?>
    <div class="maxwidth-theme">
        <div class="skin-types-scroller">
            <div class="skin-types-container">
                <?foreach($arResult['ITEMS'] as $arItem) {?>
                    <a class="skin-types-item" href="<?= $arItem['PROPERTIES']['LINK']['VALUE']?>">
                        <div class="image" style="background-image: url('<?=CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=>180, 'height'=>180), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'];?>');"></div>
                        <div class="title"><?= $arItem['NAME']?></div>
                    </a>
                <? } ?>
            </div>
        </div>
    </div>
<? } ?>