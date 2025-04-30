import {declOfNum, zeroPad} from "../utils.js";

export let Range = new class {
    init() {
        this.initRangeInput();
        this.initDateInput();
        this.initInitialState();
    }

    initInitialState() {
        let currentSum = localStorage.getItem('current_sum');
        let currentPeriod = localStorage.getItem('current_period_days');

        if (!currentSum) {
            currentSum = $('.range.summ .r_current, .main-range__total').first().text().trim();
        }

        if (!currentSum) {
            currentSum = '15000';
        }

        if (!currentPeriod) {
            currentPeriod = 15;
        }

        $('.range.summ .r_current, .main-range__total').text(currentSum);
        $('.range.summ input[type="range"], .main-range__slider input[type="range"]').val(this.getInteger(currentSum)).trigger('change');

        this.updateReturnAmounts(currentSum);
        this.updateReturnDays(currentPeriod);
    }

    initDateInput() {
        let _this = this;

        $('.main-range__days .main-range__days-btn').on('click', function () {
            let value = $(this).data('value');
            localStorage.setItem('current_period_days', value);
            _this.updateReturnDays(value);
        })
    }

    initRangeInput() {
        let _this = this;

        $('input[type="range"]').each(function() {
            // Иногда браузер оптимизирует background (на 100%), обратное изменение по регулярке не работает
            const background = $(this).css('background-image');

            $(this).on('input change', function () {
                let currentValue = $(this).val(),
                    minInputValue = $(this).attr('min'),
                    maxInputValue = $(this).attr('max');
                let val = (currentValue - minInputValue) / (maxInputValue - minInputValue);

                const regexPercent = /(\d+%)/gm;
                const newBackground = background.replace(regexPercent, (val*100)+'%');

                $(this).css('background-image', newBackground);

                let current = $(this).parent('.range').find('.input-range.numbers');
                let minValue = parseInt(minInputValue);
                let maxValue = parseInt(maxInputValue);
                let newValue = Math.round(minValue + (maxValue - minValue) * val);

                localStorage.setItem('current_sum', newValue);
                current.val(newValue);

                _this.updateReturnAmounts(newValue);
            });
        })

        $('input.input-range').on('input', function () {
            let value = this.value.replace(/[^0-9]/g, '');
            let max = $(this).data('max');
            let min = $(this).data('min');

            if (value > max) {
                this.value = max;
            } else if (value < min) {
                this.value = min;
            } else {
                this.value = value;
            }

            $(this).parent('.range')
                .find('input[type="range"]')
                .val(this.value)
                .trigger('change');
        });
    }

    updateReturnAmounts(currentValue) {
        const $returnAmountElement = $('.return_amount, .main-range__total, .step-info__get-sum');

        if ($returnAmountElement.length) {
            $returnAmountElement.text(currentValue + ' ₽');
        }
    }

    updateReturnDays(currentValue) {
        if (!currentValue) {
            return;
        }

        $('.return_days').text(
            currentValue + ' ' + declOfNum(currentValue, ['день', 'дня', 'дней'])
        );

        const secondsBeforeReturn = currentValue * 24 * 60 * 60 * 1000;
        const returnDate = new Date();
        returnDate.setTime((new Date()).getTime() + secondsBeforeReturn);

        const returnDays = zeroPad(returnDate.getDate(), 2);
        const returnMonths = zeroPad(returnDate.getMonth() + 1, 2); // Добавляем в начало "0"
        $('.return_date').text(
            returnDays + '.' + returnMonths + '.' + returnDate.getFullYear()
        );

        $('.main-range__days .main-range__days-btn.active').removeClass('active');
        $(`.main-range__days .main-range__days-btn[data-value="${currentValue}"]`).addClass('active');
    }

    getInteger(text) {
        if (text) {
            return parseInt(text.match(/\d/g).join(''));
        }

        return 0;
    }
}