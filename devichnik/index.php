<? include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<?
$productId = 22050;
?>
<!doctype html>
<html>

<head>
    <meta charset="<?= SITE_CHARSET ?>">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>BEAUTY-ДЕВИЧНИК “Красота снаружи и внутри”</title>
    <meta name="description" content="Интернет-магазин косметики из Кореи, США и Европы. Корейская косметика.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#295C4F" />
    <meta name="format-detection" content="telephone=no">



    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="css/main.css?<?= rand(); ?>">

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.fancybox.min.js"></script>
    <script src="<?= SITE_TEMPLATE_PATH ?>/js/jquery.inputmask.bundle.min.js"></script>
    <script src="js/main.js?<?= rand(); ?>"></script>

    <? $APPLICATION->ShowHead(); ?>

    <?

    \CJSCore::Init();
    CModule::IncludeModule("aspro.next");
    CModule::IncludeModule("wl.snailshop");
    CNext::SetJSOptions();
    ?>
</head>

<body>
    <div class="main-container">
        <div class="container first-container">
            <a href="/?utm_source=devichnik" class="logo"></a>
            <h1 class="text-center">BEAUTY-ДЕВИЧНИК “Красота снаружи и внутри”</h1>
            <a href="javascript:scrollTo(0, document.querySelector('.final').offsetTop);" class="btn btn-green" style="margin-bottom: 30px;">Зарегистрироваться на девичник</a>

            <p>Наши девичники — это всегда уникальное событие. Та самая встреча, которая проходит так, будто мы все знакомы уже минимум лет 10 – тепло, душевно, уютно. И вновь встречаемся 25 ноября на нашем beauty-девичнике — поболтаем о том, как быть здоровой и красивой в современном темпе жизни.</p>


            <h2>Вас ждут</h2>
            <ul>
                <li>Подарок каждой участнице</li>
                <li>Welcome-drink и фуршет</li>
                <li>Фотограф Мая Кароян и новогодняя фотозона</li>
                <li>Бес&shy;пре&shy;це&shy;дент&shy;ная скидка 20% на всё</li>
                <li>Выступления спикеров</li>
                <li>Розыгрыш подарков: сертификат на 5000 рублей в медицинский центр «Гармония» и магазин косметики CLANBEAUTY</li>
                <li>Инди&shy;видуаль&shy;ная консультация по подбору ухода</li>
                <li>И просто прекрасно проведённое время</li>
            </ul>
        </div>
    </div>

    <div class="main-container with-background">
        <div class="container second-container">
            <h2>&#128105; В этот раз у нас невероятные спикеры</h2>
            <p>Каждая — настоящий профессионал и эксперт в своём деле.</p>
            <div class="speakers">
                <div class="speaker text-center">
                    <img src="img/anufrieva.jpg" alt="" />
                    <h3>Ирина Ануфриева</h3>
                    <small>основатель и владелец бренда CLANBEAUTY</small>
                    <i>«С чего и в каком возрасте начинается старение? Как предотвратить? Что делать если уже?»</i>
                    <p>+ розыгрыш сертификата на 5000 рублей в CLANBEAUTY</p>
                </div>

                <div class="speaker text-center">
                    <img src="img/sokolova.jpg" alt="" />
                    <h3>Елена Соколова</h3>
                    <small>врач гинеколог-эндокринолог, основатель медицинского центра «Гармония»</small>
                    <i>«Гормоны. Хороший, плохой, злой»</i>
                    <p>+ розыгрыш сертификата на 5000 рублей в медицинский центр</p>
                </div>

                <div class="speaker text-center">
                    <img src="img/kiseleva.jpg" alt="" />
                    <h3>Анна Киселёва</h3>
                    <small>тренер по растяжке и умному фитнесу</small>
                    <i>«Как избавиться от отеков за 15 минут и вернуть телу тонус?»</i>
                    <p>+ розыгрыш офлайн-тренировке по растяжке</p>
                </div>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="container">
            <h2>&#127881; Как это было в прошлый раз </h2>

            <div id="previous-party" class="owl-carousel">
                <a href="img/history/img1.jpg">
                    <img src="img/history/img1.jpg">
                </a>
                <a href="img/history/img2.jpg">
                    <img src="img/history/img2.jpg">
                </a>
                <a href="img/history/img3.jpg">
                    <img src="img/history/img3.jpg">
                </a>
                <a href="img/history/img4.jpg">
                    <img src="img/history/img4.jpg">
                </a>
                <a href="img/history/img5.jpg">
                    <img src="img/history/img5.jpg">
                </a>
                <a href="img/history/img6.jpg">
                    <img src="img/history/img6.jpg">
                </a>
                <a href="img/history/img7.jpg">
                    <img src="img/history/img7.jpg">
                </a>
                <a href="img/history/img8.jpg">
                    <img src="img/history/img8.jpg">
                </a>
                <a href="img/history/img9.jpg">
                    <img src="img/history/img9.jpg">
                </a>
                <a href="img/history/img10.jpg">
                    <img src="img/history/img10.jpg">
                </a>
                <a href="img/history/img11.jpg">
                    <img src="img/history/img11.jpg">
                </a>
                <a href="img/history/img12.jpg">
                    <img src="img/history/img12.jpg">
                </a>
                <a href="img/history/img13.jpg">
                    <img src="img/history/img13.jpg">
                </a>
            </div>
        </div>


        <div class="final" id="final">
            <h3>Девичник состоится 25 ноября 2023 года с 12:00 по адресу г. Липецк, ул. Зегеля, 2</h3>
            <h3>С нетерпением ждем вас!</h3>

            <p>Воспользуйтесь формой, чтобы зарегистрироваться на девичник и получить все бонусы и подарки. Обращаем ваше внимание, что место считается забронированным за вами после оплаты.</p>

            <?
            CModule::IncludeModule("catalog");
            $arProduct = CCatalogProduct::GetByID($productId);
            $arPrice = CCatalogProduct::GetOptimalPrice($productId, 1, CUser::GetUserGroup($USER->GetID()));
            ?>
            <? if ($arProduct['AVAILABLE'] == 'Y') { ?>
                <div class="price">
                    <span>Стоимость участия:</span>
                    <span style="white-space: nowrap;">
                        <? if ($arPrice['RESULT_PRICE']['DISCOUNT']) { ?>
                            <span class="old-price"><?= CurrencyFormat($arPrice['RESULT_PRICE']['BASE_PRICE'], 'RUB'); ?></span>
                        <? } ?>
                        <span class="discount-price"><?= CurrencyFormat($arPrice['RESULT_PRICE']['DISCOUNT_PRICE'], 'RUB'); ?></span>
                    </span>
                </div>
                <form id="order" action="order.php">
                    <?
                    global $USER;
                    $name = '';
                    $phone = '';
                    $email = '';
                    if ($USER->isAuthorized()) {
                        $name = $USER->GetFullName();
                        $phone = $USER->GetLogin('LOGIN');
                        $email = $USER->GetEmail('EMAIL');
                    }
                    ?>
                    <div class="form-content">
                        <div class="form-group">
                            <label class="" for="NAME">ФИО <span class="red">*</span></label>
                            <input type="text" value="<?= $name ?>" required name="NAME" id="NAME">
                        </div>

                        <div class="form-group">
                            <label class="" for="PHONE">Телефон <span class="red">*</span></label>
                            <input type="text" value="<?= $phone ?>" required name="PHONE" id="PHONE">
                        </div>

                        <div class="form-group">
                            <label class="" for="EMAIL">E-mail <span class="red">*</span></label>
                            <input type="email" value="<?= $email ?>" required name="EMAIL" id="EMAIL">
                        </div>

                        <div class="form-group line">
                            <input type="checkbox" required name="RULES" id="RULES">
                            <label class="" for="RULES">Я согласен(-на) с <a href="/include/licenses_detail.php" target="_blank">положением о персональных данных и пользовательским соглашением</a> <span class="red">*</span></label>
                        </div>
                        <button type="submit" class="btn btn-green" value="">Оплатить участие в Девичнике</button>
                    </div>
                    <div id="form-error" class="message danger" style="display: none;"></div>
                </form>
            <? } else { ?>
                <div class="message warning">Регистрация завершена</div>
            <? } ?>
        </div>
    </div>

    <div class="main-container footer">
        <div class="container">
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