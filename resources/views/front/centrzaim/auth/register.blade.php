@extends('layouts.app')
@section('content')
    <div>

    </div>

    <div class="wrapper">
        <main class="main">
            <div class="main-info">
                <div class="container">
                    <h1 class="title main-info__title">Получить деньги под 0% без проверок КИ</h1>
                    <p class="main-info__text">Одобрим даже с текущими просрочками более 90 дней. Подай заявку в течении
                        10<br> минут получи первый заём на 21 день под 0%</p>
                    <div class="main-info__advantages">
                        <!-- begin advantages -->
                        <ul class="advantages">
                            <li class="advantages__item">
                                <svg class="advantages__icon">
                                    <use xlink:href="/assets/ctr/img/sprite.svg#icon-time"/>
                                </svg>
                                Деньги у вас за 10 минут
                            </li>
                            <li class="advantages__item">
                                <svg class="advantages__icon">
                                    <use xlink:href="/assets/ctr/img/sprite.svg#icon-thumbs"/>
                                </svg>
                                От 99% вероятность одобрения
                            </li>
                            <li class="advantages__item">
                                <svg class="advantages__icon">
                                    <use xlink:href="/assets/ctr/img/sprite.svg#icon-check"/>
                                </svg>
                                Первый займ бесплатно
                            </li>
                        </ul>
                        <!-- end advantages -->
                    </div>
                    <!-- begin calc -->
                    @include('front.centrzaim.blocks.miazaim-form')
                </div>
            </div>
            <div class="container">
                <!-- begin additional -->
                <ul class="additional main__additional">
                    <li class="additional__item bordered-block">
                        <div class="additional__icon">
                            <svg class="additional__light">
                                <use xlink:href="/assets/ctr/img/sprite.svg#icon-light"/>
                            </svg>
                        </div>
                        <p class="additional__title">Моментальное решение</p>
                        <p class="additional__text text-light">Решение по займу принимается за 3 минуты</p>
                    </li>
                    <li class="additional__item bordered-block">
                        <div class="additional__icon">
                            <svg class="additional__docs">
                                <use xlink:href="/assets/ctr/img/sprite.svg#icon-docs"/>
                            </svg>
                        </div>
                        <p class="additional__title">Минимум для получения</p>
                        <p class="additional__text text-light">Достаточно только паспорта для подтверждения
                            займа</p>
                    </li>
                    <li class="additional__item bordered-block">
                        <div class="additional__icon">
                            <svg class="additional__garant">
                                <use xlink:href="/assets/ctr/img/sprite.svg#icon-garant"/>
                            </svg>
                        </div>
                        <p class="additional__title">Выдаем 99%</p>
                        <p class="additional__text text-light">Одобряем с любой кредитной историей</p>
                    </li>
                </ul>
                <!-- end additional -->
                <!-- begin methods -->
                <div class="methods main__methods">
                    <div class="methods__info">
                        <h2 id="methods" class="title methods__title">Способы получения</h2>
                        <p class="methods__text text-light">Выберите необходимую платежную систему и нажмите<br> для
                            получения денег:</p>
                        <ul class="methods-list methods__list">
                            <li class="methods-list__item bordered-block">
                                <img loading="lazy" data-src="/assets/ctr/img/mastercard.svg" alt="Mastercard">
                            </li>
                            <li class="methods-list__item bordered-block">
                                <img loading="lazy" data-src="/assets/ctr/img/visa.svg" alt="visa">
                            </li>
                            <li class="methods-list__item bordered-block">
                                <img loading="lazy" data-src="/assets/ctr/img/mir.svg" alt="mir">
                            </li>
                            <li class="methods-list__item bordered-block">
                                <img loading="lazy" data-src="/assets/ctr/img/xz.svg" alt="xz">
                            </li>
                            <li class="methods-list__item bordered-block">
                                <img loading="lazy" data-src="/assets/ctr/img/crown.svg" alt="Корона">
                            </li>
                            <li class="methods-list__item bordered-block">
                                <img loading="lazy" data-src="/assets/ctr/img/qiwi.svg" alt="Qiwi">
                            </li>
                        </ul>
                        <btn class="btn methods__btn" data-next>Получить деньги</btn>
                    </div>
                    <div class="methods__img">
                        <img srcset="/assets/ctr/img/methods@2x.webp 2x, /assets/ctr/img/methods.webp"
                             src="/assets/ctr/img/methods_origin.png" alt="Способы получения">
                    </div>

                    <button class="btn methods__btn methods__btn_visible-xs" data-next>Получить деньги</button>
                </div>
                <!-- end methods -->
                <!-- begin reviews -->
                <div class="reviews main__reviews">
                    <h2 id="reviews" class="title reviews__title">Отзывы</h2>
                    <p class="reviews__text text-light text-light">Нам доверяют более 1000 клиентов по всей
                        России</p>
                    <ul class="reviews__list">
                        <li class="reviews__item bordered-block">
                            <div class="reviews__header">
                                <p class="reviews__author">Алексей Чернов</p>
                                <span class="reviews__rating">4,5</span>
                            </div>
                            <p class="reviews__phone">+7 (903) 992-ХХ-ХХ</p>
                            <p class="reviews__text text-light">Всегда все работает. Можно найти хорошие условия.Что
                                безумно удобно!В общем пока что тут самые лучшие предложения)Советую!</p>
                        </li>
                        <li class="reviews__item bordered-block">
                            <div class="reviews__header">
                                <p class="reviews__author">Александр Белов</p>
                                <span class="reviews__rating">5</span>
                            </div>
                            <p class="reviews__phone">+7 (903) 332-ХХ-ХХ</p>
                            <p class="reviews__text text-light">Очень удобный интерфейс, все просто и понятно,
                                стандартный процент, ничего не завышают, спасибо, пользуюсь и буду пользоваться</p>
                        </li>
                        <li class="reviews__item bordered-block">
                            <div class="reviews__header">
                                <p class="reviews__author">Алена Романова</p>
                                <span class="reviews__rating">5</span>
                            </div>
                            <p class="reviews__phone">+7 (937) 924-ХХ-ХХ</p>
                            <p class="reviews__text text-light">Ваш сайт стал настоящим спасением в сложной ситуации.
                                По совету подруги обратилась к вам. Оформила заявку через телефон и уже через 20 мин
                                получила деньги. Спасибо вам за помощь!</p>
                        </li>
                        <li class="reviews__item bordered-block">
                            <div class="reviews__header">
                                <p class="reviews__author">Юсуп Белов</p>
                                <span class="reviews__rating">5</span>
                            </div>
                            <p class="reviews__phone">+7 (997) 449-ХХ-ХХ</p>
                            <p class="reviews__text text-light">Это мой первый займ, мне понравилось.Самые лучшие
                                условия по займам!Очень выручает когда в моменте нет денег.</p>
                        </li>
                        <li class="reviews__item bordered-block">
                            <div class="reviews__header">
                                <p class="reviews__author">Виктор Боярский</p>
                                <span class="reviews__rating">4,8</span>
                            </div>
                            <p class="reviews__phone">+7 (999) 223-ХХ-ХХ</p>
                            <p class="reviews__text text-light">Спасибо!!! Благодаря Социальному Займу, я смог решить
                                финансовые трудности быстро и без лишних заморочек. Условия займа подобрали отличные,
                                деньги пришли очень быстро.</p>
                        </li>
                        <li class="reviews__item bordered-block">
                            <div class="reviews__header">
                                <p class="reviews__author">Александр Белов</p>
                                <span class="reviews__rating">4.4</span>
                            </div>
                            <p class="reviews__phone">+7 (997) 449-ХХ-ХХ</p>
                            <p class="reviews__text text-light">Был приятно удивлён отсутствием дополнительных услуг.
                                Получил только то, что было необходимо. Оформление прошло быстро, без долгой волокиты.
                                Очень доволен сервисом и качеством обслуживания. Спасибо.</p>
                        </li>
                        <template>
                            <li class="reviews__item bordered-block">
                                <div class="reviews__header">
                                    <p class="reviews__author">Адель Пелевина</p>
                                    <span class="reviews__rating">5</span>
                                </div>
                                <p class="reviews__phone">+7 (939) 452-ХХ-ХХ</p>
                                <p class="reviews__text text-light">Спасибо большое!!!!!</p>
                            </li>
                            <li class="reviews__item bordered-block">
                                <div class="reviews__header">
                                    <p class="reviews__author">Георгий Хволынский</p>
                                    <span class="reviews__rating">5</span>
                                </div>
                                <p class="reviews__phone">+7 (927) 203-ХХ-ХХ</p>
                            </li>
                            <li class="reviews__item bordered-block">
                                <div class="reviews__header">
                                    <p class="reviews__author">Валерия Печеркина</p>
                                    <span class="reviews__rating">4</span>
                                </div>
                                <p class="reviews__phone">+7 (927) 363-ХХ-ХХ</p>
                            </li>
                            <li class="reviews__item bordered-block">
                                <div class="reviews__header">
                                    <p class="reviews__author">Айнур Мароновичев</p>
                                    <span class="reviews__rating">4</span>
                                </div>
                                <p class="reviews__phone">+7 (927) 436-ХХ-ХХ</p>
                            </li>
                            <li class="reviews__item bordered-block">
                                <div class="reviews__header">
                                    <p class="reviews__author">Карина Петрова</p>
                                    <span class="reviews__rating">5</span>
                                </div>
                                <p class="reviews__phone">+7 (908) 233-ХХ-ХХ</p>
                            </li>
                            <li class="reviews__item bordered-block">
                                <div class="reviews__header">
                                    <p class="reviews__author">Валентина Васюкаова</p>
                                    <span class="reviews__rating">5</span>
                                </div>
                                <p class="reviews__phone">+7 (903) 459-ХХ-ХХ</p>
                            </li>
                        </template>
                    </ul>
                </div>
                <!-- end reviews -->
                <!-- begin calc -->
                <h2 class="title title_calc-xs">Получить деньги под 0% от государства</h2>
                <div class="calc main-info__calc">
                    <div class="flex flex-col gap-8">
                        <p class="calc__title title"><span class="text-info">Заполните</span> заявку прямо сейчас и
                            получите <span class="text-attention">решение в <span class="timeGetMoney"
                                                                                  data-time>18:32</span></span>
                        </p>

                        @include('blocks.components.money-slider')
                        <div class="app"></div>
                    </div>
                </div>
                <!-- end calc -->
                <!-- begin faq -->
                <div id="faq" class="faq main__faq">
                    <h2 class="title">Частые вопросы</h2>
                    <div class="faq__container">
                        <div class="faq__item bordered-block">
                            <div class="faq__header">
                                <p class="faq__title">Как долго обрабатывается заявка?</p>
                                <div class="faq__icon">
                                    <svg>
                                        <use xlink:href="/assets/ctr/img/sprite.svg#icon-plus">
                                    </svg>
                                </div>
                            </div>
                            <div class="faq__content">
                                <p class="faq__text text-light">Заявка на займ обрабатывается от нескольких секунд
                                    до 15
                                    минут в самых редких случаях.</p>
                            </div>
                        </div>
                        <div class="faq__item bordered-block">
                            <div class="faq__header">
                                <p class="faq__title">Как получить деньги?</p>
                                <div class="faq__icon">
                                    <svg>
                                        <use xlink:href="/assets/ctr/img/sprite.svg#icon-plus">
                                    </svg>
                                </div>
                            </div>
                            <div class="faq__content">
                                <p class="faq__text text-light">Вы можете получить займ на карту, наличными или на
                                    банковский счет</p>
                            </div>
                        </div>
                        <div class="faq__item bordered-block">
                            <div class="faq__header">
                                <p class="faq__title">География сервиса?</p>
                                <div class="faq__icon">
                                    <svg>
                                        <use xlink:href="/assets/ctr/img/sprite.svg#icon-plus">
                                    </svg>
                                </div>
                            </div>
                            <div class="faq__content">
                                <p class="faq__text text-light">Работаем на всей территории Российской Федерации</p>
                            </div>
                        </div>
                        <div class="faq__item bordered-block">
                            <div class="faq__header">
                                <p class="faq__title">Какова стоимость услуги?</p>
                                <div class="faq__icon">
                                    <svg>
                                        <use xlink:href="/assets/ctr/img/sprite.svg#icon-plus">
                                    </svg>
                                </div>
                            </div>
                            <div class="faq__content">
                                <p class="faq__text text-light">Услуга предоставляется бесплатно</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end faq -->
            </div>
        </main>
    </div>

