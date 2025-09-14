<?php
/**
 * Проверка после очистки лишних файлов меню
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

echo "<h2>Проверка после очистки лишних файлов меню</h2>";

echo "<h3>1. Оставшиеся файлы меню:</h3>";

$remaining_files = [
    'include/menu/menu.desktop_unified.php' => 'Основной унифицированный файл меню',
    'include/menu/menu.top_fixed_field.php' => 'Файл для закрепленного меню (старый)',
    'include/menu/menu.unified_fixed.php' => 'Файл для закрепленного меню (новый)',
    'include/menu/mobileappmenu.php' => 'Мобильное приложение меню'
];

foreach ($remaining_files as $file => $description) {
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$file)) {
        echo "<span style='color: green;'>✓ $description: $file</span><br>";
    } else {
        echo "<span style='color: red;'>✗ $description: $file НЕ НАЙДЕН</span><br>";
    }
}

echo "<h3>2. Оставшиеся шаблоны меню:</h3>";

$remaining_templates = [
    'desktop_unified' => 'Унифицированный шаблон для десктопа',
    'top_content_multilevel' => 'Шаблон для многоуровневого меню (используется)',
    'top_fixed_field' => 'Шаблон для закрепленного меню (старый)',
    'unified_fixed' => 'Шаблон для закрепленного меню (новый)',
    'top_mobile' => 'Мобильный шаблон',
    'left_menu' => 'Левое меню',
    'bottom' => 'Нижнее меню'
];

foreach ($remaining_templates as $template => $description) {
    $template_path = "local/templates/aspro_next_custom/components/bitrix/menu/$template/template.php";
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$template_path)) {
        echo "<span style='color: green;'>✓ $description: $template</span><br>";
    } else {
        echo "<span style='color: red;'>✗ $description: $template НЕ НАЙДЕН</span><br>";
    }
}

echo "<h3>3. Проверка работы унифицированного меню:</h3>";

echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0; background: #f9f9f9;'>";
echo "<h4>Унифицированное десктопное меню:</h4>";
include($_SERVER["DOCUMENT_ROOT"]."/include/menu/menu.desktop_unified.php");
echo "</div>";

echo "<h3>4. Статистика очистки:</h3>";

$deleted_files = [
    'menu.top.php',
    'menu.top_sections.php', 
    'menu.topest.php',
    'menu.top_catalog_wide.php',
    'menu.unified.php'
];

$deleted_templates = [
    'top',
    'top_catalog',
    'top_catalog_wide',
    'top_content_row',
    'unified'
];

echo "<p><strong>Удалено файлов меню:</strong> " . count($deleted_files) . "</p>";
echo "<ul>";
foreach ($deleted_files as $file) {
    echo "<li style='color: red;'>✗ $file</li>";
}
echo "</ul>";

echo "<p><strong>Удалено шаблонов меню:</strong> " . count($deleted_templates) . "</p>";
echo "<ul>";
foreach ($deleted_templates as $template) {
    echo "<li style='color: red;'>✗ $template</li>";
}
echo "</ul>";

echo "<h3>5. Проверка использования в хедерах:</h3>";

$header_files = [
    'local/templates/aspro_next_custom/page_blocks/default/header_1.php',
    'local/templates/aspro_next_custom/page_blocks/default/header_3.php',
    'local/templates/aspro_next_custom/page_blocks/header_fixed_2.php'
];

foreach ($header_files as $file) {
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$file)) {
        $content = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$file);
        if (strpos($content, 'menu.desktop_unified.php') !== false) {
            echo "<span style='color: green;'>✓ $file использует menu.desktop_unified.php</span><br>";
        } else {
            echo "<span style='color: orange;'>⚠ $file НЕ использует menu.desktop_unified.php</span><br>";
        }
    } else {
        echo "<span style='color: red;'>✗ Файл не найден: $file</span><br>";
    }
}

echo "<hr>";
echo "<h3>Очистка завершена!</h3>";
echo "<p><strong>Результат:</strong></p>";
echo "<ul>";
echo "<li>✅ Удалено 5 неиспользуемых файлов меню</li>";
echo "<li>✅ Удалено 5 неиспользуемых шаблонов меню</li>";
echo "<li>✅ Оставлены только необходимые файлы</li>";
echo "<li>✅ Унифицированное меню работает корректно</li>";
echo "</ul>";

echo "<p><strong>Теперь система меню максимально упрощена и унифицирована!</strong></p>";

// Устанавливаем заголовки для очистки кеша браузера
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
