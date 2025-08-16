<div style="display: flex; align-items: center; justify-content: space-between">
    <div>
        <?

        ?>
        <? $APPLICATION->IncludeComponent(
            'bitrix:main.ui.filter',
            '',
            [
                'FILTER_ID' => 'fb_filter',
                'GRID_ID' => $gridID,
                'FILTER' => [
                    [
                        'id' => 'DATE',
                        'name' => 'Дата последнего заказа',
                        'type' => 'date',
                        "exclude" => [
                            \Bitrix\Main\UI\Filter\DateType::LAST_7_DAYS,
                            \Bitrix\Main\UI\Filter\DateType::LAST_30_DAYS,
                            \Bitrix\Main\UI\Filter\DateType::LAST_60_DAYS,
                            \Bitrix\Main\UI\Filter\DateType::LAST_90_DAYS,
                            \Bitrix\Main\UI\Filter\DateType::CURRENT_WEEK,
                            \Bitrix\Main\UI\Filter\DateType::YESTERDAY,
                            \Bitrix\Main\UI\Filter\DateType::CURRENT_DAY,
                            \Bitrix\Main\UI\Filter\DateType::TOMORROW,
                            \Bitrix\Main\UI\Filter\DateType::CURRENT_MONTH,
                            \Bitrix\Main\UI\Filter\DateType::CURRENT_QUARTER,
                            \Bitrix\Main\UI\Filter\DateType::PREV_DAYS,
                            \Bitrix\Main\UI\Filter\DateType::NEXT_DAYS,
                            \Bitrix\Main\UI\Filter\DateType::MONTH,
                            \Bitrix\Main\UI\Filter\DateType::QUARTER,
                            \Bitrix\Main\UI\Filter\DateType::YEAR,
                            \Bitrix\Main\UI\Filter\DateType::LAST_WEEK,
                            \Bitrix\Main\UI\Filter\DateType::LAST_MONTH,
                            \Bitrix\Main\UI\Filter\DateType::NEXT_MONTH,
                            \Bitrix\Main\UI\Filter\DateType::NEXT_WEEK,
                        ]
                    ],
                ],
                'ENABLE_LIVE_SEARCH' => false,
                'ENABLE_LABEL' => false,
                "FILTER_PRESETS" => [
                    "orders90_1" => [
                        "name" => 'Заказы старше 90 дней',
                        "default" => true,
                        "fields" => [
                            "DATE_datesel" => 'RANGE',
                            "DATE_from" => '',
                            'DATE_to' => (new DateTime('-90 days'))->format('d.m.Y')
                        ]
                    ]
                ]
            ]
        );
        ?>
    </div>
    <? require_once('tpl_export.php'); ?>
</div>
