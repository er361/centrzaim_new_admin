@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Конверсии</h3>

    <form class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Фильтр
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
                            <label for="source_id">Партнерская программа</label>
                            <select name="source_id" class="form-control select2">
                                <option value="">Не выбрана</option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}"
                                            @if(in_array((string) $source->id, (array)request('source_id', []))) selected @endif>{{ $source->name }}</option>
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
            <table class="table table-bordered table-striped ajaxTable">
                <thead>
                <tr>

                    <th>ID</th>
                    <th>Партнерская программа</th>
                    <th>ID оффера</th>
                    <th>ID площадки</th>
                    <th>Создана</th>
                    <th>Статус</th>
                    <th>Заработок</th>
                </tr>
                </thead>

            </table>
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
                {data: 'source_id', name: 'source_id'},
                {data: 'api_offer_id', name: 'api_offer_id'},
                {data: 'api_conversion_id', name: 'api_conversion_id'},
                {data: 'api_created_at', name: 'api_created_at'},
                {data: 'api_status', name: 'api_status'},
                {data: 'api_payout', name: 'api_payout'},
            ];
            processAjaxTables();

        });
    </script>
@endsection