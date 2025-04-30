@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Постбэки</h3>

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
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="source">Партнерская программа</label>
                            <select name="source" class="form-control select2">
                                <option value="">Пожалуйста, выберите партнерскую программу</option>
                                <option value="all" @if(request('source') === 'all') selected @endif>Все партнерские
                                    программы
                                </option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}"
                                            @if(request('source') === (string) $source->id) selected @endif>{{ $source->name }}</option>
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
    <form method="post" action="{!! route('admin.postbacks.export', request()->all()) !!}"  style="margin-bottom: 25px">
        {{ csrf_field() }}
        <input type="submit" value="Экспорт" class="btn btn-success"/>
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
                    <th>Идентификатор пользователя</th>
                    <th>Партнерская программа</th>
                    <th>Идентификатор вебмастера в ПП</th>
                    <th>ID клика</th>
                    <th>Потрачено</th>
                    <th>Создано</th>
                </tr>
                </thead>

            </table>
        </div>
    </div>
@stop

@section('javascript')
    <script>

        $(document).ready(function () {
            window.dtDefaultOptions.order = [[0, 'desc']];
            window.dtDefaultOptions.ajax = '{!! route(request()->route()->getName(), request()->all()) !!}';
            window.dtDefaultOptions.columns = [
                {data: 'id', name: 'id'},
                {data: 'user_id', name: 'user_id'},
                {data: 'source_name', name: 'source_name'},
                {data: 'webmaster_api_id', name: 'webmaster_api_id'},
                {data: 'user_transaction_id', name: 'user_transaction_id'},
                {data: 'cost', name: 'cost'},
                {data: 'created_at', name: 'created_at'},
            ];
            processAjaxTables();

        });
    </script>
@endsection