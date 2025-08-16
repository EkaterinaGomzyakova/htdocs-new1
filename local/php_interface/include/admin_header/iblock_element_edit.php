<script src="/local/admin/js/jquery.autocomplete.min.js"></script>
<script>
    <?
        $arVolumeVariants = [];
        $arVolumeUsedValues = [];
        $dbVolumeVariants = CIBlockElement::GetList([], ['IBLOCK_ID' => GOODS_IBLOCK_ID], false, false, ['PROPERTY_VOLUME']);
        while($arVolumeVariant = $dbVolumeVariants->Fetch()) {
            if(array_search($arVolumeVariant['PROPERTY_VOLUME_VALUE'], $arVolumeUsedValues) === false) {
                $arVolumeVariants[] = ['value' => $arVolumeVariant['PROPERTY_VOLUME_VALUE']];
                $arVolumeUsedValues[] = $arVolumeVariant['PROPERTY_VOLUME_VALUE'];
            }
        }
    ?>
    var volumes = <?= CUtil::PhpToJsObject($arVolumeVariants)?>;
    setTimeout(function() {
        jQuery("#tr_PROPERTY_47 input[type=text]").autocomplete({lookup: volumes});
    }, 1500);
</script>
<?
