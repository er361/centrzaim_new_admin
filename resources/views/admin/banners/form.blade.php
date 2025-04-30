<div class="row">
    <div class="col-xs-6">
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
            {!! Form::label('placement_id', 'Placement Id*', ['class' => 'control-label']) !!}
            {!! Form::text('placement_id', old('placement_id'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
            <p class="help-block"></p>
            @if($errors->has('placement_id'))
                <p class="help-block">
                    {{ $errors->first('placement_id') }}
                </p>
            @endif
        </div>
        <div class="form-group">
            {!! Form::label('position', 'Позиция*', ['class' => 'control-label']) !!}
            {!! Form::select('position', $positions, old('position'), ['class' => 'form-control', 'placeholder' => 'Пожалуйста, выберите', 'required' => true, 'readonly' => isset($banner), 'disabled' => isset($banner)]) !!}
            <p class="help-block"></p>
            @if($errors->has('position'))
                <p class="help-block">
                    {{ $errors->first('position') }}
                </p>
            @endif
        </div>
        <div class="form-group">
            {!! Form::label('code', 'Код баннера', ['class' => 'control-label']) !!}
            {!! Form::textarea('code', old('code'), ['class' => 'form-control', 'required' => true,]) !!}
            <p class="help-block">Не забудьте добавить в код баннера Channel со значением <i>{value}</i></p>
            @if($errors->has('code'))
                <p class="help-block">
                    {{ $errors->first('code') }}
                </p>
            @endif
        </div>
    </div>
    <div class="col-xs-6">
        <div class="form-group">
            {!! Form::label('source_id[]', 'Показываем источники', ['class' => 'control-label']) !!}
            {!! Form::select('source_id[]', $sources, isset($banner) ? $banner->sources->pluck('id') : [], ['class' => 'form-control select2', 'multiple' => 'multiple',]) !!}
            <p class="help-block"></p>
            @if($errors->has('source_id'))
                <p class="help-block">
                    {{ $errors->first('source_id') }}
                </p>
            @endif
        </div>
        <div class="form-group">
            {!! Form::label('webmaster_id[]', 'Показываем вебмастерам', ['class' => 'control-label']) !!}
            {!! Form::select('webmaster_id[]', $webmasters, isset($banner) ? $banner->webmasters->pluck('id') : [], ['class' => 'form-control select2', 'multiple' => 'multiple',]) !!}
            <p class="help-block"></p>
            @if($errors->has('webmaster_id'))
                <p class="help-block">
                    {{ $errors->first('webmaster_id') }}
                </p>
            @endif
        </div>
    </div>
</div>