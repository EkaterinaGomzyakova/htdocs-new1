<?php
/**
 * Тестовая страница для проверки исправленного выпадающего меню каталога
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->SetTitle("Тест выпадающего меню каталога");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>

<div class="container" style="padding: 20px;">
    <h1>Тест выпадающего меню каталога</h1>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3>Инструкции по тестированию:</h3>
        <ol>
            <li>Найдите кнопку "Каталог" в верхнем меню сайта (слева от логотипа)</li>
            <li><strong>Клик мышью</strong> - должно открыться выпадающее меню (НЕ переход на страницу каталога)</li>
            <li><strong>Проверьте URL</strong> - в адресной строке должно появиться "/catalog"</li>
            <li><strong>Нажмите Enter</strong> - должно открыться выпадающее меню</li>
            <li><strong>Нажмите Escape</strong> - должно закрыться меню и убрать "/catalog" из URL</li>
            <li><strong>Кнопка "Назад"</strong> - должна закрыть меню и вернуть исходный URL</li>
            <li>Проверьте, что меню содержит категории косметики</li>
            <li>Попробуйте закрыть меню кнопкой "×" или кликом вне меню</li>
        </ol>
    </div>
    
    <div style="background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;">
        <h4>Что было исправлено:</h4>
        <ul>
            <li>✅ <strong>JavaScript функции</strong> перенесены в header.php для раннего выполнения</li>
            <li>✅ <strong>HTML структура меню</strong> подключается в header.php</li>
            <li>✅ <strong>CSS стили</strong> корректно подключены</li>
            <li>✅ <strong>Обработчики событий</strong> добавлены с проверкой DOMContentLoaded</li>
            <li>✅ <strong>Правильная кнопка каталога</strong> - исправлена кнопка в header_2.php (header-catalog-button)</li>
            <li>✅ <strong>Поведение кнопки</strong> изменено - теперь открывает меню, а не переходит на страницу</li>
            <li>✅ <strong>Клавиатурная навигация</strong> - Enter открывает меню, Escape закрывает</li>
            <li>✅ <strong>Доступность</strong> - добавлены ARIA атрибуты для screen readers</li>
            <li>✅ <strong>Актуальные категории</strong> - обновлена структура каталога по данным с clanbeauty.ru</li>
            <li>✅ <strong>Изменение URL</strong> - при открытии меню в адресной строке добавляется "/catalog"</li>
            <li>✅ <strong>Навигация браузера</strong> - кнопка "Назад" закрывает меню и возвращает исходный URL</li>
            <li>✅ <strong>Полноэкранный дизайн</strong> - меню занимает всю ширину и высоту экрана</li>
            <li>✅ <strong>Белый фон с зелеными акцентами</strong> - чистый белый фон с фирменным зеленым цветом ClanBeauty (#1a4d3a)</li>
            <li>✅ <strong>Современный дизайн</strong> - добавлены тени, анимации и плавные переходы</li>
        </ul>
    </div>
    
    <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;">
        <h4>Если меню не работает:</h4>
        <ul>
            <li>Проверьте консоль браузера на наличие ошибок JavaScript</li>
            <li>Убедитесь, что файл catalog_dropdown.css загружается</li>
            <li>Проверьте, что элемент с id="catalogDropdownMenu" существует на странице</li>
        </ul>
    </div>
    
    <div style="margin: 20px 0;">
        <h3>Проверка элементов на странице:</h3>
        <div id="debug-info" style="background: #f8f9fa; padding: 15px; border-radius: 4px; font-family: monospace;">
            <div>Проверка элементов...</div>
        </div>
    </div>
</div>

<script>
// Проверка элементов на странице
document.addEventListener('DOMContentLoaded', function() {
    const debugInfo = document.getElementById('debug-info');
    let info = [];
    
    // Проверяем наличие кнопки каталога
    const catalogButton = document.querySelector('.header-catalog-button');
    if (catalogButton) {
        info.push('✅ Кнопка каталога (.header-catalog-button) найдена');
    } else {
        info.push('❌ Кнопка каталога (.header-catalog-button) НЕ найдена');
    }
    
    // Проверяем наличие выпадающего меню
    const dropdownMenu = document.getElementById('catalogDropdownMenu');
    if (dropdownMenu) {
        info.push('✅ Выпадающее меню найдено');
        info.push('   - Стиль display: ' + dropdownMenu.style.display);
    } else {
        info.push('❌ Выпадающее меню НЕ найдено');
    }
    
    // Проверяем наличие функций
    if (typeof showCatalogDropdown === 'function') {
        info.push('✅ Функция showCatalogDropdown() доступна');
    } else {
        info.push('❌ Функция showCatalogDropdown() НЕ доступна');
    }
    
    if (typeof closeCatalogDropdown === 'function') {
        info.push('✅ Функция closeCatalogDropdown() доступна');
    } else {
        info.push('❌ Функция closeCatalogDropdown() НЕ доступна');
    }
    
    // Проверяем CSS
    const catalogCSS = document.querySelector('link[href*="catalog_dropdown.css"]');
    if (catalogCSS) {
        info.push('✅ CSS файл catalog_dropdown.css подключен');
    } else {
        info.push('❌ CSS файл catalog_dropdown.css НЕ подключен');
    }
    
    // Проверяем текущий URL
    info.push('🌐 <strong>Текущий URL:</strong> ' + window.location.href);
    
    // Проверяем поддержку History API
    if (window.history && window.history.pushState) {
        info.push('✅ History API поддерживается');
    } else {
        info.push('❌ History API НЕ поддерживается');
    }
    
    debugInfo.innerHTML = info.join('<br>');
});
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
