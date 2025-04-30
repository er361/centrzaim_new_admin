@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Вебмастера</h3>

    <p>
        <a href="{{ route('admin.webmasters.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
    </p>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable">
                <thead>
                <tr>
                    <th>Наш ID</th>
                    <th>ПП</th>
                    <th>API ID</th>
                    <th>Коммент</th>
                    <th>Стоимость postback</th>
                    <th>Процент заработка</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>

            </table>
        </div>
    </div>
@stop

@section('javascript')
    <script>

        $(document).ready(function () {

            window.dtDefaultOptions.ajax = '{!! route(request()->route()->getName(), request()->all()) !!}';
            window.dtDefaultOptions.columns = [
                {data: 'id', name: 'id'},
                {data: 'source_id', name: 'source_id'},
                {data: 'api_id', name: 'api_id'},
                {data: 'comment', name: 'comment'},
                {data: 'postback_cost', name: 'postback_cost'},
                {data: 'income_percent', name: 'income_percent'},
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();

        });
    </script>
@endsection