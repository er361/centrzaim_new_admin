@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">SMS</h3>
    
    {!! Form::model($model, ['method' => 'PUT', 'route' => ['admin.sms.update', $model->id]]) !!}

    @include('admin.sms.form')

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

