<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arItem */
?>
<div class="banner-top-slide-text banner-top-slide-text--<?= $arItem['TEXT_POSITION'] ?> <?= $arItem['BUTTONS'] ? 'banner-top-slide-text--has-buttons' : '' ?>">
    <?php
    if ($arItem['TITLE'] && !$arItem['HIDE_TITLE']) { ?>
        <div class="banner-top-slide-text__title">
            <span>
                <!-- Убираем ссылку с заголовка, делаем его некликабельным -->
                <?= $arItem['TITLE_NOTAGS'] ?>
            </span>
        </div>
        <?php
    }
    if ($arItem['TEXT']) { ?>
        <div class="banner-top-slide-text__content"><?= $arItem['TEXT'] ?></div>
        <?php
    }
    
    // Добавляем кнопку "За покупками" для всех баннеров
    // Показываем кнопку всегда, даже если нет URL (тогда ведем на главную)
    $buttonUrl = $arItem['URL'] ?: '/';
    $buttonTarget = $arItem['TARGET'] ? "target=\"{$arItem['TARGET']}\"" : '';
    
    // Отладочная информация (можно убрать после проверки)
    if (isset($_GET['debug_banner'])) {
        echo "<!-- DEBUG: URL = " . ($arItem['URL'] ?: 'НЕТ') . ", TARGET = " . ($arItem['TARGET'] ?: 'НЕТ') . " -->";
    }
    ?>
    <div class="banner-top-slide-text__buttons">
        <a href="<?= $buttonUrl ?>" <?= $buttonTarget ?> class="banner-shopping-button">
            За покупками
        </a>
    </div>
    <?php
    
    if ($arItem['BUTTONS']) { ?>
        <div class="banner-top-slide-text__buttons"><?= $arItem['BUTTONS'] ?></div>
        <?php
    }
    ?>
</div>
