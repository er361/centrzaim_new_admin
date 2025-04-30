@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Настройки</h3>

    <form action="{{ route('admin.settings.store') }}" method="post">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Анкета
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <input type="hidden" name="is_phone_verification_enabled" value="0"/>
                            <div class="checkbox">
                                <label for="is_phone_verification_enabled">
                                    <input type="checkbox" name="is_phone_verification_enabled"
                                           id="is_phone_verification_enabled" value="1"
                                           @if(\Illuminate\Support\Arr::get($settings, 'is_phone_verification_enabled', '0') === '1') checked @endif>
                                    Включить подтверждение по номеру телефона
                                </label>
                            </div>
                        </div>

                        @foreach($steps as $step => $fields)
                            <div class="form-group">
                                <input type="hidden" name="is_account_fill_step_{{ $step }}_enabled" value="0"/>
                                <div class="checkbox">
                                    <label for="is_account_fill_step_{{ $step }}_enabled">
                                        <input type="checkbox" name="is_account_fill_step_{{ $step }}_enabled"
                                               id="is_account_fill_step_{{ $step }}_enabled" value="1"
                                               @if(\Illuminate\Support\Arr::get($settings, 'is_account_fill_step_'.$step.'_enabled', '0') === '1') checked @endif>
                                        Показывать шаг #{{ $step }} после регистрации ({{ $fields }})
                                    </label>
                                </div>
                            </div>
                        @endforeach

                        <div class="form-group">
                            <input type="hidden" name="is_payments_enabled" value="0"/>
                            <div class="checkbox">
                                <label for="is_payments_enabled">
                                    <input type="checkbox" name="is_payments_enabled" id="is_payments_enabled" value="1"
                                           @if(\Illuminate\Support\Arr::get($settings, 'is_payments_enabled', '0') === '1') checked @endif>
                                    Включить платежную систему
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="should_redirect_to_register_page_from_sources" value="0"/>
                            <div class="checkbox">
                                <label for="should_redirect_to_register_page_from_sources">
                                    <input type="checkbox" name="should_redirect_to_register_page_from_sources"
                                           id="should_redirect_to_register_page_from_sources" value="1"
                                           @if(\Illuminate\Support\Arr::get($settings, 'should_redirect_to_register_page_from_sources', '0') === '1') checked @endif>
                                    Открывать пользователям из ПП страницу оформления заявки
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="show_banks_notification_modal" value="0"/>
                            <div class="checkbox">
                                <label for="show_banks_notification_modal">
                                    <input type="checkbox" name="show_banks_notification_modal"
                                           id="show_banks_notification_modal" value="1"
                                           @if(\Illuminate\Support\Arr::get($settings, 'show_banks_notification_modal', '0') === '1') checked @endif>
                                    Показывать модальное окно с рекомендуемыми банками
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        API ключи
                    </div>

                    <div class="panel-body">
                        @foreach(\App\Services\SettingsService\Enums\SettingNameEnum::cases() as $setting)
                            <div class="form-group">
                                <label for="{{ $setting->value }}">{{ $setting->getLabel() }}</label>
                                <input type="text" class="form-control" id="{{ $setting->value }}" name="{{ $setting->value }}" value="{{ $settings[$setting->value] ?? '' }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Постбэки
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="postback_step">Отправка постбэков</label>
                            <select id="postback_step" name="postback_step" class="form-control">
                                @foreach($postbackSteps as $stepId => $name)
                                    <option value="{{ $stepId }}"
                                            @if(\Illuminate\Support\Arr::get($settings, 'postback_step') === $stepId) selected @endif
                                    >{{ $name }}</option>
                                @endforeach
                            </select>
                            <p class="help-block">Если выбрано "После подтверждения телефона" и отключено подтверждение по телефону,
                                постбэк будет отправляться после заполнения анкеты. Если выбрано "После привязки карты"
                                и отключена платежная система (на уровне сайта или вебмастера), постбэк будет
                                отправляться после заполнения анкеты.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Настройки фронтэнд части сайта
                    </div>

                    <div class="panel-body">
                        @foreach(\App\Facades\SettingsServiceFacade::getFrontendSettings() as $setting)
                            <x-frontend-setting
                                    :type="$setting->getType()"
                                    :name="$setting->value"
                                    :label="$setting->getLabel()"
                                    :value="$settings[$setting->value] ?? ''"/>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <input type="submit" value="Сохранить" class="btn btn-primary"/>
                </div>
            </div>
        </div>
    </form>
@stop