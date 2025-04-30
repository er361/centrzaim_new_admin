import {updateTime} from "./lib.js";


document.addEventListener('DOMContentLoaded', function () {

    // Получаем параметры из URL
    const urlParams = new URLSearchParams(window.location.search);
    const amountFromUrl = urlParams.get('amount');
    const daysFromUrl = urlParams.get('days');

    // Устанавливаем начальное значение слайдера
    const startValue = amountFromUrl ? parseInt(amountFromUrl.replace(/\D/g, ''), 10) : frontConfig.sliderSumm;
    const startDays = daysFromUrl ? parseInt(daysFromUrl.replace(/\D/g, ''), 10) : 12;

    document.querySelectorAll('.money-slider-container .money-slider').forEach((slider, index) => {
        noUiSlider.create(slider, {
            start: startValue,
            connect: [true, false],
            tooltips: false,
            step: 1000,
            range: {
                'min': 1000,
                'max': 100000
            },
            format: {
                to: function (value) {
                    return value.toFixed(0) + ' ₽';
                },
                from: function (value) {
                    return parseFloat(value);
                }
            }
        });

        slider.noUiSlider.on('update', function (values, handle) {
            slider.closest('.flex-col').querySelector('.amountLabel').innerText = values[handle];
        });
    });

    document.querySelectorAll('.money-slider-container .day-slider').forEach((slider, index) => {
        noUiSlider.create(slider, {
            start: startDays,
            connect: [true, false],
            tooltips: false,
            step: 1,
            range: {
                'min': 5,
                'max': 365
            },
            format: {
                to: function (value) {
                    return value.toFixed(0);
                },
                from: function (value) {
                    return parseFloat(value);
                }
            }
        });

        slider.noUiSlider.on('update', function (values, handle) {
            slider.closest('.flex-col').querySelector('.daysLabel').innerText = values[handle] + ' дней';
        });
    });

    updateTime('.timeGetMoney');

    setInterval(updateTime, 60000);

    console.log('DOM is ready')
    document.getElementById('logo').addEventListener('click', function () {
        window.location.href = '/'
    })

    function toggleMenu() {
        document.getElementById('showMenu').classList.toggle('rotate-[135deg]');
        document.getElementById('showMenuContent').classList.toggle('!w-full');
    }

    // Обработчик клика для кнопки открытия/закрытия меню
    document.getElementById('showMenu').addEventListener('click', toggleMenu);

    // Обработчики кликов для ссылок мобильного меню
    document.querySelectorAll('.mobile-menu-link').forEach((link) => {
        link.addEventListener('click', function () {
            toggleMenu(); // Вызываем функцию toggleMenu при клике на ссылку
        });
    });

})

console.log('init js file loaded')
