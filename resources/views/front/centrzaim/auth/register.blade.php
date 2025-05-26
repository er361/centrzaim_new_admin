@extends('layouts.app')
@section('styles')
    <style>
        body {
            background: #FFFFFF;
        }
    </style>
@endsection
@section('content')
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

            validateAndSubmitForm(
                'fioForm',
                document.getElementById('fioForm').attributes.validateurl.value,
                'send_form'
            );
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