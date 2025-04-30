@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Аккаунты SMS</h3>
    
    {!! Form::model($model, ['method' => 'PUT', 'route' => ['admin.sms-providers.update', $model->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_edit')
        </div>

        <div class="panel-body">
            @include('admin.sms-providers.form')
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

