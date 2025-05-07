<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Preload fonts -->
    <link rel="preload"
          href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600&family=Roboto:wght@400;500&display=swap"
          as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600&family=Roboto:wght@400;500&display=swap"
              rel="stylesheet">
    </noscript>
    {{--    <link href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">--}}
    <title>Хорошие займы онлайн 24/7 - Центр займов!</title>
    <link rel="stylesheet" href="/css/nouislider.min.css">
    <link rel="icon" href="{{ asset('assets/ctr/img/svg/favicon.png') }}" type="image/x-icon">
    @viteReactRefresh
    @vite('resources/assets/projects/ctr/css/style.css',)
    @viteReactRefresh
    @vite('resources/assets/projects/ctr/css/app.css')
</head>
<body>
<header class="header">
    <div class="header__container container">
        <div class="header__logo logo">
            <a href="{{route('front.index')}}"><img src="/assets/ctr/img/logo.svg" alt="Социальный займ"></a>
        </div>
        <div class="header__menu">
            @php
                // Проверяем, находимся ли мы на главной странице
                $isHomePage = request()->is('/');
            @endphp

            <ul class="menu">
                <li class="menu__item">
                    <a href="{{ $isHomePage ? '#methods' : url('/') . '#methods' }}" class="menu__link">Способы
                        получения</a>
                </li>
                <li class="menu__item">
                    <a href="{{ $isHomePage ? '#reviews' : url('/') . '#reviews' }}" class="menu__link">Отзывы</a>
                </li>
                <li class="menu__item">
                    <a href="{{ $isHomePage ? '#faq' : url('/') . '#faq' }}" class="menu__link">Ответы на вопросы</a>
                </li>
            </ul>
        </div>
        <div class="header__timer">
            <div class="timer">
                <p class="timer__text">Деньги у вас</p>
                <div class="timer__block">
                    <div class="timer__icon">
                        <svg>
                            <use xlink:href="/assets/ctr/img/sprite.svg#icon-clock">
                        </svg>
                    </div>
                    <span class="timeGetMoney" data-time>00:00</span>
                </div>
            </div>
        </div>
        <button class="mobile-action">
            <svg>
                <use xlink:href="/assets/ctr/img/sprite.svg#icon-menu">
            </svg>
        </button>
    </div>
</header>
@yield('content')
<!-- begin footer -->
<footer class="footer">
    <div class="container">
        <div class="footer__logo logo">
            <img src="/assets/ctr/img/logo.svg" alt="Центр займов 24/7">
        </div>
        <ul class="contacts footer__contacts">
            <li class="contacts__item">
                <div class="contacts__icon contacts__icon_phone">
                    <svg>
                        <use xlink:href="/assets/ctr/img/sprite.svg#icon-phone">
                    </svg>
                </div>
                <a class="contacts__text" href="tel:+79649937937">+7 (964) 9-937-937</a>
            </li>
            <li class="contacts__item">
                <div class="contacts__icon contacts__icon_mail">
                    <svg>
                        <use xlink:href="/assets/ctr/img/sprite.svg#icon-mail">
                    </svg>
                </div>
                <a class="contacts__text" href="mailto:finxmall24@gmail.com">finxmall24@gmail.com</a>
            </li>
            <li class="contacts__item">
                <div class="contacts__icon contacts__icon_map">
                    <svg>
                        <use xlink:href="/assets/ctr/img/sprite.svg#icon-map">
                    </svg>
                </div>
                <p class="contacts__text">г. Москва, ул. Пресненская наб. 12, Башня Федерации, 19 этаж</p>
            </li>
        </ul>
        <div class="schedule">
            <p class="schedule__value">Заявки принимаются 24/7 без обедов и выходных!</p>
        </div>
        <p class="mark">Сервис предназначен для лиц, старше<br> 18 лет</p>
        <div class="footer__info">
            <h3 class="footer__title">Условия займов:</h3>
            <p class="footer__text">Сервис Социальный займ позволяет подобрать предложения по выдаче займа на следующих
                условиях: Сумма займа от 1000 до 100 000 рублей. Возраст заемщика от 18 до 75 лет. Срок займа от 5 дней
                до 365 дней. Полная стоимость кредита (ПСК) может варьироваться от 0 до 292% годовых. Бесплатные первые
                займы (под 0%) возможны в исключительных случаях по решению кредитора. Займы и кредиты предоставляются
                организациями, имеющими соответствующие лицензии.</p>

            <h3 class="footer__title">ПСК (полная стоимость кредита)</h3>
            <p class="footer__text">Максимальная процентная в процентах составляет 292% годовых. Максимальная
                годовая процентная ставка, включающая ссудный процент, а также все остальные комиссии и расходы
                за год, или аналогичная ставка.</p>
            <p class="footer__text">* У пользователей сервиса есть возможность получить займ с минимальной
                процентной ставкой одобренной МФО. Подробности при выборе персонального предложения. Займ у
                партнеров выдается в российских рублях гражданам Российской Федерации, на банковский счет, на
                карту или наличными. Минимальная сумма займа: 1 000 рублей. Максимальная сумма займа: 100 000
                рублей. Процентная ставка и срок займа: по решению МФО. Услугу сервиса предоставляет
                индивидуальный предприниматель Кузнецова М.Ю. ОГРНИП: 323645700109605, ИНН 645004790022</p>

            <h3 class="footer__title">Находится в реестре операторов, осуществляющих обработку персональных
                данных, Приказ № 174 от 27.12.2023 Рег. № 64-23-011823</h3>
            <p class="footer__text">Вся представленная на сайте информация, касающаяся финансовых услуг, носит
                информационный характер и ни при каких условиях не является публичной офертой, определяемой
                положениями статьи 437 Гражданского кодекса РФ. Нажатие на кнопки "Получить деньги", а также
                последующее заполнение тех или иных форм, не накладывает на владельцев сайта никаких
                обязательств.</p>
            <p class="footer__text">Все материалы, размещенные на сайте являются собственностью владельцев
                сайта, либо собственностью организаций, с которыми у владельцев сайта есть соглашение о
                размещении материалов.<br>
                Для аналитических целей на сайте работает система статистики, которая собирает информацию о
                посещенных страницах сайта, заполненных формах и т.д. Сотрудники компании имеют доступ к этой
                информации.</p>
            <p class="footer__text">Регистрируясь на сайте или оставляя тем, или иным способом свои персональные
                данные (информацию о себе), Вы предоставляете право сотрудникам компании обрабатывать вашу
                персональную информацию.</p>
            <p class="footer__text">Данное соглашение действует бессрочно.<br>
                Важно: предоплату берут только мошенники!<br>
                Сервис бесплатный - за предоставление информации комиссия не взимается.<br></p>
        </div>
    </div>
</footer>
<div class="mobile-menu">
    <div class="container">
        <ul class="mobile-menu__list">
            <li class="mobile-menu__item">
                <a href="#methods" class="mobile-menu__link">Способы получения</a>
            </li>
            <li class="mobile-menu__item">
                <a href="#reviews" class="mobile-menu__link">Отзывы</a>
            </li>
            <li class="mobile-menu__item">
                <a href="#faq" class="mobile-menu__link">Ответы на вопросы</a>
            </li>
        </ul>
        <a href="#" class="btn mobile-menu__btn" data-next>Получить деньги</a>

        <div class="mobile-menu__img">
            <img loading="lazy" srcset="/assets/ctr/img/methods@2x.webp 2x, /assets/ctr/img/methods.webp"
                 src="/assets/ctr/img/methods_origin.png" alt="Способы получения">
        </div>
    </div>
</div>
<!-- end footer -->
<!-- Yandex.Metrika counter -->
<script defer type="text/javascript">
    function getParameterByName(name) {
        const url = new URL(window.location.href);
        return url.searchParams.get(name);
    }
    // Получаем идентификатор счетчика из параметра URL
    const ym_id = getParameterByName('ym_id');

    window.addEventListener('load', function () {
        setTimeout(function () {
            (function (m, e, t, r, i, k, a) {
                m[i] = m[i] || function () {
                    (m[i].a = m[i].a || []).push(arguments);
                };
                m[i].l = 1 * new Date();
                k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a);
            })(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
            //default counter
            ym(96714912, "init", {
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true,
                webvisor: true
            });

            // Если идентификатор найден, инициализируем счетчик
            console.info('Идентификатор ym_id:', ym_id);
            if (ym_id) {
                ym(ym_id, "init", {
                    clickmap: true,
                    trackLinks: true,
                    accurateTrackBounce: true,
                    webvisor: true
                });
            } else {
                console.warn('Идентификатор ym_id не найден в URL');
            }
        }, 4000);
    });
</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/96714912" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->
<!-- Top.Mail.Ru counter -->
<script defer type="text/javascript">
    var _tmr = window._tmr || (window._tmr = []);
    _tmr.push({id: "3493619", type: "pageView", start: (new Date()).getTime(), pid: "USER_ID"});
    window.addEventListener('load', function () {
        setTimeout(function () {
            (function (d, w, id) {
                if (d.getElementById(id)) return;
                var ts = d.createElement("script");
                ts.type = "text/javascript";
                ts.async = true;
                ts.defer = true;
                ts.id = id;
                ts.src = "https://top-fwz1.mail.ru/js/code.js";
                var f = function () {
                    var s = d.getElementsByTagName("script")[0];
                    s.parentNode.insertBefore(ts, s);
                };
                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else {
                    f();
                }
            })(document, window, "tmr-code");
        }, 4000);
    });
</script>
<noscript>
    <div><img src="https://top-fwz1.mail.ru/counter?id=3493619;js=na" style="position:absolute;left:-9999px;"
              alt="Top.Mail.Ru"/></div>
</noscript>
<script defer src="/js/nouislider.min.js"></script>
@viteReactRefresh
@vite(['resources/js/scripts.js', 'resources/js/app.jsx'])
<!-- /Top.Mail.Ru counter -->
@yield('scripts')
</body>
</html>
