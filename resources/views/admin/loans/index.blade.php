@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Офферы для витрин</h3>

    <div class="flex flex-row gap-4 py-4">
        @can('loan_create')
            <p>
                <a href="{{ route('admin.loans.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
            </p>
        @endcan
        <button id="update-offers" class="btn btn-primary">Обновить офферы</button>
    </div>

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
                    <th>Api ID</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('javascript')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            // Настройка DataTables
            window.dtDefaultOptions.ajax = '{!! route(\Request::route()->getName()) !!}?{!! request()->getQueryString() !!}';
            window.dtDefaultOptions.columns = [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'api_id', name: 'apiId'},
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();

            // Обработка кнопки "Обновить офферы"
            $('#update-offers').click(function () {
                let $button = $(this); // Сохраняем ссылку на кнопку
                $button.prop('disabled', true).text('Обновляется...'); // Делаем кнопку неактивной и меняем текст

                $.ajax({
                    url: '{{ route('admin.update-offers') }}',
                    method: 'GET'
                })
                    .done(function () {
                        toastr["success"]("Статус оффера обновлен.");
                    })
                    .fail(function () {
                        toastr["error"]("Ошибка при обновлении статуса оффера");
                    })
                    .always(function () {
                        $button.prop('disabled', false).text('Обновить офферы'); // Возвращаем кнопку в исходное состояние
                    });
            });
        });
    </script>
@endsection
