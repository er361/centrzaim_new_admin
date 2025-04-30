@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Отчет по SMS</h3>

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
                            <label for="group_type">Тип группировки даты</label>
                            <select name="group_type" required class="form-control">
                                <option value="1" @if(request('group_type') === '1') selected @endif>По дням</option>
                                <option value="2" @if(request('group_type') === '2') selected @endif>По месяцам</option>
                                <option value="3" @if(request('group_type') === '3') selected @endif>По годам</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="sms_id">SMS</label>
                            <select name="sms_id" class="form-control select2">
                                <option value="">Пожалуйста, выберите SMS</option>
                                <option value="all" @if(request('sms_id') === 'all') selected @endif>Все SMS
                                </option>
                                @foreach($sms as $singleSms)
                                    <option value="{{ $singleSms->id }}"
                                            @if(request('sms_id') === (string) $singleSms->id) selected @endif>{{ $singleSms->name }}</option>
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
                    @if(request('sms_id') === 'all')
                        <th>SMS</th>
                    @endif
                    <th class="sum">Стоимость отправки</th>
                    <th class="sum">Конверсии</th>
                    <th class="sum">Заработок</th>
                </tr>
                </thead>

                <tbody>
                @if (count($rows) > 0)
                    @foreach ($rows as $row)
                        <tr>
                            <td>
                                {{ $row['date'] }}
                            </td>
                            @if(request('sms_id') === 'all')
                                <td>
                                    {{ $sms[$row['sms_id']]->name ?? '-' }}
                                </td>
                            @endif
                            @foreach(['cost' => true,
                                      'conversions' => true,
                                      'total' => true,
                                      ] as $column => $isSum)
                                <td @if($isSum)class="sum"@endif>
                                    {{ round($row[$column], 2) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">@lang('quickadmin.qa_no_entries_in_table')</td>
                    </tr>
                @endif
                </tbody>
                <tfoot>
                <tr>
                    @php($colSpan = 1 + (int)(request('sms_id') === 'all'))
                    <td colspan="{{ $colSpan }}">
                        <strong>Итого</strong>
                    </td>
                    @foreach(['cost' => 'sum',
                                      'conversions' => 'sum',
                                      'total' => 'sum',
                                      ] as $column => $method)
                        @if ($method === 'sum')
                            <td class="sum">
                                {{ round(array_sum(\Illuminate\Support\Arr::pluck($rows, $column)), 2) }}
                            </td>
                        @else
                            <td>-</td>
                        @endif

                    @endforeach

                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop