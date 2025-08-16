<?php

IncludeModuleLangFile(__FILE__);
global $USER;
if (WL\SnailShop::userIsShopAdmin()) {
    $aMenu = [];
    $salary_filter = '?set_filter=Y&filter_status_date_from_FILTER_PERIOD=month&filter_status_date_from_FILTER_DIRECTION=current&filter_status_date_from=' . date('01.m.Y');
    $order_coupons_filter = '?set_filter=Y&filter_status_date_from_FILTER_PERIOD=month&filter_status_date_from_FILTER_DIRECTION=current&filter_status_date_from=' . date('01.m.Y') . '&by=DATE_INSERT&order=desc';
    $profit_filter = '?set_filter=Y&filter_date_payed_from=' . date('01.m.Y');
    $daily_report_filter = '?set_filter=Y&adm_filter_applied=0&filter_insert_date_from_FILTER_PERIOD=day&filter_insert_date_from_FILTER_DIRECTION=previous&filter_insert_date_from=' . date('d.m.Y') . '&filter_insert_date_to=' . date('d.m.Y');
    $shipments_filter = $salary_filter;
    $payment_filter = $salary_filter;

    $aMenu[] = [
        'parent_menu' => 'global_menu_store',
        'sort'        => 600,
        'text'        => "Дополнительные отчеты",
        'title'       => "Дополнительные отчеты",
        'icon'        => 'sale_menu_icon_statistic',
        'page_icon'   => 'sale_menu_icon_statistic',
        'items_id'    => 'menu_wl',
        'module_id'      => 'custom.reports',
        'items'       => [
            [
                'sort'        => 100,
                'text'        => "Ежедневный отчет",
                'title'       => "Ежедневный отчет",
                'url'         => 'wl_daily_report.php' . $daily_report_filter,
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Купоны - сводная таблица",
                'title'       => "Купоны - сводная таблица",
                'url'         => 'wl_order_coupons_summary.php' . $order_coupons_filter,
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Купоны, примененные в заказах",
                'title'       => "Купоны, примененные в заказах",
                'url'         => 'wl_order_coupons.php' . $order_coupons_filter,
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "История складских остатков",
                'title'       => "История складских остатков",
                'url'         => 'wl_products_quantity.php',
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Сравнение отгрузок и заказов",
                'title'       => "Сравнение отгрузок и заказов",
                'url'         => 'wl_shipments.php' . $shipments_filter,
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Продажи сотрудников за период",
                'title'       => "Продажи сотрудников за период",
                'url'         => 'wl_orders_salary.php' . $salary_filter,
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Продажи сотрудников в розницу",
                'title'       => "Продажи сотрудников в розницу",
                'url'         => 'wl_orders_salary_retail.php' . $salary_filter,
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Продажи сотрудников за период (2023)",
                'title'       => "Продажи сотрудников за период (2023)",
                'url'         => 'wl_orders_salary_2023.php' . $salary_filter,
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Способы оплаты за период",
                'title'       => "Способы оплаты за период",
                'url'         => 'wl_payments.php' . $payment_filter,
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Способы доставки за период",
                'title'       => "Способы доставки за период",
                'url'         => 'wl_deliveries.php' . $payment_filter,
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Инвентаризация",
                'title'       => "Инвентаризация",
                'url'         => 'wl_inventorisation.php',
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Отчет по предельному количеству товара",
                'title'       => "Отчет по предельному количеству товара",
                'url'         => 'wl_maximum_quantity_goods.php',
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Накопительные скидки покупателей",
                'title'       => "Накопительные скидки покупателей",
                'url'         => 'wl_user_cumulative_discounts.php',
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Самые комментируемые товары",
                'title'       => "Самые комментируемые товары",
                'url'         => 'wl_most_commented_products.php',
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 110,
                'text'        => "История цен",
                'title'       => "История цен",
                'url'         => 'wl_history_price.php',
                'module_id'      => 'wl.snailshop',
            ],
            [
                'sort'        => 120,
                'text'        => "Наценки и Установка розничных цен",
                'title'       => "Наценки и Установка розничных цен",
                'url'         => 'wl_purchase_price.php',
                'module_id'      => 'wl.snailshop',
            ],
            [
                'sort'        => 120,
                'text'        => "Продажи за период",
                'title'       => "Продажи за период",
                'url'         => 'wl_sales_for_the_period.php',
                'module_id'      => 'wl.snailshop',
            ],
            [
                'sort'        => 120,
                'text'        => "[Рулетка] Список победителей",
                'title'       => "[Рулетка] Список победителей",
                'url'         => 'wl_roulete_winners.php',
                'module_id'      => 'wl.snailshop',
            ],
            [
                'sort'        => 120,
                'text'        => "Средний чек и кол-во товара за период",
                'title'       => "Продажи за период",
                'url'         => 'wl_median_check_for_the_period.php',
                'module_id'      => 'wl.snailshop',
            ],
            [
                'sort'        => 130,
                'text'        => "Дисбаланс товаров на складах",
                'title'       => "Дисбаланс товаров на складах",
                'url'         => 'wl_store_products_disbalance.php',
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 130,
                'text'        => "Товары, которые давно не продавались",
                'title'       => "Товары, которые давно не продавались",
                'url'         => 'wl_not_selling_products.php',
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 135,
                'text'        => "Подписки покупателей на складах",
                'title'       => "Подписки покупателей на складах",
                'url'         => 'wl_buyer_products_subscription.php',
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Доходность по товарам за период",
                'title'       => "Доходность по товарам за период",
                'url'         => 'wl_profit_by_goods.php' . $profit_filter,
                'module_id'      => 'custom.reports',
            ],
            [
                'sort'        => 100,
                'text'        => "Источники покупателей",
                'title'       => "Источники покупателей",
                'url'         => 'wl_buyer_source_stats.php?set_filter=Y&filter_source=advice',
                'module_id'      => 'custom.reports',
            ]
        ]
    ];

    $aMenu[] = [
        'parent_menu' => 'global_menu_marketing',
        'sort'        => 600,
        'text'        => "Маркетинговые инструменты Facebook",
        'title'       => "Маркетинговые инструменты Facebook",
        'icon'        => 'cb_audience_facebook',
        'page_icon'   => 'cb_audience_facebook',
        'items_id'    => 'cb_marketing_tools',
        'module_id'      => 'wl.snailshop',
        'items'       => [
            0 => [
                'sort'        => 100,
                'text'        => "Пользователи не совершавшие заказы N дней",
                'title'       => "Пользователи не совершавшие заказы N дней",
                'url'         => 'cb_audience_facebook.php',
                'module_id'      => 'wl.snailshop',
                'icon'        => '',
            ],
            1 => [
                'sort'        => 200,
                'text'        => "Пользователи с брошенными корзинами",
                'title'       => "Пользователи с брошенными корзинами",
                'url'         => 'cb_abandonedcart_facebook.php',
                'module_id'      => 'wl.snailshop',
                'icon'        => '',
            ],
        ]
    ];

    $aMenu[] = [
        'parent_menu' => 'global_menu_store',
        'sort'        => 200,
        'text'        => "Отчеты по заказам",
        'title'       => "Отчеты по заказам",
        'icon'        => 'sale_menu_icon_orders',
        'page_icon'   => 'sale_menu_icon_orders',
        'items_id'    => 'menu_order',
        'module_id'      => 'custom.reports',
        'items' => [
            0 => [
                'sort'        => 100,
                'text'        => "Оплаты по магазинам",
                'title'       => "Оплаты по магазинам",
                'url'         => 'wl_order_payment.php',
                'module_id'      => 'wl.snailshop',
                'icon'        => '',
            ],
            1 => [
                'sort'        => 100,
                'text'        => "Оплаты по дням",
                'title'       => "Оплаты по дням",
                'url'         => 'wl_order_payment_daily.php?set_filter=Y&filter_date_paid_from=' . date('01.m.Y'),
                'module_id'   => 'wl.snailshop',
                'icon'        => '',
            ],
        ]
    ];
}

return !empty($aMenu) ? $aMenu : false;
