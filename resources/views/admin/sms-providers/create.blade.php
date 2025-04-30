@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Аккаунты SMS</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.sms-providers.store']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>
        
        <div class="panel-body">
            @include('admin.sms-providers.form')
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

