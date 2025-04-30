@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">SMS</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            Основная информация
        </div>
        @php
        /** @var $sms \App\Models\Sms */
        @endphp
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>ID</th>
                            <td>{{ $sms->id }}</td>
                        </tr>
                        <tr>
                            <th>Название</th>
                            <td>{{ $sms->name }}</td>
                        </tr>
                        <tr>
                            <th>Тип</th>
                            <td>{{ $sms->type->getLabel() }}</td>
                        </tr>
                        <tr>
                            <th>Включена</th>
                            <td>{{ $sms->is_enabled ? 'Да' : 'Нет' }}</td>
                        </tr>
                        <tr>
                            <th>Связанная SMS</th>
                            <td>{{ $sms->relatedSms->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Задержка перед отправкой в минутах</th>
                            <td>{{ $sms->delay }}</td>
                        </tr>
                        <tr>
                            <th>Отправлять только зарегистрированным после</th>
                            <td>{{ $sms->registered_after->format('d.m.Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Текст сообщения</th>
                            <td>{{ $sms->text }}</td>
                        </tr>
                        <tr>
                            <th>Ссылка в сообщении</th>
                            <td>{{ $sms->link ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>От кого</th>
                            <td>{{ $sms->from ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Провайдер</th>
                            <td>{{ $sms->smsProvider->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Статистика
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Отправок за последний час</th>
                            <td>{{ $sentLastHourCount }}</td>
                        </tr>
                        <tr>
                            <th>Стоимость за последний час</th>
                            <td>{{ $sentLastHourTotal }} ₽</td>
                        </tr>
                        <tr>
                            <th>Кликов за последний час</th>
                            <td>{{ $clicksLastHourCount }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Отправок за последний день</th>
                            <td>{{ $sentLastDayCount }}</td>
                        </tr>
                        <tr>
                            <th>Стоимость за последний день</th>
                            <td>{{ $sentLastDayTotal }} ₽</td>
                        </tr>
                        <tr>
                            <th>Кликов за последний день</th>
                            <td>{{ $clicksLastDayCount }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Последние отправки
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Пользователь</th>
                    <th>Стоимость</th>
                </tr>
                </thead>

                <tbody>
                @if (count($lastSends) > 0)
                    @foreach ($lastSends as $lastSend)
                        <tr>
                            <td>{{ $lastSend->created_at->format('d.m.Y H:i:s') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $lastSend->user_id) }}">
                                    {{ $lastSend->user->email }}
                                </a>
                            </td>
                            <td>
                                {{ $lastSend->cost }} ₽
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3">@lang('quickadmin.qa_no_entries_in_table')</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Последние клики
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Пользователь</th>
                </tr>
                </thead>

                <tbody>
                @if (count($lastClicks) > 0)
                    @foreach ($lastClicks as $lastClick)
                        <tr>
                            <td>{{ $lastClick->created_at->format('d.m.Y H:i:s') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $lastClick->user_id) }}">
                                    {{ $lastClick->user->email }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2">@lang('quickadmin.qa_no_entries_in_table')</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

