@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Баннеры</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.banners.store']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>
        
        <div class="panel-body">
            @include('admin.banners.form')
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop