<?php
/**
 * Быстрая очистка только CSS файлов
 * Запустите этот файл через браузер для быстрой очистки CSS кеша
 */

// Подключаем ядро Bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

// Проверяем права администратора
if (!$USER->IsAdmin()) {
    die("Ошибка: Необходимы права администратора");
}

echo "<h2>Быстрая очистка CSS кеша</h2>";

// 1. Очищаем основной кеш
bx_accelerator_reset();
echo "<p>✓ Основной кеш очищен</p>";

// 2. Удаляем CSS файлы тем
$themesPath = $_SERVER['DOCUMENT_ROOT'] . '/local/templates/aspro_next_custom/themes/';
$deletedCount = 0;

if (is_dir($themesPath)) {
    $themes = glob($themesPath . '*', GLOB_ONLYDIR);
    foreach ($themes as $theme) {
        $files = glob($theme . '/*.css');
        foreach ($files as $file) {
            if (unlink($file)) {
                $deletedCount++;
            }
        }
    }
}

// 3. Удаляем CSS файлы фоновых цветов
$bgColorsPath = $_SERVER['DOCUMENT_ROOT'] . '/local/templates/aspro_next_custom/bg_color/';
if (is_dir($bgColorsPath)) {
    $bgThemes = glob($bgColorsPath . '*', GLOB_ONLYDIR);
    foreach ($bgThemes as $bgTheme) {
        $files = glob($bgTheme . '/*.css');
        foreach ($files as $file) {
            if (unlink($file)) {
                $deletedCount++;
            }
        }
    }
}

echo "<p>✓ Удалено $deletedCount CSS файлов</p>";

// 4. Перегенерируем темы
if (CModule::IncludeModule('aspro.next') && class_exists('CNext')) {
    CNext::SetOption('NeedGenerateAllThemes', 'Y');
    CNext::GenerateThemes(SITE_ID);
    echo "<p>✓ Темы перегенерированы</p>";
}

echo "<p><strong>Готово! Обновите страницу (Ctrl+F5)</strong></p>";
echo "<p><a href='/' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>На главную</a></p>";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>
