@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Редактирование вебмастера</h3>

    {!! Form::model($webmaster, ['method' => 'PUT', 'route' => ['admin.webmasters.update', $webmaster->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_edit')
        </div>

        <div class="panel-body">
            @include('admin.webmasters.form')
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

