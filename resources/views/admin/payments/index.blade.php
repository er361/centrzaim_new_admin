@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.payments.title')</h3>

    <form class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Фильтр
                </div>
                <div class="panel-body row">
                    <div class="form-group col-md-3">
                        <label for="date_from">Начальная дата</label>
                        <input type="text" name="date_from" id="date_from" class="form-control datepicker-here"
                               value="{{ request('date_from') }}" autocomplete="off"/>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="date_to">Конечная дата</label>
                        <input type="text" name="date_to" id="date_to" class="form-control datepicker-here"
                               value="{{ request('date_to') }}" autocomplete="off"/>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="type">Тип платежа</label>
                        {!! Form::select('type', [null => 'Не выбран'] + $types, old('type', request('type')), ['class' => 'form-control select2']) !!}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Статус платежа</label>
                        {!! Form::select('status', [null => 'Не выбран'] + $statuses, old('status', request('status')), ['class' => 'form-control select2']) !!}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="amount">Сумма платежа</label>
                        <input type="number" name="amount" id="amount" class="form-control"
                               value="{{ request('amount') }}" step="1"/>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="payment_number"># Платежа</label>
                        <input type="number" name="payment_number" id="payment_number" class="form-control"
                               value="{{ request('payment_number') }}" step="1"/>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="iteration_number"># Круга</label>
                        <input type="number" name="iteration_number" id="iteration_number" class="form-control"
                               value="{{ request('iteration_number') }}" step="1"/>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="payment_plan">Схема пользователя</label>
                        {!! Form::select('payment_plan', [null => 'Не выбран'] + $paymentPlans, old('payment_plan', request('payment_plan')), ['class' => 'form-control select2']) !!}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="error_code">Код ошибки</label>
                        <input type="text" name="error_code" id="error_code" class="form-control"
                               value="{{ request('error_code') }}"/>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="card_number">Номер карты</label>
                        <input type="text" name="card_number" id="card_number" class="form-control"
                               value="{{ request('card_number') }}"/>
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
                    <th>Платежная система</th>
                    <th>Дата платежа</th>
                    <th>Тип</th>
                    <th>Схема</th>
{{--                    <th># Платежа</th>--}}
{{--                    <th># Круга</th>--}}
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Подтип</th>
                    <th>Код ошибки</th>
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
            window.dtDefaultOptions.order = [[0, 'desc']];
            window.dtDefaultOptions.ajax = '{!! route('admin.payments.index', request()->all()) !!}';
            window.dtDefaultOptions.columns = [
                {data: 'id', name: 'id'},

                {data: 'service', name: 'service'},
                {data: 'created_at', name: 'created_at'},
                {data: 'type', name: 'type'},
                {data: 'payment_plan', name: 'payment_plan'},
                // {data: 'payment_number', name: 'payment_number'},
                // {data: 'iteration_number', name: 'iteration_number'},
                {data: 'amount', name: 'amount'},
                {data: 'status', name: 'status'},
                {data: 'subtype', name: 'subtype'},
                {data: 'error_code', name: 'error_code'},

                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();

        });

    </script>
@endsection