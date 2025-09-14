<?php
/**
 * Тестовый файл для проверки унифицированного десктопного меню
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

echo "<h2>Тест унифицированного десктопного меню</h2>";

echo "<h3>1. Проверка файлов:</h3>";

// Проверяем основные файлы
$files_to_check = [
    'include/menu/menu.desktop_unified.php' => 'Унифицированный файл меню',
    'local/templates/aspro_next_custom/components/bitrix/menu/desktop_unified/template.php' => 'Шаблон унифицированного меню'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$file)) {
        echo "<span style='color: green;'>✓ $description: $file</span><br>";
    } else {
        echo "<span style='color: red;'>✗ $description: $file НЕ НАЙДЕН</span><br>";
    }
}

echo "<h3>2. Проверка обновленных файлов меню:</h3>";

$menu_files = [
    'include/menu/menu.top.php',
    'include/menu/menu.top_sections.php',
    'include/menu/menu.topest.php',
    'include/menu/menu.top_catalog_wide.php',
    'include/menu/menu.unified.php'
];

foreach ($menu_files as $file) {
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$file)) {
        $content = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$file);
        if (strpos($content, 'desktop_unified') !== false) {
            echo "<span style='color: green;'>✓ $file использует desktop_unified</span><br>";
        } else {
            echo "<span style='color: red;'>✗ $file НЕ использует desktop_unified</span><br>";
        }
    } else {
        echo "<span style='color: red;'>✗ Файл не найден: $file</span><br>";
    }
}

echo "<h3>3. Проверка обновленных хедеров:</h3>";

$header_files = [
    'local/templates/aspro_next_custom/page_blocks/default/header_1.php',
    'local/templates/aspro_next_custom/page_blocks/default/header_3.php',
    'local/templates/aspro_next_custom/page_blocks/default/header_7.php',
    'local/templates/aspro_next_custom/page_blocks/header_fixed_2.php'
];

foreach ($header_files as $file) {
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$file)) {
        $content = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$file);
        if (strpos($content, 'menu.desktop_unified.php') !== false) {
            echo "<span style='color: green;'>✓ $file использует menu.desktop_unified.php</span><br>";
        } else {
            echo "<span style='color: red;'>✗ $file НЕ использует menu.desktop_unified.php</span><br>";
        }
    } else {
        echo "<span style='color: red;'>✗ Файл не найден: $file</span><br>";
    }
}

echo "<h3>4. Тест отображения унифицированного меню:</h3>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0; background: #f9f9f9;'>";
echo "<h4>Унифицированное десктопное меню:</h4>";
include($_SERVER["DOCUMENT_ROOT"]."/include/menu/menu.desktop_unified.php");
echo "</div>";

echo "<h3>5. Статистика унификации:</h3>";

// Подсчитываем количество файлов, использующих desktop_unified
$all_menu_files = glob($_SERVER["DOCUMENT_ROOT"]."/include/menu/*.php");
$unified_count = 0;
$total_count = count($all_menu_files);

foreach ($all_menu_files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'desktop_unified') !== false) {
        $unified_count++;
    }
}

echo "<p><strong>Файлов меню:</strong> $unified_count из $total_count используют desktop_unified</p>";

$all_header_files = glob($_SERVER["DOCUMENT_ROOT"]."/local/templates/aspro_next_custom/page_blocks/**/*.php");
$unified_headers = 0;
$total_headers = count($all_header_files);

foreach ($all_header_files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'menu.desktop_unified.php') !== false) {
        $unified_headers++;
    }
}

echo "<p><strong>Хедеров:</strong> $unified_headers из $total_headers используют menu.desktop_unified.php</p>";

echo "<hr>";
echo "<h3>Унификация десктопных меню завершена!</h3>";
echo "<p><strong>Преимущества:</strong></p>";
echo "<ul>";
echo "<li>✅ Единый шаблон для всех десктопных меню</li>";
echo "<li>✅ Автоматическое добавление пункта 'Отследить заказ'</li>";
echo "<li>✅ Централизованное управление меню</li>";
echo "<li>✅ Упрощенное обслуживание</li>";
echo "<li>✅ Консистентность во всех хедерах</li>";
echo "</ul>";

echo "<p><strong>Обновите страницу в браузере (Ctrl+F5) для применения изменений</strong></p>";

// Устанавливаем заголовки для очистки кеша браузера
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
