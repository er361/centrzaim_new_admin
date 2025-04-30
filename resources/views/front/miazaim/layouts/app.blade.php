@props(['hasTimer' => true])
        <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="yandex-verification" content="6a1a5b68d9cdc5c6"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Займы онлайн на карту срочно без отказа | miaZaim</title>

    <!-- Fonts -->
    {{--    <link rel="preconnect" href="https://fonts.bunny.net">--}}
    {{--    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>--}}
    <link rel="stylesheet" href="/assets/miazaim/css/nouislider.min.css">

    <link rel="preload" href="/assets/miazaim/fonts/Lato-Regular.woff2" as="font" type="font/woff2"
          crossorigin="anonymous">
    <link rel="preload" href="/assets/miazaim/fonts/Montserrat-Medium.woff2" as="font" type="font/woff2"
          crossorigin="anonymous">
    <link rel="preload" href="/assets/miazaim/fonts/GolosText-Regular.woff2" as="font" type="font/woff2"
          crossorigin="anonymous">

    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    @vite('resources/assets/projects/miazaim/css/app.css')
    @yield('head_scripts')
</head>
<body class="font-body text-black-text">
<header class="container sm:px-8 px-2 sm:py-2 py-4 flex flex-row justify-between
sm:gap-8 gap-4  sm:text-base text-sm">
    @include('blocks.components.logo')
    @yield('header')
    @if($hasTimer)
        <div class="flex flex-row sm:gap-2 gap-2 items-center">
            <img src="/assets/miazaim/imgs/clock.svg" alt="clock" class="hidden sm:block">
            <span class="shrink-0">Деньги у вас</span>
            <span class="timeGetMoney text-lg font-bold">18:32</span>
            <img id="showMenu" src="/assets/miazaim/imgs/logo_romb.svg" alt="logo2" class="sm:hidden
        block cursor-pointer transition duration-700">
        </div>
    @endif
    @if(auth()->check())
        <div class="hidden md:block">
            @include('blocks.logout')
        </div>
    @endif
</header>
<div id="showMenuContent" class="h-full sm:hidden w-0 overflow-hidden  absolute z-10 text-white bg-black-text transition-[width] duration-700
    bg-[url('/assets/miazaim/imgs/vector_mob_menu.svg')] bg-no-repeat bg-[center_bottom_4rem] bg-cover
">
    <ul class="text-[28px] font-semibold">
        <li class="py-3 px-5 cursor-pointer">
            <a class="mobile-menu-link" href="#why-us">Почему мы?</a>
        </li>
        <li class="py-3 px-5 cursor-pointer">
            <a class="mobile-menu-link" href="#how-to">Как оформить заём</a>
        </li>
        <li class="py-3 px-5 cursor-pointer">
            <a class="mobile-menu-link" href="#money-to">Способы получения</a>
        </li>
        @if(auth()->check())
            <li class="py-3 px-5 cursor-pointer">
                @include('blocks.logout', ['class' => 'bg-text-black !text-white !p-0'])
            </li>
        @endif

    </ul>
</div>
<div class="text-black-text">
    @yield('content')
</div>
@include('blocks.footer')
<script src="/assets/miazaim/js/nouislider.min.js"></script>
@vite([
    'resources/assets/projects/miazaim/js/app.js',
//    'resources/assets/js/app.js'
])
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
        if(!frontConfig.isRedirectEnabled)
            return;

        if(frontConfig.redirectUrl)
            redirectUrl = frontConfig.redirectUrl;


        // Позволяем открыть форму в новой вкладке
        setTimeout(() => {
            window.location.href = redirectUrl;
        }, frontConfig.redirectDelay); // Задержка 100 мс для гарантии
    }

</script>

@vite('resources/assets/js/app.js')

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

</body>
</html>
