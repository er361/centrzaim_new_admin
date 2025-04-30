<div class="panel panel-default">
    <div class="panel-heading">
        Основные настройки
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('name', 'Название*', ['class' => 'control-label']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                <div class="form-group">
                    {!! Form::label('is_enabled', 'Включена*', ['class' => 'control-label']) !!}
                    {!! Form::hidden('is_enabled', 0) !!}
                    {!! Form::checkbox('is_enabled', 1, old('is_enabled')) !!}
                    <p class="help-block"></p>
                    @if($errors->has('is_enabled'))
                        <p class="help-block">
                            {{ $errors->first('is_enabled') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        Кому отправляем
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('type', 'Тип*', ['class' => 'control-label']) !!}
                    {!! Form::select('type', $types, old('type') ?? $model->type->value ?? null, ['class' => 'form-control', 'placeholder' => '', 'required' => '', 'readonly' => isset($model)]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('type'))
                        <p class="help-block">
                            {{ $errors->first('type') }}
                        </p>
                    @endif
                </div>
                <div class="form-group">
                    {!! Form::label('related_sms_id', 'Связанная SMS', ['class' => 'control-label']) !!}
                    {!! Form::select('related_sms_id', $relatedSms, old('related_sms_id'), ['class' => 'form-control', 'placeholder' => 'Пожалуйста, выберите']) !!}
                    <p class="help-block">Для SMS по клику — после клика по какой SMS отправлять сообщение.</p>
                    @if($errors->has('related_sms_id'))
                        <p class="help-block">
                            {{ $errors->first('related_sms_id') }}
                        </p>
                    @endif
                </div>
                <div class="form-group">
                    {!! Form::label('source_id', 'Отправлять пользователям из ПП', ['class' => 'control-label']) !!}
                    {!! Form::select('source_id', $sources, old('source_id'), ['class' => 'form-control', 'placeholder' => 'Пожалуйста, выберите', 'readonly' => isset($model)]) !!}
                    @if($errors->has('source_id'))
                        <p class="help-block">
                            {{ $errors->first('source_id') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('registered_after', 'Отправлять пользователям, зарегистрированным после*', ['class' => 'control-label']) !!}
                    {!! Form::text('registered_after', isset($model, $model->registered_after) ? $model->registered_after->format('d.m.Y H:i') : old('registered_after'), ['class' => 'form-control datepicker-here', 'placeholder' => '', 'required' => '', 'data-timepicker' => 'true', 'autocomplete' => 'off']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('registered_after'))
                        <p class="help-block">
                            {{ $errors->first('registered_after') }}
                        </p>
                    @endif
                </div>
                <div class="form-group">
                    {!! Form::label('delay', 'Задержка перед отправкой в минутах*', ['class' => 'control-label']) !!}
                    {!! Form::number('delay', old('delay'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block">Для обычных SMS и пользователям без карт — считается с момента регистрации,
                        для SMS по касанию — с момента клика по предыдущей SMS.</p>
                    @if($errors->has('delay'))
                        <p class="help-block">
                            {{ $errors->first('delay') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('excluded_webmaster_id[]', 'Вебмастера, которым не отправляем', ['class' => 'control-label']) !!}
                    {!! Form::select('excluded_webmaster_id[]', $webmasters, isset($model) ? $model->excludedWebmasters->pluck('id') : [], ['class' => 'form-control select2', 'multiple' => 'multiple',]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('excluded_webmaster_id'))
                        <p class="help-block">
                            {{ $errors->first('excluded_webmaster_id') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('included_webmaster_id[]', 'Вебмастера, которым отправляем', ['class' => 'control-label']) !!}
                    {!! Form::select('included_webmaster_id[]', $webmasters, isset($model) ? $model->includedWebmasters->pluck('id') : [], ['class' => 'form-control select2', 'multiple' => 'multiple',]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('included_webmaster_id'))
                        <p class="help-block">
                            {{ $errors->first('included_webmaster_id') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        Как отправялем
    </div>

    <div class="panel-body">
        <div class="row">
            <livewire:sms-provider-alpha-names
                    :selectedProvider="$model->sms_provider_id ?? null"
                    :sms="$model ?? null"
            />
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        Содержимое SMS
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('text', 'Текст*', ['class' => 'control-label']) !!}
                    {!! Form::textarea('text', old('text'), ['class' => 'form-control', 'placeholder' => '', 'required' => '', 'rows' => 5]) !!}
                    <p class="help-block">
                        <strong>Поддерживаемые шаблоны:</strong><br/>
                        <i>{link}</i> — вставка ссылки (в том числе автоматических ссылок на регистрацию или
                        витрину)<br/>
                        <i>{name}</i> — вставка имени пользователя
                    </p>
                    <p class="help-block">Например, сообщение: «{name}, лучшие займы тут: {link}», при отправке станет
                        таким:
                        «Иван, лучшие займы тут: https://bit.ly/abCde».</p>
                    @if($errors->has('value'))
                        <p class="help-block">
                            {{ $errors->first('value') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('link', 'Ссылка', ['class' => 'control-label']) !!}
                    {!! Form::text('link', old('link'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block">Если выбран тип SMS "Пользователи без карт" или выбрана витрина, значение
                        этого поля будет проигнорировано. Не забудьте указать шаблон <i>{link}</i> в тексте сообщения!
                    </p>
                    @if($errors->has('link'))
                        <p class="help-block">
                            {{ $errors->first('link') }}
                        </p>
                    @endif
                </div>
                <div class="form-group">
                    {!! Form::label('link_source_id', 'Ссылка ведет на ПП', ['class' => 'control-label']) !!}
                    {!! Form::select('link_source_id', $sources, old('link_source_id'), ['class' => 'form-control', 'placeholder' => 'Пожалуйста, выберите', 'readonly' => isset($model)]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('link_source_id'))
                        <p class="help-block">
                            {{ $errors->first('link_source_id') }}
                        </p>
                    @endif
                </div>
                <div class="form-group">
                    {!! Form::label('showcase_id', 'Вставить ссылку на витрину', ['class' => 'control-label']) !!}
                    {!! Form::select('showcase_id', $showcases, old('showcase_id'), ['class' => 'form-control', 'placeholder' => 'Пожалуйста, выберите']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('showcase_id'))
                        <p class="help-block">
                            {{ $errors->first('showcase_id') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

