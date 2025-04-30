<div class="row">
    <div class="col-xs-6 form-group">
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
    <div class="col-xs-6 form-group">
        {!! Form::label('description', 'Описание', ['class' => 'control-label']) !!}
        {!! Form::textarea('description', old('description'), ['class' => 'form-control', 'placeholder' => '', 'required']) !!}
        <p class="help-block"></p>
        @if($errors->has('description'))
            <p class="help-block">
                {{ $errors->first('description') }}
            </p>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-xs-12 form-group">
        @isset($loan)
            {!! Form::label('image', 'Новое изображение', ['class' => 'control-label']) !!}
            {!! Form::file('image') !!}
        @else
            {!! Form::label('image', 'Изображение*', ['class' => 'control-label']) !!}
            {!! Form::file('image', ['required']) !!}
        @endisset

        <p class="help-block"></p>
        @if($errors->has('image'))
            <p class="help-block">
                {{ $errors->first('image') }}
            </p>
        @endif
    </div>
{{--    {{/** @var $loan \App\Models\Loan */}}--}}
    @isset($loan)
        <div class="col-xs-12 form-group">
            <img src="{{$loan->image_path }}" width="300">
        </div>
    @endisset
</div>
@if($shouldShowExtendedFields)
    <div class="row">
        <div class="col-xs-6 form-group">
            {!! Form::label('rating', 'Рейтинг', ['class' => 'control-label']) !!}
            {!! Form::number('rating', old('rating'), ['class' => 'form-control', 'placeholder' => '4,9', 'step' => 0.01]) !!}
            <p class="help-block"></p>
            @if($errors->has('rating'))
                <p class="help-block">
                    {{ $errors->first('rating') }}
                </p>
            @endif
        </div>
        <div class="col-xs-6 form-group">
            {!! Form::label('amount', 'Сумма', ['class' => 'control-label']) !!}
            {!! Form::text('amount', old('amount'), ['class' => 'form-control', 'placeholder' => '2 000 - 15 000']) !!}
            <p class="help-block"></p>
            @if($errors->has('amount'))
                <p class="help-block">
                    {{ $errors->first('amount') }}
                </p>
            @endif
        </div>
        <div class="col-xs-6 form-group">
            {!! Form::label('issuing_time', 'Время выдачи', ['class' => 'control-label']) !!}
            {!! Form::text('issuing_time', old('issuing_time'), ['class' => 'form-control', 'placeholder' => 'До 4 минут']) !!}
            <p class="help-block"></p>
            @if($errors->has('issuing_time'))
                <p class="help-block">
                    {{ $errors->first('issuing_time') }}
                </p>
            @endif
        </div>
        <div class="col-xs-6 form-group">
            {!! Form::label('issuing_period', 'Срок (дней)', ['class' => 'control-label']) !!}
            {!! Form::text('issuing_period', old('issuing_period'), ['class' => 'form-control', 'placeholder' => '7 - 21']) !!}
            <p class="help-block"></p>
            @if($errors->has('issuing_period'))
                <p class="help-block">
                    {{ $errors->first('issuing_period') }}
                </p>
            @endif
        </div>
        <div class="col-xs-6 form-group">
            {!! Form::label('issuing_bid', 'Ставка', ['class' => 'control-label']) !!}
            {!! Form::text('issuing_bid', old('issuing_bid'), ['class' => 'form-control', 'placeholder' => '0%']) !!}
            <p class="help-block"></p>
            @if($errors->has('issuing_bid'))
                <p class="help-block">
                    {{ $errors->first('issuing_bid') }}
                </p>
            @endif
        </div>
    </div>
@endif