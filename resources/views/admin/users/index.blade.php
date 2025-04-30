@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.users.title')</h3>

    <div class="form-group">
        @can('user_full_access')
            <form method="post" action="{{ route('admin.users.export', request()->all()) }}" style="display: inline;">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-primary">Экспорт</button>
            </form>
        @endcan
        @can('user_create')
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Создать</a>
        @endcan

    </div>
    @if(\Illuminate\Support\Facades\Auth::user()?->isSuperAdmin())
        <livewire:export-panel/>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body">
            <form>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="webmaster_id">Вебмастер</label>
                        {!! Form::select('webmaster_id', [null => 'Не выбран'] + $webmasters->all(), old('webmaster_id', request('webmaster_id')), ['class' => 'form-control select2']) !!}
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="created_at_dates">Дата регистрации</label>
                        <input type="text" name="created_at_dates" id="created_at_dates"
                               value="{{ request('created_at_dates') }}" class="form-control"
                               autocomplete="off"/>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="payment_plan">Схема пользователя</label>
                        {!! Form::select('payment_plan', [null => 'Не выбран'] + $paymentPlans, old('payment_plan', request('payment_plan')), ['class' => 'form-control select2']) !!}
                    </div>
                    @can('user_create')
                        <div class="col-md-4 form-group">
                            <label for="payment_plan">Роль</label>
                            {!! Form::select('role_id', [null => 'Не выбрана'] + $roles, old('role_id', request('role_id')), ['class' => 'form-control select2']) !!}
                        </div>
                    @endcan
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input type="submit" value="Отфильтровать" class="btn btn-primary"/>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable user-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Создан</th>
                    <th>Успешных рекуррентных платежей</th>
                    <th>&nbsp;</th>

                </tr>
                </thead>
            </table>

            <div class="form-group">
                <div class="input-group mb-3">
                    <input type="number" id="page_number" class="form-control" placeholder="Перейти к странице">
                    <div class="input-group-appendmt-1">
                        <button class="btn btn-primary" type="button" id="goto_page">Перейти</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>

        $(document).ready(function () {
            window.dtDefaultOptions.order = [[0, 'desc']];
            window.dtDefaultOptions.ajax = '{!! route(request()->route()->getName(), request()->all()) !!}';
            window.dtDefaultOptions.columns = [
                {data: 'id', name: 'id'},
                {data: 'email', name: 'email'},
                {data: 'created_at', name: 'created_at'},
                {data: 'recurrent_payment_success_count', name: 'recurrent_payment_success_count', searchable: false},

                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();

        });

        $('#goto_page').on('click', function (e) {
            e.preventDefault();
            $('.ajaxTable').DataTable().page($('#page_number').val() - 1).draw('page')
        });
    </script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    <script type="text/javascript">
        $(function () {
            $('input[name="created_at_dates"]').daterangepicker({
                autoApply: true,
                "locale": {
                    "format": "DD.MM.YYYY",
                    "separator": " - ",
                    "applyLabel": "Применить",
                    "cancelLabel": "Отмена",
                    "fromLabel": "От",
                    "toLabel": "До",
                    "customRangeLabel": "Свой период",
                    "weekLabel": "W",
                    "daysOfWeek": [
                        "Вс",
                        "Пн",
                        "Вт",
                        "Ср",
                        "Чт",
                        "Пт",
                        "Сб"
                    ],
                    "monthNames": [
                        "Январь",
                        "Февраль",
                        "Март",
                        "Апрель",
                        "Май",
                        "Июнь",
                        "Июль",
                        "Август",
                        "Сентябрь",
                        "Октябрь",
                        "Ноябрь",
                        "Декабрь"
                    ],
                    "firstDay": 1
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
                autoUpdateInput: false,
            });

            $('input[name="created_at_dates"]').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            });

            $('input[name="created_at_dates"]').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endsection