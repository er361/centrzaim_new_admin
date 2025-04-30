@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.payments.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Пользователь</th>
                            <td>
                                <a href="{{ route('admin.users.show', $payment->user) }}">
                                    {{ $payment->user->email ?? '' }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Платежная система</th>
                            <td>{{ $payment->service_description }}</td>
                        </tr>
                        <tr>
                            <th>Сумма</th>
                            <td >{{ $payment->amount }}</td>
                        </tr>
                        <tr>
                            <th>Статус</th>
                            <td>{{ $payment->status_description }}</td>
                        </tr>
                        <tr>
                            <th>Код ошибки</th>
                            <td>{{ $payment->error_code ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Тип</th>
                            <td>{{ $payment->type_description }}</td>
                        </tr>
                        <tr>
                            <th>Создан</th>
                            <td>{{ $payment->created_at->format('d.m.Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th># Платежа</th>
                            <td>{{ $payment->payment_number + 1 }}</td>
                        </tr>
                        <tr>
                            <th># Круга</th>
                            <td>{{ $payment->iteration_number + 1 }}</td>
                        </tr>
                        <tr>
                            <th>Номер карты</th>
                            <td>{{ $payment->card_number ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.payments.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop
