@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.users.title')</h3>
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>ID</th>
                            <td id="user-id">{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.users.fields.email')</th>
                            <td id="user-email">{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.users.fields.phone')</th>
                            <td id="user-mphone">{{ $user->mphone }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.users.fields.ip')</th>
                            <td id="user-ip">{{ $user->ip_address }}</td>
                        </tr>
                        <tr>
                            <th>Имя</th>
                            <td id="user-first-name">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.users.fields.last-name')</th>
                            <td id="user-last-name">{{ $user->last_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.users.fields.middlename')</th>
                            <td id="user-middlename">{{ $user->middlename }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.users.fields.logged-at')</th>
                            <td>{{ $user->logged_at }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Вебмастер</th>
                            <td>
                                @if($user->webmaster !== null)
                                    <a href="{{ route('admin.webmasters.show', ['webmaster' => $user->webmaster]) }}">
                                        {{ $user->webmaster->api_id }}
                                        ({{ $user->webmaster->source?->name ?? 'Без источника' }})
                                    </a>
                                @else
                                    Нет вебмастера
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Дата регистрации</th>
                            <td id="user-created-at">{{ $user->created_at->format('d.m.Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Отправлен постбэк?</th>
                            <td>
                                {{ $wasPostbackSent ? 'Да' : 'Нет' }}
                            </td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.users.fields.is-disabled')</th>
                            <td>{{ $user->is_disabled ? 'Да' : 'Нет' }}</td>
                        </tr>
                        <tr>
                            <th>Роль</th>
                            <td>{{ $user->role->title ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Схема списаний</th>
                            <td>{{ $paymentPlans[$user->payment_plan]['name'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Показываем форму платежа?</th>
                            <td>{{ $user->is_payment_required ? 'Да' : 'Нет' }}</td>
                        </tr>
                        <tr>
                            <th>Успешных рекуррентных платежей</th>
                            <td>{{ $user->recurrent_payment_success_count }}</td>
                        </tr>
                        <tr>
                            <th>Ошибок списания рекурретных платежей подряд</th>
                            <td>{{ $user->recurrent_payment_consequent_error_count }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-12">
                    <div style="margin-bottom: 20px;">
                        @can('user_edit')
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Редактировать</a>
                        @endcan
                        @can('user_unsubscribe')
                            @if (!$user->is_disabled)
                                <form action="{{ route('admin.users.unsubscribe', compact('user')) }}" method="post"
                                      style="display: inline">
                                    @csrf
                                    @method('put')
                                    <button type="submit" class="btn btn-danger">Отписать от платежей и SMS</button>
                                </form>
                            @else
                                <button class="btn btn-success" disabled>Пользователь уже отписан от платежей и SMS
                                </button>
                            @endif
                        @endcan
                        <a href="{{ route('admin.users.document', $user) }}"
                           class="btn btn-primary">Скачать договор</a>
                        <a href="#" id="copy-appeal-text"
                           class="btn btn-primary">Скопировать текст обжалования</a>
                    </div>
                </div>
            </div>
            <ul class="nav nav-tabs" role="tablist">

                <li role="presentation" class="active"><a href="#payments" aria-controls="payments" role="tab"
                                                          data-toggle="tab">Платежи</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">

                <div role="tabpanel" class="tab-pane active" id="payments">
                    <table class="table table-bordered table-striped {{ count($payments) > 0 ? 'datatable' : '' }}">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Создан</th>
                            <th>Платежная система</th>
                            <th># Платежа</th>
                            <th># Круга</th>
                            <th>Сумма</th>
                            <th>Тип</th>
                            <th>Статус</th>
                            <th>Код ошибки</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>

                        <tbody>
                        @if (count($payments) > 0)
                            @foreach ($payments as $payment)
                                <tr data-entry-id="{{ $payment->id }}">
                                    <td>{{ $payment->id }}</td>
                                    <td>{{ $payment->created_at->format('d.m.Y H:i:s') }}</td>
                                    <td>{{ $payment->service_description }}</td>
                                    <td>{{ $payment->payment_number + 1 }}</td>
                                    <td>{{ $payment->iteration_number + 1 }}</td>
                                    <td>{{ $payment->amount }}</td>
                                    <td>{{ $payment->type_description }}</td>
                                    <td>{{ $payment->status_description }}</td>
                                    <td>{{ $payment->error_code }}</td>
                                    <td>
                                        @can('payment_view')
                                            <a href="{{ route('admin.payments.show',[$payment->id]) }}"
                                               class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                        @endcan
                                    </td>

                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9">@lang('quickadmin.qa_no_entries_in_table')</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.users.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#copy-appeal-text').on('click', async function (e) {
                e.preventDefault();

                const textElements = [
                    'Идентификатор пользователя: ' + $('#user-id').text(),
                    'Email: ' + $('#user-email').text(),
                    'Телефон: ' + $('#user-mphone').text(),
                    'IP адрес: ' + $('#user-ip').text(),
                    'ФИО: ' + $('#user-last-name').text() + ' ' + $('#user-first-name').text() + ' ' + $('#user-middlename').text(),
                    'Дата регистрации: ' + $('#user-created-at').text(),
                ];
                let text = textElements.join('\n');
                await copyToClipboard(text);
            });
        });

        async function copyToClipboard(textToCopy) {
            // Navigator clipboard api needs a secure context (https)
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(textToCopy);
            } else {
                // Use the 'out of viewport hidden text area' trick
                const textArea = document.createElement("textarea");
                textArea.value = textToCopy;

                // Move textarea out of the viewport so it's not visible
                textArea.style.position = "absolute";
                textArea.style.left = "-999999px";

                document.body.prepend(textArea);
                textArea.select();

                try {
                    document.execCommand('copy');
                } catch (error) {
                    console.error(error);
                } finally {
                    textArea.remove();
                }
            }
        }
    </script>
@endsection