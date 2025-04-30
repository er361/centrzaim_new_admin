@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Клики</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable">
                <thead>
                <tr>

                    <th>ID</th>
                    <th>IP адрес</th>
                    <th>User Agent</th>
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
                {data: 'ip', name: 'ip'},
                {data: 'user_agent', name: 'user_agent'},

            ];
            processAjaxTables();

        });
    </script>
@endsection