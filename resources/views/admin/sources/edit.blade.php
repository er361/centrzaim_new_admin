@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Редактирование партнерской программы</h3>

    {!! Form::model($source, ['method' => 'PUT', 'route' => ['admin.sources.update', $source]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('postback_cost', 'Стоимость конверсии', ['class' => 'control-label']) !!}
                    {!! Form::number('postback_cost', old('postback_cost'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('postback_cost'))
                        <p class="help-block">
                            {{ $errors->first('postback_cost') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

