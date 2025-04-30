@if(!isset($webmaster))
    <div class="row">
        <div class="col-xs-12 form-group">
            {!! Form::label('source_id', 'Партнерская программа', ['class' => 'control-label']) !!}
            {!! Form::select('source_id', $sources, old('source_id'), ['class' => 'form-control', 'placeholder' => 'Пожалуйста, выберите']) !!}
            <p class="help-block"></p>
            @if($errors->has('source_id'))
                <p class="help-block">
                    {{ $errors->first('source_id') }}
                </p>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 form-group">
            {!! Form::label('api_id', 'Идентификатор вебмастера', ['class' => 'control-label']) !!}
            {!! Form::text('api_id', old('api_id'), ['class' => 'form-control', 'placeholder' => '']) !!}
            <p class="help-block"></p>
            @if($errors->has('api_id'))
                <p class="help-block">
                    {{ $errors->first('api_id') }}
                </p>
            @endif
        </div>
    </div>
@endif
<div class="row">
    <div class="col-xs-6 form-group">
        <div class="form-group">
            <label for="postback_step">Отправка постбэков</label>
            <select id="postback_step" name="postback_step" class="form-control">
                <option value="" @if(isset($webmaster) && $webmaster->postback_step === null) selected @endif>
                    По умолчанию (как в общих настройках)
                </option>
                @foreach($postbackSteps as $stepId => $name)
                    <option value="{{ $stepId }}"
                            @if(isset($webmaster) && $webmaster->postback_step === $stepId) selected @endif
                    >{{ $name }}</option>
                @endforeach
            </select>
            <p class="help-block">Любое значение, кроме "По умолчанию" будет использоваться вместо глобальных настроек
                сайта.</p>
        </div>
    </div>
    <div class="col-xs-6 form-group">
        {!! Form::hidden('is_payment_required', 0) !!}
        {!! Form::checkbox('is_payment_required', 1, old('is_payment_required'), ['id' => 'is_payment_required']) !!}
        {!! Form::label('is_payment_required', 'Показывать платежную форму', ['class' => 'control-label']) !!}
        <p class="help-block">Если вы отключите платежную форму и выберете отправку постбэка после привязки карты, он
            будет отправляться после заполнения анкеты.</p>
        @if($errors->has('is_payment_required'))
            <p class="help-block">
                {{ $errors->first('is_payment_required') }}
            </p>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-xs-6 form-group">
        {!! Form::label('postback_cost', 'Стоимость конверсии', ['class' => 'control-label']) !!}
        {!! Form::number('postback_cost', old('postback_cost'), ['class' => 'form-control', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('postback_cost'))
            <p class="help-block">
                {{ $errors->first('postback_cost') }}
            </p>
        @endif
    </div>
    <div class="col-xs-6 form-group">
        {!! Form::label('income_percent', 'Процент заработка вебмастеру', ['class' => 'control-label']) !!}
        {!! Form::number('income_percent', old('income_percent'), ['class' => 'form-control', 'placeholder' => 'Например, 80']) !!}
        <p class="help-block">При изменении этого поля будет обновлена вся статистика по вебмастеру</p>
        @if($errors->has('income_percent'))
            <p class="help-block">
                {{ $errors->first('income_percent') }}
            </p>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-xs-12 form-group">
        {!! Form::label('page_tag', 'HTML код для вставки на страницы сайта', ['class' => 'control-label']) !!}
        {!! Form::textarea('page_tag', old('page_tag'), ['class' => 'form-control', 'placeholder' => '<script>...</script>']) !!}
        <p class="help-block"></p>
        @if($errors->has('page_tag'))
            <p class="help-block">
                {{ $errors->first('page_tag') }}
            </p>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-xs-12 form-group">
        {!! Form::label('comment', 'Комментарий', ['class' => 'control-label']) !!}
        {!! Form::textarea('comment', old('comment'), ['class' => 'form-control']) !!}
        <p class="help-block"></p>
        @if($errors->has('comment'))
            <p class="help-block">
                {{ $errors->first('comment') }}
            </p>
        @endif
    </div>
</div>