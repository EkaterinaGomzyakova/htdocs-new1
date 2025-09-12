/* ================================================================ */
/* ============== ФУНКЦИИ ДЛЯ ОЧИСТКИ ВСЕЙ КОРЗИНЫ ================ */
/* ================================================================ */

function clearAllBasketItems() {
	// Показываем красивое модальное окно
	showClearBasketModal();
	return false;
}

function showClearBasketModal() {
	var modal = document.getElementById('clear-basket-modal');
	if (modal) {
		modal.style.display = 'flex';
		modal.classList.add('active');
		document.body.classList.add('modal-active');
		document.body.style.overflow = 'hidden'; // Блокируем прокрутку страницы
	}
}

function closeClearBasketModal() {
	var modal = document.getElementById('clear-basket-modal');
	if (modal) {
		modal.style.display = 'none';
		modal.classList.remove('active');
		document.body.classList.remove('modal-active');
		document.body.style.overflow = ''; // Восстанавливаем прокрутку
	}
}

function confirmClearBasket() {
	// Закрываем модальное окно
	closeClearBasketModal();
	
	// Выполняем очистку корзины
	performClearBasket();
}

function performClearBasket() {
	console.log('Начинаем очистку корзины...');
	
	// Ищем товары в корзине разными способами
	var basketItems = [];
	
	// Способ 1: Ищем через компонент Bitrix
	if (window.BX && window.BX.Sale && window.BX.Sale.BasketComponent) {
		var basketComponent = window.BX.Sale.BasketComponent;
		console.log('Найден компонент корзины:', basketComponent);
		
		if (basketComponent.items) {
			for (var itemId in basketComponent.items) {
				if (basketComponent.items.hasOwnProperty(itemId)) {
					basketItems.push(itemId);
					console.log('Найден товар в компоненте:', itemId);
				}
			}
		}
	}
	
	// Способ 2: Ищем в таблице корзины
	if (basketItems.length === 0) {
		var basketTable = document.getElementById('basket-item-table');
		console.log('Ищем в таблице:', basketTable);
		
		if (basketTable && basketTable.rows) {
			for (var i = 0; i < basketTable.rows.length; i++) {
				var row = basketTable.rows[i];
				if (row.id && row.id !== '' && row.id !== 'undefined') {
					basketItems.push(row.id);
					console.log('Найден товар в таблице:', row.id);
				}
			}
		}
	}
	
	// Способ 3: Ищем по классам товаров
	if (basketItems.length === 0) {
		var itemContainers = document.querySelectorAll('.basket-items-list-item-container');
		console.log('Найдено контейнеров товаров:', itemContainers.length);
		
		for (var j = 0; j < itemContainers.length; j++) {
			if (itemContainers[j].id) {
				basketItems.push(itemContainers[j].id);
				console.log('Найден товар по классу:', itemContainers[j].id);
			}
		}
	}
	
	// Способ 4: Ищем все строки таблицы с ID
	if (basketItems.length === 0) {
		var allRows = document.querySelectorAll('tr[id]');
		console.log('Найдено строк с ID:', allRows.length);
		
		for (var k = 0; k < allRows.length; k++) {
			var rowId = allRows[k].id;
			if (rowId && rowId !== '' && rowId !== 'undefined' && !rowId.includes('header')) {
				basketItems.push(rowId);
				console.log('Найден товар в строке:', rowId);
			}
		}
	}
	
	console.log('Всего найдено товаров для удаления:', basketItems.length);
	console.log('Список товаров:', basketItems);
	
	if (basketItems.length === 0) {
		alert('Корзина уже пуста');
		return false;
	}
	
	// Показываем индикатор загрузки
	if (window.BX && window.BX.showWait) {
		BX.showWait();
	}
	
	// Создаем URL для удаления всех товаров
	var deleteUrls = [];
	for (var l = 0; l < basketItems.length; l++) {
		var deleteUrl = basketJSParams['DELETE_URL'].replace('#ID#', basketItems[l]);
		deleteUrls.push(deleteUrl);
		console.log('URL для удаления товара', basketItems[l], ':', deleteUrl);
	}
	
	// Удаляем товары по одному
	var deletedCount = 0;
	var totalItems = basketItems.length;
	
	function deleteNextItem() {
		if (deletedCount >= totalItems) {
			// Все товары удалены, перезагружаем страницу
			console.log('Все товары удалены, перезагружаем страницу');
			if (window.BX && window.BX.closeWait) {
				BX.closeWait();
			}
			window.location.reload();
			return;
		}
		
		var currentUrl = deleteUrls[deletedCount];
		console.log('Удаляем товар', deletedCount + 1, 'из', totalItems, ':', currentUrl);
		
		// Выполняем AJAX запрос для удаления товара
		if (window.BX && window.BX.ajax) {
			BX.ajax({
				url: currentUrl,
				method: 'GET',
				onsuccess: function() {
					deletedCount++;
					console.log('Товар удален успешно, переходим к следующему');
					// Небольшая задержка между запросами
					setTimeout(deleteNextItem, 200);
				},
				onfailure: function() {
					console.error('Ошибка при удалении товара:', currentUrl);
					if (window.BX && window.BX.closeWait) {
						BX.closeWait();
					}
					alert('Произошла ошибка при удалении товаров. Попробуйте еще раз.');
				}
			});
		} else {
			// Альтернативный способ через fetch
			fetch(currentUrl, {
				method: 'GET',
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
			.then(function(response) {
				if (response.ok) {
					deletedCount++;
					console.log('Товар удален успешно через fetch, переходим к следующему');
					setTimeout(deleteNextItem, 200);
				} else {
					throw new Error('Ошибка HTTP: ' + response.status);
				}
			})
			.catch(function(error) {
				console.error('Ошибка при удалении товара через fetch:', error);
				alert('Произошла ошибка при удалении товаров. Попробуйте еще раз.');
			});
		}
	}
	
	// Начинаем удаление
	deleteNextItem();
	
	return false;
}

// Добавляем обработчики для модального окна
document.addEventListener('DOMContentLoaded', function() {
	// Закрытие по клику на overlay
	var modal = document.getElementById('clear-basket-modal');
	if (modal) {
		var overlay = modal.querySelector('.clear-basket-modal-overlay');
		if (overlay) {
			overlay.addEventListener('click', closeClearBasketModal);
		}
	}
	
	// Закрытие по клавише Escape
	document.addEventListener('keydown', function(event) {
		if (event.key === 'Escape') {
			var modal = document.getElementById('clear-basket-modal');
			if (modal && modal.style.display === 'flex') {
				closeClearBasketModal();
			}
		}
	});
});
