<?php
/**
 * Скрипт для сброса кеширования стилей в Bitrix
 * Запустите этот файл через браузер или командную строку
 */

// Подключаем ядро Bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

// Проверяем права администратора
if (!$USER->IsAdmin()) {
    die("Ошибка: Необходимы права администратора для выполнения этого скрипта");
}

echo "<h2>Сброс кеширования стилей</h2>";
echo "<p>Начинаем процесс очистки кеша...</p>";

// 1. Очищаем основной кеш Bitrix
echo "<p>1. Очищаем основной кеш Bitrix...</p>";
if (function_exists('bx_accelerator_reset')) {
    bx_accelerator_reset();
    echo "<span style='color: green;'>✓ Основной кеш очищен</span><br>";
} else {
    echo "<span style='color: red;'>✗ Функция bx_accelerator_reset не найдена</span><br>";
}

// 2. Очищаем кеш HTML страниц
echo "<p>2. Очищаем кеш HTML страниц...</p>";
if (class_exists('CHTMLPagesCache')) {
    CHTMLPagesCache::ClearAll();
    echo "<span style='color: green;'>✓ Кеш HTML страниц очищен</span><br>";
} else {
    echo "<span style='color: red;'>✗ Класс CHTMLPagesCache не найден</span><br>";
}

// 3. Очищаем кеш модуля aspro.next
echo "<p>3. Очищаем кеш модуля aspro.next...</p>";
if (CModule::IncludeModule('aspro.next')) {
    if (class_exists('CNextCache')) {
        CNextCache::ClearAllCache();
        echo "<span style='color: green;'>✓ Кеш модуля aspro.next очищен</span><br>";
    } else {
        echo "<span style='color: orange;'>⚠ Класс CNextCache не найден, но модуль подключен</span><br>";
    }
} else {
    echo "<span style='color: red;'>✗ Модуль aspro.next не найден</span><br>";
}

// 4. Удаляем скомпилированные CSS файлы тем
echo "<p>4. Удаляем скомпилированные CSS файлы тем...</p>";
$themesPath = $_SERVER['DOCUMENT_ROOT'] . '/local/templates/aspro_next_custom/themes/';
if (is_dir($themesPath)) {
    $themes = glob($themesPath . '*', GLOB_ONLYDIR);
    $deletedCount = 0;
    foreach ($themes as $theme) {
        $themeCss = $theme . '/theme.css';
        $themeMinCss = $theme . '/theme.min.css';
        
        if (file_exists($themeCss)) {
            unlink($themeCss);
            $deletedCount++;
        }
        if (file_exists($themeMinCss)) {
            unlink($themeMinCss);
            $deletedCount++;
        }
    }
    echo "<span style='color: green;'>✓ Удалено $deletedCount CSS файлов тем</span><br>";
} else {
    echo "<span style='color: orange;'>⚠ Директория тем не найдена: $themesPath</span><br>";
}

// 5. Удаляем скомпилированные CSS файлы фоновых цветов
echo "<p>5. Удаляем скомпилированные CSS файлы фоновых цветов...</p>";
$bgColorsPath = $_SERVER['DOCUMENT_ROOT'] . '/local/templates/aspro_next_custom/bg_color/';
if (is_dir($bgColorsPath)) {
    $bgThemes = glob($bgColorsPath . '*', GLOB_ONLYDIR);
    $deletedCount = 0;
    foreach ($bgThemes as $bgTheme) {
        $bgCss = $bgTheme . '/bgcolors.css';
        $bgMinCss = $bgTheme . '/bgcolors.min.css';
        
        if (file_exists($bgCss)) {
            unlink($bgCss);
            $deletedCount++;
        }
        if (file_exists($bgMinCss)) {
            unlink($bgMinCss);
            $deletedCount++;
        }
    }
    echo "<span style='color: green;'>✓ Удалено $deletedCount CSS файлов фоновых цветов</span><br>";
} else {
    echo "<span style='color: orange;'>⚠ Директория фоновых цветов не найдена: $bgColorsPath</span><br>";
}

// 6. Очищаем кеш компонентов
echo "<p>6. Очищаем кеш компонентов...</p>";
if (class_exists('CBitrixComponent')) {
    CBitrixComponent::clearComponentCache();
    echo "<span style='color: green;'>✓ Кеш компонентов очищен</span><br>";
} else {
    echo "<span style='color: red;'>✗ Класс CBitrixComponent не найден</span><br>";
}

// 7. Очищаем кеш инфоблоков
echo "<p>7. Очищаем кеш инфоблоков...</p>";
if (CModule::IncludeModule('iblock')) {
    CIBlock::ClearIBlockCache();
    echo "<span style='color: green;'>✓ Кеш инфоблоков очищен</span><br>";
} else {
    echo "<span style='color: red;'>✗ Модуль iblock не найден</span><br>";
}

// 8. Принудительно перегенерируем темы
echo "<p>8. Принудительно перегенерируем темы...</p>";
if (CModule::IncludeModule('aspro.next') && class_exists('CNext')) {
    // Устанавливаем флаги для перегенерации тем
    CNext::SetOption('NeedGenerateAllThemes', 'Y');
    CNext::SetOption('NeedGenerateCustomTheme', 'Y');
    CNext::SetOption('NeedGenerateCustomThemeBG', 'Y');
    
    // Генерируем темы для текущего сайта
    CNext::GenerateThemes(SITE_ID);
    echo "<span style='color: green;'>✓ Темы перегенерированы</span><br>";
} else {
    echo "<span style='color: red;'>✗ Не удалось перегенерировать темы</span><br>";
}

// 9. Очищаем кеш браузера (добавляем заголовки)
echo "<p>9. Устанавливаем заголовки для очистки кеша браузера...</p>";
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
echo "<span style='color: green;'>✓ Заголовки установлены</span><br>";

echo "<hr>";
echo "<h3>Сброс кеширования стилей завершен!</h3>";
echo "<p><strong>Что было сделано:</strong></p>";
echo "<ul>";
echo "<li>Очищен основной кеш Bitrix</li>";
echo "<li>Очищен кеш HTML страниц</li>";
echo "<li>Очищен кеш модуля aspro.next</li>";
echo "<li>Удалены скомпилированные CSS файлы тем</li>";
echo "<li>Удалены скомпилированные CSS файлы фоновых цветов</li>";
echo "<li>Очищен кеш компонентов</li>";
echo "<li>Очищен кеш инфоблоков</li>";
echo "<li>Перегенерированы темы</li>";
echo "<li>Установлены заголовки для очистки кеша браузера</li>";
echo "</ul>";

echo "<p><strong>Рекомендации:</strong></p>";
echo "<ul>";
echo "<li>Обновите страницу в браузере (Ctrl+F5)</li>";
echo "<li>Очистите кеш браузера</li>";
echo "<li>Проверьте, что стили загружаются корректно</li>";
echo "</ul>";

echo "<p><a href='/' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Перейти на главную страницу</a></p>";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>
