<?php

namespace Clanbeauty\Properties;

use \Bitrix\Main\Loader;

class BasketRule
{
    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "basket_rule",
            "DESCRIPTION" => 'Привязка к правилам работы с корзиной',
            "GetPublicViewHTML" => array("Clanbeauty\Properties\BasketRule", "GetPublicViewHTML"),
            "GetAdminListViewHTML" => array("Clanbeauty\Properties\BasketRule", "GetAdminListViewHTML"),
            "GetPropertyFieldHtml" => array("Clanbeauty\Properties\BasketRule", "GetPropertyFieldHtml"),
            "ConvertToDB" => array("Clanbeauty\Properties\BasketRule", "ConvertToDB"),
            "ConvertFromDB" => array("Clanbeauty\Properties\BasketRule", "ConvertFromDB"),
        );
    }


    public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return '';
    }

    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return;
    }

    //отображение формы редактирования в админке и в режиме правки
    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        \Bitrix\Main\Loader::includeModule('sale');
        $discounts =  \Bitrix\Sale\Internals\DiscountTable::getList()->fetchAll();
        ob_start();
        ?>
        <div class="checkbox">
            <input type="checkbox"
                   value="Y"
                   id="basketRuleCheckbox"
                    <?if($value['VALUE'] > 0):?>checked<?endif;?>
            >
            <label for="basketRuleCheckbox">Подарочный сертификат</label>
        </div>
        <select <?if(empty($value['VALUE'])):?>style="display: none"<?endif;?> id="js-field-basket-rule" name="PROP[<?= $arProperty['ID'] ?>][VALUE]">
            <option value="">Выберите значение</option>
            <? foreach ($discounts as $key => $item): ?>
                <?
                $selected = '';
                if ($item['ID'] == $value['VALUE']) {
                    $selected = 'selected';
                }
                ?>
                <option value="<?= $item['ID'] ?>" <?= $selected ?>>[<?= $item['ID'] ?>] <?= $item['NAME'] ?></option>
            <? endforeach; ?>
        </select>
        <script>
            $('#basketRuleCheckbox').on('change', function () {
                if($(this).is(':checked')){
                    $('#js-field-basket-rule').show();
                }else {
                    $('#js-field-basket-rule').hide();
                    $('#js-field-basket-rule').val('');
                }
            });
        </script>
        <?
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    //Сохранение в БД
    public static function ConvertToDB($arProperty, $value)
    {
        $return = array("VALUE" => $value["VALUE"]);
        $return["DESCRIPTION"] = '';
        if (strlen(trim($value["DESCRIPTION"])) > 0) $return["DESCRIPTION"] = trim($value["DESCRIPTION"]);
        $value = $return;
        return $value;
    }

    //Извлечение из БД
    public static function ConvertFromDB($arProperty, $value)
    {
        $return = false;
        $return = array("VALUE" => $value["VALUE"]);
        return $return;
    }
}