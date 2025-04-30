@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Витрина займов</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.loans.store'], 'enctype' => 'multipart/form-data']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>

        <div class="panel-body">
            @include('admin.loans.forms.loan')
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop