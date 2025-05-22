@props(['hasTimer' => true])
        <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="yandex-verification" content="6a1a5b68d9cdc5c6"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <link rel="stylesheet" href="/assets/ctr/css/nouislider.min.css">
    <link rel="icon" href="{{ asset('/assets/ctr/img/svg/favicon.png') }}" type="image/x-icon">
    {{-- Вручную подключаем @vite/client безопасно --}}
    @viteReactRefresh
    @vite([
                'resources/assets/projects/ctr/css/app.css',
                'resources/assets/projects/ctr/css/style.css',
                'resources/assets/projects/ctr/js/scripts.js',
                'resources/assets/projects/ctr/js/app.jsx'
        ])
    @yield('styles')
    @yield('head_scripts')

</head>
<body>
<header class="container sm:px-8 px-2 sm:py-2 py-4 flex flex-row justify-between
sm:gap-8 gap-4  sm:text-base text-sm">

    <div class="header__container container !px-0">
        <div class="header__logo logo">
            <a href="{{route('front.index')}}"><img src="/assets/ctr/img/logo.svg" alt="Социальный займ"></a>
        </div>
        @yield('header')
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
    @if(auth()->check())
        <div class="hidden md:block">
            @include('blocks.logout')
        </div>
    @endif

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
                <div class="contacts__icon contacts__icon_mail">
                    <svg>
                        <use xlink:href="/assets/ctr/img/sprite.svg#icon-mail">
                    </svg>
                </div>
                <a class="contacts__text" href="mailto:support@centrzaim24.ru">support@centrzaim24.ru</a>
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
<script src="/assets/ctr/js/nouislider.min.js"></script>
<script src="https://unpkg.com/imask"></script>
<script type="text/javascript">
    const frontConfig = {
        routeName: @json(request()->route()->getName()),
        dadataToken: @json($dadataToken),
        sliderSumm: @json($sliderSumm),
        isRedirectEnabled: @json($isRedirectEnabled),
        redirectUrl: @json($redirectUrl),
        redirectDelay: @json($redirectDelay),
    };
    console.log('front config', frontConfig);

    function redirect(event, redirectUrl) {
        if (!frontConfig.isRedirectEnabled)
            return;

        if (frontConfig.redirectUrl)
            redirectUrl = frontConfig.redirectUrl;


        // Позволяем открыть форму в новой вкладке
        setTimeout(() => {
            window.location.href = redirectUrl;
        }, frontConfig.redirectDelay); // Задержка 100 мс для гарантии
    }

</script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript" defer>

    // Получаем текущий URL
    const currentUrl = window.location.pathname;

    // Устанавливаем таймаут в зависимости от URL
    const delay = currentUrl.includes('/register') ? 4000 : 0;
    console.log('delay', delay);

    window.addEventListener('load', function () {
        setTimeout(() => {
            (function (m, e, t, r, i, k, a) {
                m[i] = m[i] || function () {
                    (m[i].a = m[i].a || []).push(arguments)
                };
                m[i].l = 1 * new Date();
                for (var j = 0; j < document.scripts.length; j++) {
                    if (document.scripts[j].src === r) {
                        return;
                    }
                }
                k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
            })

            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym(99015882, "init", {
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true,
                webvisor: true
            });
        }, delay);

    });

</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/99015882" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>

@yield('scripts')
<!-- /Yandex.Metrika counter -->

<script>
    // Smooth scrolling for all anchor links
    document.addEventListener('DOMContentLoaded', function() {
        // Get all links with hash
        const links = document.querySelectorAll('a[href^="#"]');

        // Add click event to each link
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                // Only if the hash is not empty (not just '#')
                if(this.hash.length > 1) {
                    e.preventDefault();

                    const target = document.querySelector(this.hash);
                    if(target) {
                        // Smooth scroll to the target
                        window.scrollTo({
                            top: target.offsetTop,
                            behavior: 'smooth'
                        });
                        
                        // Update URL hash without scrolling
                        history.pushState(null, null, this.hash);
                    }
                }
            });
        });
    });
</script>

</body>
</html>
