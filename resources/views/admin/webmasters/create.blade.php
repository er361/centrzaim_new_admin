@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Создание вебмастера</h3>

    {!! Form::open(['method' => 'POST', 'route' => ['admin.webmasters.store']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>

        <div class="panel-body">
            @include('admin.webmasters.form')
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_create'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

