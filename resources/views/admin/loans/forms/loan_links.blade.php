<div class="row">
    @foreach($sources as $source)
        @php($fieldKey = "loan_links[$source->id]")
        <div class="col-xs-6 form-group">
            {!! Form::label($fieldKey, $source->name, ['class' => 'control-label']) !!}
            {!! Form::text($fieldKey, old($fieldKey, $loanLinks->get($source->id)?->link), ['class' => 'form-control', 'placeholder' => '']) !!}
            <p class="help-block"></p>
            @if($errors->has($fieldKey))
                <p class="help-block">
                    {{ $errors->first($fieldKey) }}
                </p>
            @endif
        </div>
    @endforeach
</div>
