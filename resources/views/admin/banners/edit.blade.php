@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Баннеры</h3>
    
    {!! Form::model($banner, ['method' => 'PUT', 'route' => ['admin.banners.update', $banner->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_edit')
        </div>

        <div class="panel-body">
            @include('admin.banners.form')
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop
