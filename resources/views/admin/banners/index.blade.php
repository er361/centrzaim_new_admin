@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Баннеры</h3>

    @can('banner_create')
        <p>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        </p>
    @endcan

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable">
                <thead>
                <tr>

                    <th>ID</th>
                    <th>Название</th>
                    <th>Расположение</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>

            </table>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function () {
            window.dtDefaultOptions.ajax = '{!! route(\Request::route()->getName()) !!}?{!! request()->getQueryString() !!}';
            window.dtDefaultOptions.columns = [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'position', name: 'position'},
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>
@endsection