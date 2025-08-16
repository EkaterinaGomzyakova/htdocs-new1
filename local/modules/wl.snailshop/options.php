<?

use Bitrix\Main\Application;

if (!$USER->IsAdmin())
    return;

global $APPLICATION;
IncludeModuleLangFile(__FILE__);
$request = Application::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);
$lang = LANGUAGE_ID;
$url = "{$APPLICATION->getCurPage()}?mid={$module_id}&lang={$lang}";

// build page
$aTabs = [
    [
        "DIV" => "edit_1",
        "TAB" => "Общие настройки",
        "TITLE" => "Общие настройки",
        "ICON" => 'tbIcon',
        "OPTIONS" => [
            ['roulete_active_to_admins_only', '[Рулетка] Активна только для сотрудников', '', ['checkbox', '']],
            ['roulete_date_active_from', '[Рулетка] Активна с', '30.10.2022', ['date', 10]],
            ['roulete_date_active_to', '[Рулетка] Активна до', '30.10.2022', ['date', 10]],
            ['roulete_order_date_payed', '[Рулетка] Начальная дата Оплаты заказов', '30.10.2022', ['date', 10]],
            ['roulete_order_sum', '[Рулетка] Сумма заказа для участия', '2000', ['text', 10]],
            ['basket_comment_is_active', 'Показать поле "Комментарий в корзине", которое добавляет текст в комментарий при оформлении заказа', '', ['checkbox', '']],
        ]
    ],
    [
        "DIV" => "edit_2",
        "TAB" => "Реферальная программа",
        "TITLE" => "Реферальная программа",
        "ICON" => 'tbIcon',
        "OPTIONS" => [
            ['referal_is_active', 'Активна реферальная программа', '', ['checkbox', '']],
            ['referal_xml_id_basket_rule', 'Внешний код правила корзины, участвующий в реферальной программе', '', ['text', 20]],
            ['referal_minimum_order_amount', 'Минимальная сумма заказа от которой начисляются баллы', '', ['text', 10]],
            ['referal_number_points', 'Количество начисляемых баллов', '', ['text', 10]],
        ]
    ],
    [
        "DIV" => "edit_3",
        "TAB" => "Начислять бонусные баллы равные сумме доставки заказа",
        "TITLE" => "Начислять бонусные баллы равные сумме доставки заказа",
        "ICON" => 'tbIcon',
        "OPTIONS" => [
            ['bonus_points_from_order_delivery_amount', 'Начислять бонусные баллы равные сумме доставки заказа', '', ['checkbox', '']],
            ['bonus_points_from_order_delivery_amount_min_order_sum', 'Минимальная сумма заказа', '', ['text', 10]],
            ['bonus_points_from_order_delivery_amount_date_beginning', 'Дата начала акции', '', ['date', 10]],
            ['bonus_points_from_order_delivery_amount_date_end', 'Дата окончания акции', '', ['date', 10]],
        ]
    ],
];

if (!empty($strError) || !empty($strOk)) {
    CAdminMessage::ShowMessage(array(
        "DETAILS" => !empty($strError) ? $strError : $strOk,
        "TYPE" => !empty($strError) ? 'ERROR' : 'OK',
    ));
}

$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<? $tabControl->Begin(); ?>
<form method="post" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= $_REQUEST['mid'] ?>&lang=<? echo LANGUAGE_ID ?>">
    <? foreach ($aTabs as $aTab) {
        $tabControl->BeginNextTab();

        foreach($aTab['OPTIONS'] as $option) {
            if($option[3][0] == 'date') { ?>
                <tr>
                    <td width="40%" nowrap><?=$option[1]?></td>
                    <td width="60%"><? echo CalendarDate($option[0], COption::GetOptionString("wl.snailshop", $option[0], ""), "cleanupform", $option[3][1]) ?></td>
                </tr>
            <? } else {
                __AdmSettingsDrawRow($module_id, $option);
            }
        }
    } ?>
    
    <?
    $tabControl->Buttons(array(
        "disabled" => false,
        "back_url" => $_REQUEST["back_url"]
    ));
    ?>
    <?= bitrix_sessid_post(); ?>
</form>
<? $tabControl->End(); ?>

<? if ($request->isPost() && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        __AdmSettingsSaveOptions($module_id, $aTab['OPTIONS']);
    }

    LocalRedirect($url);
} ?>