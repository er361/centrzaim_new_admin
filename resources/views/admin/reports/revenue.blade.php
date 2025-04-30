@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Отчет по выручке</h3>

    <form class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Параметры отчета
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="date_from">Начальная дата</label>
                            <input type="text" name="date_from" id="date_from" class="form-control datepicker-here"
                                   value="{{ request('date_from') }}" required autocomplete="off"/>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="date_to">Конечная дата</label>
                            <input type="text" name="date_to" id="date_to" class="form-control datepicker-here"
                                   value="{{ request('date_to') }}" required autocomplete="off"/>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="group_type">Тип группировки</label>
                            <select name="group_type" required class="form-control">
                                <option value="1" @if(request('group_type') === '1') selected @endif>По дням</option>
                                <option value="2" @if(request('group_type') === '2') selected @endif>По месяцам</option>
                                <option value="3" @if(request('group_type') === '3') selected @endif>По годам</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="source_id">Партнерская программа</label>
                            <select name="source_id[]" class="form-control select2" multiple="multiple">
                                <option value="">Пожалуйста, выберите партнерскую программу</option>
                                <option value="all" @if(in_array('all', (array)request('source_id', []))) selected @endif>Все партнерские
                                    программы
                                </option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}"
                                            @if(in_array((string) $source->id, (array)request('source_id', []))) selected @endif>{{ $source->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="webmaster_id">Вебмастер</label>
                            <select name="webmaster_id[]" class="form-control select2" multiple="multiple">
                                <option value="">Пожалуйста, выберите вебмастера</option>
                                <option value="all" @if(in_array('all', (array)request('webmaster_id', []))) selected @endif>Все
                                    вебмастера
                                </option>
                                @foreach($webmasters as $webmaster)
                                    <option value="{{ $webmaster->id }}"
                                            @if(in_array((string) $webmaster->id, (array)request('webmaster_id', []))) selected @endif>{{ $webmaster->api_id }}
                                        ({{ $webmaster->source->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12" style="margin-bottom: 25px">
            <input type="submit" value="Отфильтровать" class="btn btn-primary"/>
        </div>
    </form>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($rows) > 0 ? 'datatable' : '' }}">
                <thead>
                <tr>
                    <th>Дата</th>
                    @if($shouldShowSourceName)
                        <th>Партнерская программа</th>
                    @endif
                    @if($shouldShowWebmasterName)
                        <th>Вебмастер</th>
                    @endif
                    <th class="sum">Кликов</th>
                    <th class="sum">Регистраций</th>
                    <th class="sum">Подтвержденных регистраций</th>
                    <th class="sum">Привязок карты</th>
                    <th class="sum">Заработок Витрина</th>
                    <th class="sum">Заработок SMS</th>
                    <th class="sum">Расходы SMS</th>
                    <th class="sum">Заработок платежи</th>
                    <th class="sum">Доход LTV</th>
                    <th class="sum">Заработок баннеры</th>
                    <th class="sum">Отправлено конверсий</th>
                    <th class="sum">Потрачено</th>
                    <th class="sum">Итого</th>
                    <th class="sum">{{ $shouldShowSalary ? 'Ваш заработок' : 'Доход' }}</th>
                </tr>
                </thead>

                <tbody>
                @if (count($rows) > 0)
                    @foreach ($rows as $row)
                        <tr>
                            <td>
                                {{ $row->formatted_date }}
                            </td>
                            @if($shouldShowSourceName)
                                <td>
                                    {{ $sources[$row->source_id]->name ?? '-' }}
                                </td>
                            @endif
                            @if($shouldShowWebmasterName)
                                <td>
                                    {{ isset($row->api_id) && $row->api_id !== 0 ? $row->api_id : '-' }}
                                </td>
                            @endif
                            @foreach(['actions_count',
                                      'users_count',
                                      'active_users_count',
                                      'card_added_users_count',
                                      'dashboard_conversions',
                                      'sms_conversions',
                                      'sms_cost_sum',
                                      'payments_sum',
                                      'ltv_sum',
                                      'banners_sum',
                                      'postback_count',
                                      'postback_cost_sum',
                                      'total'
                                      ] as $column)
                                <td class="sum">
                                    {{ round($row->$column, 2) }}
                                </td>
                            @endforeach
                            <td>
                                {{ $shouldShowSalary ? round($row->salary, 2) : round($row->total - $row->salary, 2) }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="14">Для отображения данных укажите параметры отчета.</td>
                    </tr>
                @endif
                </tbody>
                <tfoot>
                <tr>
                    @php($colSpan = 1 + (int)$shouldShowSourceName + (int)$shouldShowWebmasterName)
                    @for($i = 0; $i < 13; $i++)
                        <td @if($i == 0) colspan="{{ $colSpan }}" @endif>
                            @if($i == 0)
                                <strong>Итого</strong>
                            @endif
                        </td>
                    @endfor
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop