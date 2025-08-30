<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $templateData */

/** @var @global CMain $APPLICATION */

if (!empty($arResult['ITEMS_ID'])) {
    $wishIDs = \WL\WishList::getListID($arResult['ITEMS_ID']);
} ?>
<script>
    $(document).ready(function() {
        <? if (!empty($wishIDs)) : ?>
            <? foreach ($wishIDs as $itemID) : ?>
                $('.wish_item_button .wish_item.to[data-item="<?= $itemID ?>"]').hide();
                $('.wish_item_button .wish_item.in[data-item="<?= $itemID ?>"]').show();
            <? endforeach; ?>
        <? endif; ?>
    });
</script>



<script type="text/javascript">
       // Ждем ПОЛНОЙ загрузки страницы (включая все скрипты и картинки)
    $(window).on('load', function() {
        // Добавляем микро-задержку на всякий случай, чтобы скрипты вкладок точно успели инициализироваться
        setTimeout(function() {
            
            // Проверяем, есть ли в URL якорь #reviews
            if (window.location.hash === '#reviews') {
                
                // ЭТО САМЫЙ ВАЖНЫЙ СЕЛЕКТОР. 
                // Он ищет ссылку, которая ведет на якорь #reviews, внутри блока с вкладками
                var tabSelector = 'ul.tabs > li > a[href="#reviews"]';
                var tab = $(tabSelector);
                
                // Если такая вкладка найдена
                if (tab.length) {
                    
                    // Имитируем клик по ней
                    tab.trigger('click');
                    
                    // Плавно прокручиваем к началу блока с вкладками
                    // setTimeout здесь нужен, чтобы прокрутка сработала после того, как вкладка откроется
                    setTimeout(function() {
                        $('html, body').animate({
                            scrollTop: tab.closest('.tabs_section').offset().top - 20 // -20, чтобы был небольшой отступ сверху
                        }, 500);
                    }, 100);
                }
            }
        }, 100); // задержка в 100 миллисекунд
    });
</script>