@endsection
@section('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite('resources/assets/projects/miazaim/js/app.jsx')
    <script>
        document.getElementsByClassName('money-btn')[0].addEventListener('click', function () {
            if (document.getElementById('fioHiddenInput').getAttribute('has-error') == '1') {
                console.log('not send')
                return
            }

            // Update the hidden form fields with the current slider values
            const amountLabel = document.querySelector('.amountLabel');
            const daysLabel = document.querySelector('.daysLabel');

            if (amountLabel) {
                // Extract number from "63К ₽" or "63000 ₽" format
                const amountText = amountLabel.innerText;
                let amount = amountText.replace(/[^\d]/g, '');
                document.getElementById('sliderAmount').value = amount;
            }

            if (daysLabel) {
                // Extract number from "12 дней" format
                const daysText = daysLabel.innerText;
                let days = daysText.replace(/[^\d]/g, '');
                document.getElementById('sliderDays').value = days;
            }

            document.getElementById('submitFioForm').click()
        })

        const element = document.querySelector('input[name="mphone"]');

        const phoneMask = {
            mask: '+{7}(000)000-00-00'
        };

        const dateMask = {
            mask: Date,
            pattern: 'd.m.YYYY',
            blocks: {
                d: {
                    mask: IMask.MaskedRange,
                    from: 1,
                    to: 31,
                    maxLength: 2,
                },
                m: {
                    mask: IMask.MaskedRange,
                    from: 1,
                    to: 12,
                    maxLength: 2,
                },
                Y: {
                    mask: IMask.MaskedRange,
                    from: 1900,
                    to: 2999,
                }
            }
        };

        const mask = IMask(element, phoneMask);

        // Update hidden fields when sliders change
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize the hidden fields with the initial slider values
            const amountLabel = document.querySelector('.amountLabel');
            const daysLabel = document.querySelector('.daysLabel');

            if (amountLabel) {
                const amountText = amountLabel.innerText;
                let amount = amountText.replace(/[^\d]/g, '');
                document.getElementById('sliderAmount').value = amount;
            }

            if (daysLabel) {
                const daysText = daysLabel.innerText;
                let days = daysText.replace(/[^\d]/g, '');
                document.getElementById('sliderDays').value = days;
            }

            // Set up event listener for slider changes
            document.querySelectorAll('.money-slider, .day-slider').forEach(slider => {
                if (slider.noUiSlider) {
                    slider.noUiSlider.on('update', function() {
                        const amountLabel = document.querySelector('.amountLabel');
                        const daysLabel = document.querySelector('.daysLabel');

                        if (amountLabel) {
                            const amountText = amountLabel.innerText;
                            let amount = amountText.replace(/[^\d]/g, '');
                            document.getElementById('sliderAmount').value = amount;
                        }

                        if (daysLabel) {
                            const daysText = daysLabel.innerText;
                            let days = daysText.replace(/[^\d]/g, '');
                            document.getElementById('sliderDays').value = days;
                        }
                    });
                }
            });

            // validateAndSubmitForm(
            //     'fioForm',
            //     document.getElementById('fioForm').attributes.validateurl.value,
            //     'complete_step_register'
            // );
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const lazyLoadImages = document.querySelectorAll('img[loading=lazy]');
            console.log(lazyLoadImages)
            if ("IntersectionObserver" in window) {
                let observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            let img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove("lazyload");
                            observer.unobserve(img);
                        }
                    });
                });

                lazyLoadImages.forEach(img => {
                    observer.observe(img);
                });
            } else {
                // Fallback for browsers without IntersectionObserver support
                let lazyLoadThrottleTimeout;

                function lazyLoad() {
                    if (lazyLoadThrottleTimeout) {
                        clearTimeout(lazyLoadThrottleTimeout);
                    }

                    lazyLoadThrottleTimeout = setTimeout(function () {
                        let scrollTop = window.pageYOffset;
                        lazyLoadImages.forEach(img => {
                            if (img.offsetTop < (window.innerHeight + scrollTop)) {
                                img.src = img.dataset.src;
                                img.classList.remove('lazyload');
                            }
                        });
                        if (lazyLoadImages.length == 0) {
                            document.removeEventListener("scroll", lazyLoad);
                            window.removeEventListener("resize", lazyLoad);
                            window.removeEventListener("orientationChange", lazyLoad);
                        }
                    }, 20);
                }

                document.addEventListener("scroll", lazyLoad);
                window.addEventListener("resize", lazyLoad);
                window.addEventListener("orientationChange", lazyLoad);
            }
        });
    </script>
@endsection
@section('scripts')
    <script>
        {{--frontConfig.shouldAG = @json(\App\Services\AccountService\AccountSourceService::getSource() !== null);--}}
    </script>
@endsection