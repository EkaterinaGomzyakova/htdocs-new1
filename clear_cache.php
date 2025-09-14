<?php
// Скрипт для очистки кэша CSS
echo "Очистка кэша CSS...\n";

// Очищаем кэш Bitrix
if (file_exists('bitrix/cache/')) {
    $files = glob('bitrix/cache/*');
    foreach($files as $file) {
        if(is_file($file)) {
            unlink($file);
        }
    }
    echo "Кэш Bitrix очищен\n";
}

// Очищаем кэш шаблона
if (file_exists('bitrix/templates/')) {
    $files = glob('bitrix/templates/*/cache/*');
    foreach($files as $file) {
        if(is_file($file)) {
            unlink($file);
        }
    }
    echo "Кэш шаблонов очищен\n";
}

// Очищаем managed cache
if (file_exists('bitrix/managed_cache/')) {
    $files = glob('bitrix/managed_cache/*');
    foreach($files as $file) {
        if(is_file($file)) {
            unlink($file);
        }
    }
    echo "Managed cache очищен\n";
}

echo "Кэш очищен! Обновите страницу с Ctrl+F5\n";
?>
