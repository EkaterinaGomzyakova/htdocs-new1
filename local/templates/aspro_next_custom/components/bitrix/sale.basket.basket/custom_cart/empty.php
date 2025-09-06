<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
?>
<div class="bx-sbb-empty-cart-container">
	<!-- Заголовок корзины -->
	<div class="basket-main-header">
		<h2 class="basket-main-header__title">Корзина</h2>
	</div>
	
	<!-- Сообщение о пустой корзине -->
	<div class="bx-sbb-empty-cart-text">В корзине пока пусто</div>
	
	<div class="bx-sbb-empty-cart-desc">
		Посмотрите в каталоге, у нас много интересного!
	</div>
	
	<!-- Блок с товарами, которые ранее смотрели -->
	<div class="basket-recommendations-container">
		<?
		// Подключаем блок с просмотренными товарами как в основном индексе
		include($_SERVER['DOCUMENT_ROOT'] . "/include/footer/comp_viewed.php");
		?>
	</div>
</div>