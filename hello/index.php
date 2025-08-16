<? include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<!doctype html>
<html>

<head>
    <meta charset="<?= SITE_CHARSET ?>">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Добро пожаловать в Бьюти-семью! Clanbeauty.ru</title>
    <meta name="description" content="Интернет-магазин косметики из Кореи, США и Европы. Корейская косметика.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#295C4F" />
    <meta name="format-detection" content="telephone=no">



    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css?<?= rand(); ?>">

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/main.js?<?= rand(); ?>"></script>
    <? $APPLICATION->ShowHead(); ?>
    <?
    CModule::IncludeModule("aspro.next");
    CModule::IncludeModule("wl.snailshop");
    CNext::SetJSOptions();
    ?>
</head>

<body>
    <div class="main-container">
        <div class="container">
            <a href="/?utm_source=hello" class="logo"></a>
            <?//<i class="icon icon-star"></i>?>
            <h1 class="text-center">О нас</h1>
            <p class="text-center">Привет! Мы — интернет-магазин косметики из Кореи, США и Европы. Мы помогаем подобрать лучший уход для кожи на консультациях и стать еще прекраснее! Доставка по всей России.</p>
            <a href="#" class="btn btn-white" onclick="$('.socials').slideDown(); return false;" style="margin-bottom: 30px;">Записаться на консультацию</a>

            <div class="socials" style="display: none;">
                <p>Пишите нам прямо сейчас в удобный мессенджер</p>
                <a class="social-icon" href="https://t.me/CLAN_BEAUTY">
                    <i class="icon tg-icon"></i>
                    <span>Telegram</span>
                </a>
                <a class="social-icon" href="https://api.whatsapp.com/send?phone=79202488898">
                    <i class="icon wa-icon"></i>
                    <span>Whatsapp</span>
                </a>
                <a class="social-icon" href="https://vk.com/clanbeauty">
                    <i class="icon vk-icon"></i>
                    <span>Vkontakte</span>
                </a>
            </div>

            <a href="/?utm_source=hello" class="btn btn-green">Перейти на сайт</a>
            <a href="#" class="btn btn-white" onclick="$('.features').slideDown(); return false;">Как сэкономить в ClanBeauty?</a>

            <ul class="features" style="display: none;">
                <li>Используйте <strong>промокод FIRST</strong>, чтобы получить <strong>скидку -5% на первый заказ</strong></li>
                <li>У нас действует накопительная система скидок от 3% до 10%</li>
                <li>Кешбек beauty-баллами — можно оплатить до 5% от стоимости заказа</li>
                <li>Оплата Долями — сервис оплаты покупок частями</li>
            </ul>

            <div class="button-line">
                <a href="/offers/discount/?utm_source=hello" class="btn btn-green btn-with-icon btn-with-icon-percent">Распродажа</a>
                <a href="/offers/novelty/?utm_source=hello" class="btn btn-green btn-with-icon btn-with-icon-new">Новинки</a>
            </div>

            <div class="subscribe">
                <p>Подписывайтесь на нас в социальных сетях, чтобы быть в курсе последних акций и промокодов на скидку, а также получать полезную информацию об уходе за собой и смотреть обзоры на средства!</p>
                <div class="subscribe-buttons">
                    <div class="subscribe-buttons-item">
                        <a href="https://t.me/clanbeauty">Мы в Telegram <i class="icon tg-icon"></i></a>
                    </div>
                    <div class="subscribe-buttons-item">
                        <a href="https://vk.com/clanbeauty">Мы в Вконтакте <i class="icon vk-icon"></a></i>
                    </div>
                </div>
            </div>
        </div>

        <? /*
        <div class="marta8">
            <a class="btn btn-white" href="/catalog/gift-certificates/?utm_source=hello">Купить сертификат</a>
            <a class="btn btn-white" href="/catalog/8_marta/?utm_source=hello">Купить подарки</a>
            <div class="marta8-bottom">
                <p>Мы также принимаем заказы на <i>корпоративные подарки</i> и подарочные сертификаты. При покупке оптом порадуем красивой скидкой. Заказы принимаем онлайн и офлайн.</p>
            </div>
        </div>
        */?>

        <div class="questions">
            <h2 class="text-center">Вопросы</h2>
            <div class="questions-question">
                <a class="questions-ask">Как сделать заказ?</a>
                <div class="question-answer">Вы можете сделать заказ самостоятельно на сайте или написать нам в любой из мессенджеров или соцсеть. Мы поможем оформить заказ.</div>
            </div>
            <div class="questions-question">
                <a class="questions-ask">Как оплатить заказ?</a>
                <div class="question-answer">Вы можете оплатить заказ банковской картой на сайте, банковской картой или наличными в магазине или воспользоваться сервисом оплаты Долями.</div>
            </div>
            <div class="questions-question">
                <a class="questions-ask">Когда я получу свой заказ?</a>
                <div class="question-answer">
                    Срок получения заказа зависят от способ доставки.
                    <ul>
                        <li>Курьером по Липецку — в день заказ или на следующий день <small>(в период продаж сроки доставки могут увеличиться)</small></li>
                        <li>Доставка до пункта самовывоза СДЭК или Почта России обычно занимает 2-3 дня по ЦФО и 4-6 по России</li>
                        <li>Также вы можете забрать свой заказ из нашего магазина по адресу г. Липецк, пр. Победы, 61 Б</li>
                    </ul>
                    </p>
                </div>
            </div>
            <div class="questions-question">
                <a class="questions-ask">У вас оригинальная продукция?</a>
                <div class="question-answer">Да, продукция полностью оригинальна и сертифицирована</div>
            </div>
        </div>


        <div class="socials">
            <h2 class="text-center">Остались вопросы? Напишите нам!</h2>
            <a class="social-icon" href="https://t.me/CLAN_BEAUTY">
                <i class="icon tg-icon"></i>
                <span>Telegram</span>
            </a>
            <a class="social-icon" href="https://api.whatsapp.com/send?phone=79202488898">
                <i class="icon wa-icon"></i>
                <span>Whatsapp</span>
            </a>
            <a class="social-icon" href="https://vk.com/clanbeauty">
                <i class="icon vk-icon"></i>
                <span>Vkontakte</span>
            </a>
        </div>

        <div class="working-hours">
            <p>Режим работы:</p>
            <p>Ежедневно с 10:00 до 20:00</p>

            <div class="button-line">
                <a href="https://t.me/clanbeauty">Мы в Telegram</a>
                <a href="https://vk.com/clanbeauty">Мы Вконтакте</a>
            </div>

            <div class="address">
                <p>г. Липецк, пр. Победы, 61 Б</p>
                <p>г. Липецк, ул. Зегеля, 2</p>
                <p><a href="tel:+79202488898">+7 920 248 88 98</a></p>
                <p><a href="mailto:sales@clanbeauty.ru">sales@clanbeauty.ru</a></p>
            </div>

            <div class="legals">
                ИП Ануфриева Ирина Сергеевна
                <br>
                ИНН: 482614116612, ОГРНИП: 320482700034770 от 09.09.2020
                <br>
                Адрес осуществления деятельности: г. Липецк, пр. Победы, 61 Б
            </div>
        </div>

        <!-- Yandex.Metrika counter -->
        <script type="text/javascript">
            (function(d, w, c) {
                (w[c] = w[c] || []).push(function() {
                    try {
                        w.yaCounter48394271 = new Ya.Metrika({
                            id: 48394271,
                            clickmap: true,
                            trackLinks: true,
                            accurateTrackBounce: true
                        });
                    } catch (e) {}
                });
                var n = d.getElementsByTagName("script")[0],
                    s = d.createElement("script"),
                    f = function() {
                        n.parentNode.insertBefore(s, n);
                    };
                s.type = "text/javascript";
                s.async = true;
                s.src = "https://mc.yandex.ru/metrika/watch.js";
                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else {
                    f();
                }
            })(document, window, "yandex_metrika_callbacks");
        </script>
        <!-- /Yandex.Metrika counter -->
</body>

</html>