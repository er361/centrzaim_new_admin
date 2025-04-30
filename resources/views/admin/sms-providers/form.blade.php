<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="col-xs-12 form-group">
                {!! Form::label('name', 'Название*', ['class' => 'control-label']) !!}
                {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('name'))
                    <p class="help-block">
                        {{ $errors->first('name') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                {!! Form::label('api_login', 'API логин', ['class' => 'control-label']) !!}
                {!! Form::text('api_login', old('api_login'), ['class' => 'form-control', 'placeholder' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('api_login'))
                    <p class="help-block">
                        {{ $errors->first('api_login') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                {!! Form::label('api_password', 'API пароль', ['class' => 'control-label']) !!}
                {!! Form::text('api_password', old('api_password'), ['class' => 'form-control', 'placeholder' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('api_password'))
                    <p class="help-block">
                        {{ $errors->first('api_password') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                {!! Form::label('sender', 'Отправитель', ['class' => 'control-label']) !!}
                {!! Form::text('sender', old('sender'), ['class' => 'form-control', 'placeholder' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('sender'))
                    <p class="help-block">
                        {{ $errors->first('sender') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                {!! Form::label('service_id', 'Сервис*', ['class' => 'control-label']) !!}
                {!! Form::select('service_id', $services, old('service_id'), ['class' => 'form-control', 'placeholder' => 'Пожалуйста, выберите', 'required' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('service_id'))
                    <p class="help-block">
                        {{ $errors->first('service_id') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                {!! Form::label('is_for_activation', 'Использовать для подтверждения аккаунтов? *', ['class' => 'control-label']) !!}
                {!! Form::select('is_for_activation', [0 => 'Нет', 1 => 'Да'], old('is_for_activation'), ['class' => 'form-control', 'placeholder' => 'Пожалуйста, выберите', 'required' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('is_for_activation'))
                    <p class="help-block">
                        {{ $errors->first('is_for_activation') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                {!! Form::label('sms_cost', 'Стоимость отправки одного SMS*', ['class' => 'control-label']) !!}
                {!! Form::number('sms_cost', old('sms_cost'), ['class' => 'form-control', 'placeholder' => '', 'step' => 0.01]) !!}
                <p class="help-block"></p>
                @if($errors->has('sms_cost'))
                    <p class="help-block">
                        {{ $errors->first('sms_cost') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>