<?php
    foreach ($arResult["ITEMS"] as $key => $arItem) {
        $file = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], ['width' => 160, 'height' => 160],
            BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
        $arResult["ITEMS"][$key]['PREVIEW_PICTURE']['RESIZE_SRC'] = $file['src'];
    }
