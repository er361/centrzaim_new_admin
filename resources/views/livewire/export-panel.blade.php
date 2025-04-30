{{-- resources/views/livewire/export-panel.blade.php --}}

<div>
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">
            Панель экспорта данных
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="webmaster-selector">Вебмастер (API ID)</label>
                    <select wire:model.live="webmasterId" id="webmaster-selector" class="form-control select2">
                        <option value="">Не выбран</option>
                        @foreach($webmasters as $api_id => $id)
                            <option value="{{ $id }}">{{ $api_id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label for="export-panel-date-range">Период</label>
                    <input type="text" id="export-panel-date-range" class="form-control" autocomplete="off"/>
                    <input type="hidden" wire:model="dateRange" id="export-panel-date-range-hidden"/>
                </div>
                <div class="col-md-4 form-group" style="margin-top: 24px;">

                    <button wire:click="export" class="btn btn-primary">Экспорт</button>

                    <button wire:click="resetFilters" class="btn btn-default">Сбросить</button>
                </div>
            </div>
        </div>

    </div>
        <script>
            // Используем событие livewire:initialized для инициализации датапикера и select2
            document.addEventListener('livewire:initialized', function() {
                console.log('Событие livewire:initialized сработало - начинаем инициализацию компонентов');
                initDateRangePicker();
                initSelect2();
            });

            function initSelect2() {
                if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
                    console.error('jQuery или select2 не загружены!');
                    return;
                }

                const selectorId = 'webmaster-selector';
                const selectorElement = document.getElementById(selectorId);

                if (!selectorElement) {
                    console.error(`Элемент #${selectorId} не найден на странице`);
                    return;
                }

                // Деинициализируем select2, если он уже был инициализирован
                if ($(selectorElement).hasClass('select2-hidden-accessible')) {
                    $(selectorElement).select2('destroy');
                }

                // Инициализируем select2
                $(selectorElement).select2({
                    placeholder: 'Выберите вебмастера',
                    allowClear: true
                });

                // Обрабатываем изменения в select2
                $(selectorElement).on('change', function (e) {
                    // Получаем выбранное значение
                    const value = $(this).val();
                    
                    // Обновляем Livewire модель
                    @this.set('webmasterId', value);
                });

                // Обновляем select2 при изменении значения в Livewire
                Livewire.on('filtersReset', function () {
                    $(selectorElement).val('').trigger('change');
                });
            }

            function initDateRangePicker() {
                const datePickerId = 'export-panel-date-range';
                const datePickerElement = document.getElementById(datePickerId);

                // Проверка наличия необходимых библиотек
                if (typeof $ === 'undefined') {
                    console.error('jQuery не загружен!');
                    return;
                }

                if (typeof moment === 'undefined') {
                    console.error('moment.js не загружен!');
                    return;
                }

                if (typeof $.fn.daterangepicker === 'undefined') {
                    console.error('daterangepicker не загружен!');
                    return;
                }

                if (!datePickerElement) {
                    console.error(`Элемент #${datePickerId} не найден на странице`);
                    return;
                }

                console.log(`Инициализация daterangepicker для #${datePickerId}`);

                // Проверяем, не инициализирован ли уже daterangepicker
                if ($(datePickerElement).data('daterangepicker')) {
                    console.log('DateRangePicker уже инициализирован, пропускаем...');
                    return;
                }

                // Инициализация daterangepicker
                $(datePickerElement).daterangepicker({
                    autoApply: true,
                    locale: {
                        format: "DD.MM.YYYY",
                        separator: " - ",
                        applyLabel: "Применить",
                        cancelLabel: "Отмена",
                        fromLabel: "От",
                        toLabel: "До",
                        customRangeLabel: "Свой период",
                        weekLabel: "W",
                        daysOfWeek: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
                        monthNames: [
                            "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь",
                            "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"
                        ],
                        firstDay: 1
                    },
                    ranges: {
                        'Сегодня': [moment(), moment()],
                        'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                        'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                        'Этот месяц': [moment().startOf('month'), moment().endOf('month')],
                        'Предыдущий месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    showCustomRangeLabel: false,
                    alwaysShowCalendars: true,
                    autoUpdateInput: false
                });

                // Получаем ID компонента Livewire
                let livewireId;
                try {
                    const livewireElement = document.querySelector('[wire\\:id]');
                    livewireId = livewireElement ? livewireElement.getAttribute('wire:id') : null;
                } catch (e) {
                    console.error('Ошибка при получении ID компонента Livewire:', e);
                }

                // При выборе периода обновляем Livewire переменную и отображение
                $(datePickerElement).on('apply.daterangepicker', function (ev, picker) {
                    const range = picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY');
                    $(this).val(range);

                    // Обновляем Livewire компонент
                    if (typeof Livewire !== 'undefined') {
                        try {
                            @this.set('dateRange', range);
                        } catch (e) {
                            console.error('Ошибка при обновлении Livewire компонента:', e);
                            // Запасной вариант, если @this не работает
                            if (livewireId) {
                                Livewire.find(livewireId).set('dateRange', range);
                            }
                        }
                    }
                });

                // Сброс значения при отмене
                $(datePickerElement).on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');

                    // Обновляем Livewire компонент
                    if (typeof Livewire !== 'undefined') {
                        try {
                            @this.set('dateRange', '');
                        } catch (e) {
                            console.error('Ошибка при сбросе значения в Livewire компоненте:', e);
                            // Запасной вариант, если @this не работает
                            if (livewireId) {
                                Livewire.find(livewireId).set('dateRange', '');
                            }
                        }
                    }
                });

                // Слушаем событие сброса фильтров
                Livewire.on('filtersReset', function () {
                    $(datePickerElement).val('');
                });

                console.log('DateRangePicker успешно инициализирован');
            }
        </script>

</div>