<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="col-xs-12 form-group">
                {!! Form::label('name', 'Название*', ['class' => 'control-label']) !!}
                {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'readonly' => '', 'disabled' => '', 'required' => '']) !!}
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
                {!! Form::label('registered_after', 'Отправлять пользователей, зарегистрированных после', ['class' => 'control-label']) !!}
                {!! Form::text('registered_after', old('registered_after'), ['class' => 'form-control datepicker-here', 'data-clear-button' => 'true', 'placeholder' => '', 'autocomplete' => 'off']) !!}
                <p class="help-block">Оставьте пустым, чтобы не отправлять анкеты в этот сервис.</p>
                @if($errors->has('registered_after'))
                    <p class="help-block">
                        {{ $errors->first('registered_after') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                {!! Form::label('delay_minutes', 'Задержка перед отправкой, в минутах', ['class' => 'control-label']) !!}
                {!! Form::number('delay_minutes', old('delay_minutes'), ['class' => 'form-control', 'placeholder' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('delay_minutes'))
                    <p class="help-block">
                        {{ $errors->first('delay_minutes') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>