export function updateTime(selector) {
    // Получаем все элементы с классом 'time-plus-15'
    const timeElements = document.querySelectorAll(selector);

    // Текущее время + 15 минут
    const now = new Date();
    now.setMinutes(now.getMinutes() + 15);

    // Форматируем время, например, "14:25"
    const timeString = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();

    // Устанавливаем время во все найденные элементы
    timeElements.forEach(element => {
        element.textContent = timeString;
    });
}

export function initSliders(){
    const amountFromLocalStorage = localStorage.getItem('sum')
    const daysFromlocalStorage = localStorage.getItem('days');

    // Устанавливаем начальное значение слайдера
    const startValue = amountFromLocalStorage ? parseInt(amountFromLocalStorage.replace(/\D/g, ''), 10) : 20000;
    const startDays = daysFromlocalStorage ? parseInt(daysFromlocalStorage.replace(/\D/g, ''), 10) : 12;
    let moneySlider, daySlider;

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
                    return value.toFixed(0);
                },
                from: function (value) {
                    return parseFloat(value);
                }
            }
        });

        moneySlider = slider.noUiSlider;

        slider.noUiSlider.on('update', function (values, handle) {
            slider.closest('.flex-col').querySelector('.amountLabel').innerText = values[handle] + ' ₽';
            localStorage.setItem('sum', values[handle]);
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

        daySlider = slider.noUiSlider;

        slider.noUiSlider.on('update', function (values, handle) {
            console.log('set intems days');
            let value = values[handle];
            slider.closest('.flex-col').querySelector('.daysLabel').innerText = value + ' дней';
            localStorage.setItem('days', value);
        });
    });
}

console.log('lib js file loaded');
