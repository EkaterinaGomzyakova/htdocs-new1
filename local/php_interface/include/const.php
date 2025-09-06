<?
//Инфоблоки
define("GOODS_IBLOCK_ID", 2);
define("SKU_IBLOCK_ID", 35);
define("OFFERS_IBLOCK_ID", 35); // ID инфоблока предложений (тот же что и SKU)

define('ADDITIONAL_CATALOG_IBLOCK_ID', 30);
define('FAVORITES_IBLOCK_ID', 31);
define('BRANDS_IBLOCK_ID', 15);
define('ARTICLES_IBLOCK_ID', 21);
define('BLOGGER_ARTICLES_IBLOCK_ID', 38);
define('REVIEWS_IBLOCK_ID', 32);

define("CATALOG_DISCOUNT_ACTION_VALUE_ID", 101);  //ID значения свойства Наши предложения->Скидка каталога
define("CATALOG_NOVELTY_ACTION_VALUE_ID", 100);  //ID значения свойства Наши предложения->Новинка каталога
define("CATALOG_HIT_VALUE_ID", 98);  //ID значения свойства Наши предложения->Хит

define("GIFT_CERTIFICATE_PRODUCT_ID", 1217);
define("GIFT_PACKAGE_PRODUCT_ID", 1343);

define('DELIVERY_PICKUP_ID', 3);

define('INNER_PAY_SYSTEM_ID', 10);
define('PAY_SYSTEMS_ID_EXCLUDE_FROM_SALARY', [15, 20]); //[Уточняется, Реклама]


define('COUNT_BONUS_POINTS', 0.5); //Какой процент бонусных баллов начисляется после выполнения заказа

define('MAX_PERCENT_POINTS_PAY', 5); //Какой процент от заказа можно оплатить баллами

define('COUNT_PROBLEMS', 20); // количество проблем в гаджете проверки

define('CUMULATIVE_DISCOUNT_EXCLUDE_BRANDS_ID_ARRAY',
[
    //2677, //Hempz
]);

/*
 * login: d.pantushin@weblipka.ru
 * password: gh3kvgu46x
 * */
define('DADATA_API_KEY', 'b55ec42d93f76c6a1edde93adcb1d26a4fd9b2cf');
define('DADATA_SECRET_KEY', '669e406a1eab5baacdad9648776a7eb8ffd193fd');

//Подарочные сертификаты
const GIFT_CODE = 'podarochnyy_sertifikat';
const GIFT_SECTION_CODE = 'gift-certificates';