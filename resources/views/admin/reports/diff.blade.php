@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Отчет по изменениям</h3>

    <form class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Фильтр по дате
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
                            <label for="column">Показатель для сравенения</label>
                            <select name="column" required class="form-control">
                                <option value="actions_count" @if(request('column') === 'actions_count') selected @endif>Клики</option>
                                <option value="users_count" @if(request('column') === 'users_count') selected @endif>Регистрации</option>
                                <option value="active_users_count" @if(request('column') === 'active_users_count') selected @endif>Подтвержденные пользователи</option>
                                <option value="dashboard_conversions" @if(request('column') === 'dashboard_conversions') selected @endif>Заработок витрина</option>
                                <option value="sms_conversions" @if(request('column') === 'sms_conversions') selected @endif>Заработок SMS</option>
                                <option value="sms_cost_sum" @if(request('column') === 'sms_cost_sum') selected @endif>Расходы SMS</option>
                                <option value="payments_sum" @if(request('column') === 'payments_sum') selected @endif>Заработок платежи</option>
                                <option value="banners_sum" @if(request('column') === 'banners_sum') selected @endif>Заработок баннеры</option>
                                <option value="postback_count" @if(request('column') === 'postback_count') selected @endif>Отправлено конверсий</option>
                                <option value="postback_cost_sum" @if(request('column') === 'postback_cost_sum') selected @endif>Потрачено</option>
                                <option value="total" @if(request('column') === 'total') selected @endif>Итого</option>
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
                    <th>Партнерская программа</th>
                    <th>Вебмастер</th>
                    <th class="sum">Предыдущий период</th>
                    <th class="sum">Текущий период</th>
                    <th class="sum">Изменение</th>
                    <th class="sum">Изменение (%)</th>
                </tr>
                </thead>

                <tbody>
                @if (count($rows) > 0)
                    @foreach ($rows as $row)
                        <tr>
                            <td>
                                {{ $webmasters[$row->webmaster_id]->source->name ?? '-' }}
                            </td>
                            <td>
                                {{ $webmasters[$row->webmaster_id]->api_id ?? '-' }}
                            </td>
                            <td>
                                {{ round($row->previous_period, 2) }}
                            </td>
                            <td>
                                {{ round($row->current_period, 2) }}
                            </td>
                            <td>
                                {{ round($row->diff, 2) }}
                            </td>
                            <td>
                                {{ round($row->diff_percent, 2) }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="15">Для отображения данных укажите параметры отчета.</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
@stop