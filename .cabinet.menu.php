<?
$aMenuLinks = Array(
	Array(
		"Мой кабинет", 
		"/personal/index.php", 
		Array(), 
		Array(), 
		"" 
	),
	array(
		"Избранное",
		"/personal/wishlist/",
		array(),
		array(),
		""
	),
    array(
        "Мои отзывы",
        "/personal/my-reviews/",
        array(),
        array(),
        ""
    ),
    Array(
        "Мои покупки",
        "/personal/shopping-list/",
        Array(),
        Array(),
        ""
    ),
	Array(
		"Мои заказы",
		"/personal/orders/",
		Array(),
		Array(),
		""
	),
	Array(
		"Личные данные", 
		"/personal/private/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Сменить пароль", 
		"/personal/change-password/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Выйти", 
		"?logout=yes&login=yes", 
		Array(), 
		Array("class"=>"exit", "BLOCK"=>"<i class='icons'><svg id='Exit.svg' xmlns='http://www.w3.org/2000/svg' width='8' height='8.031' viewBox='0 0 8 8.031'><path id='Rounded_Rectangle_82_copy_2' data-name='Rounded Rectangle 82 copy 2' class='cls-1' d='M333.831,608.981l2.975,2.974a0.6,0.6,0,0,1-.85.85l-2.975-2.974-2.974,2.974a0.6,0.6,0,0,1-.85-0.85l2.974-2.974-2.974-2.975a0.6,0.6,0,0,1,.85-0.849l2.974,2.974,2.975-2.974a0.6,0.6,0,0,1,.85.849Z' transform='translate(-329 -604.969)'/></svg></i>"), 
		"\$GLOBALS['USER']->IsAuthorized()" 
	),
	/*Array(
		"Личный счет", 
		"/personal/account/", 
		Array(), 
		Array(), 
		"CBXFeatures::IsFeatureEnabled('SaleAccounts')" 
	),
	Array(
		"Профили заказов", 
		"/personal/profiles/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Корзина", 
		"/basket/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Подписки", 
		"/personal/subscribe/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Контакты", 
		"/contacts/", 
		Array(), 
		Array(), 
		"" 
	),*/
);
?>