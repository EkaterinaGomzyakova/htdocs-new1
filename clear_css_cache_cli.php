<?php
/**
 * Скрипт для сброса кеширования стилей в Bitrix (командная строка)
 * Запуск: php clear_css_cache_cli.php
 */

// Подключаем ядро Bitrix
require(__DIR__."/bitrix/modules/main/include/prolog_before.php");

echo "=== Сброс кеширования стилей ===\n";

// 1. Очищаем основной кеш Bitrix
echo "1. Очищаем основной кеш Bitrix... ";
if (function_exists('bx_accelerator_reset')) {
    bx_accelerator_reset();
    echo "OK\n";
} else {
    echo "ERROR - функция bx_accelerator_reset не найдена\n";
}

// 2. Очищаем кеш HTML страниц
echo "2. Очищаем кеш HTML страниц... ";
if (class_exists('CHTMLPagesCache')) {
    CHTMLPagesCache::ClearAll();
    echo "OK\n";
} else {
    echo "ERROR - класс CHTMLPagesCache не найден\n";
}

// 3. Очищаем кеш модуля aspro.next
echo "3. Очищаем кеш модуля aspro.next... ";
if (CModule::IncludeModule('aspro.next')) {
    if (class_exists('CNextCache')) {
        CNextCache::ClearAllCache();
        echo "OK\n";
    } else {
        echo "WARNING - класс CNextCache не найден, но модуль подключен\n";
    }
} else {
    echo "ERROR - модуль aspro.next не найден\n";
}

// 4. Удаляем скомпилированные CSS файлы тем
echo "4. Удаляем скомпилированные CSS файлы тем... ";
$themesPath = __DIR__ . '/local/templates/aspro_next_custom/themes/';
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
    echo "OK - удалено $deletedCount файлов\n";
} else {
    echo "WARNING - директория тем не найдена: $themesPath\n";
}

// 5. Удаляем скомпилированные CSS файлы фоновых цветов
echo "5. Удаляем скомпилированные CSS файлы фоновых цветов... ";
$bgColorsPath = __DIR__ . '/local/templates/aspro_next_custom/bg_color/';
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
    echo "OK - удалено $deletedCount файлов\n";
} else {
    echo "WARNING - директория фоновых цветов не найдена: $bgColorsPath\n";
}

// 6. Очищаем кеш компонентов
echo "6. Очищаем кеш компонентов... ";
if (class_exists('CBitrixComponent')) {
    CBitrixComponent::clearComponentCache();
    echo "OK\n";
} else {
    echo "ERROR - класс CBitrixComponent не найден\n";
}

// 7. Очищаем кеш инфоблоков
echo "7. Очищаем кеш инфоблоков... ";
if (CModule::IncludeModule('iblock')) {
    CIBlock::ClearIBlockCache();
    echo "OK\n";
} else {
    echo "ERROR - модуль iblock не найден\n";
}

// 8. Принудительно перегенерируем темы
echo "8. Принудительно перегенерируем темы... ";
if (CModule::IncludeModule('aspro.next') && class_exists('CNext')) {
    // Устанавливаем флаги для перегенерации тем
    CNext::SetOption('NeedGenerateAllThemes', 'Y');
    CNext::SetOption('NeedGenerateCustomTheme', 'Y');
    CNext::SetOption('NeedGenerateCustomThemeBG', 'Y');
    
    // Генерируем темы для текущего сайта
    CNext::GenerateThemes(SITE_ID);
    echo "OK\n";
} else {
    echo "ERROR - не удалось перегенерировать темы\n";
}

echo "\n=== Сброс кеширования стилей завершен! ===\n";
echo "Рекомендации:\n";
echo "- Обновите страницу в браузере (Ctrl+F5)\n";
echo "- Очистите кеш браузера\n";
echo "- Проверьте, что стили загружаются корректно\n";

require(__DIR__."/bitrix/modules/main/include/epilog_after.php");
?>
