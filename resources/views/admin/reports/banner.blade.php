@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Отчет по баннерам</h3>

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
                            <label for="position">Расположение</label>
                            <select name="position" class="form-control select2">
                                <option value="">Пожалуйста, выберите расположение</option>
                                <option value="all" @if(request('position') === 'all') selected @endif>Все расположения
                                </option>
                                @foreach($positions as $id => $name)
                                    <option value="{{ $id }}"
                                            @if(request('position') === (string) $id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="banner_id">Баннер</label>
                            <select name="banner_id" class="form-control select2">
                                <option value="">Пожалуйста, выберите баннер</option>
                                <option value="all" @if(request('banner_id') === 'all') selected @endif>Все баннеры
                                </option>
                                @foreach($banners as $banner)
                                    <option value="{{ $banner->id }}"
                                            @if(request('banner_id') === (string) $banner->id) selected @endif>{{ $banner->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="source_id">Партнерская программа</label>
                            <select name="source_id" class="form-control select2">
                                <option value="">Пожалуйста, выберите партнерскую программу</option>
                                <option value="all" @if(request('source_id') === 'all') selected @endif>Все партнерские
                                    программы
                                </option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}"
                                            @if(request('source_id') === (string) $source->id) selected @endif>{{ $source->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="webmaster_id">Вебмастер</label>
                            <select name="webmaster_id" class="form-control select2">
                                <option value="">Пожалуйста, выберите вебмастера</option>
                                <option value="all" @if(request('webmaster_id') === 'all') selected @endif>Все
                                    вебмастера
                                </option>
                                @foreach($webmasters as $webmaster)
                                    <option value="{{ $webmaster->id }}"
                                            @if(request('webmaster_id') === (string) $webmaster->id) selected @endif>{{ $webmaster->api_id }}
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
                    @if(request('position') === 'all')
                        <th>Расположение</th>
                    @endif
                    @if(request('banner_id') === 'all')
                        <th>Баннер</th>
                    @endif
                    @if(request('source_id') === 'all')
                        <th>Партнерская программа</th>
                    @endif
                    @if(request('webmaster_id') === 'all')
                        <th>Вебмастер</th>
                    @endif
                    <th class="sum">Показов</th>
                    <th class="sum">Кликов</th>
                    <th>CTR</th>
                    <th class="sum">Заработок</th>
                    <th>eCPM</th>
                </tr>
                </thead>

                <tbody>
                @if (count($rows) > 0)
                    @foreach ($rows as $row)
                        <tr>
                            <td>
                                {{ $row['date'] }}
                            </td>
                            @if(request('position') === 'all')
                                <td>
                                    {{ $positions[$row['position']] ?? '-' }}
                                </td>
                            @endif
                            @if(request('banner_id') === 'all')
                                <td>
                                    {{ $banners[$row['banner_id']]->name ?? '-' }}
                                </td>
                            @endif
                            @if(request('source_id') === 'all')
                                <td>
                                    {{ $sources[$row['source_id']]->name ?? '-' }}
                                </td>
                            @endif
                            @if(request('webmaster_id') === 'all')
                                <td>
                                    {{ isset($row['api_id']) && $row['api_id'] !== 0 ? $row['api_id'] : '-' }}
                                </td>
                            @endif
                            @foreach(['impressions_sum' => true,
                                      'clicks_sum' => true,
                                      'ctr_avg' => false,
                                      'revenue_sum' => true,
                                      'e_cpm_avg' => false,
                                      ] as $column => $isSum)
                                <td @if($isSum)class="sum"@endif>
                                    {{ round($row[$column], 2) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6">@lang('quickadmin.qa_no_entries_in_table')</td>
                    </tr>
                @endif
                </tbody>
                <tfoot>
                <tr>
                    @php($colSpan = 1 + (int)(request('position') === 'all') + (int)(request('banner_id') === 'all') + (int)(request('source_id') === 'all') + (int)(request('webmaster_id') === 'all'))
                    <td colspan="{{ $colSpan }}">
                        <strong>Итого</strong>
                    </td>
                    @foreach(['impressions' => 'sum',
                                      'clicks' => 'sum',
                                      'ctr' => 'none',
                                      'revenue' => 'sum',
                                      'e_cpm' => 'none',
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