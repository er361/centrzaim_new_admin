@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Витрина займов</h3>


    <div class="panel panel-default">
        <div class="panel-heading">
            Основная информация
        </div>

        <div class="panel-body">
            {!! Form::model($loan, ['method' => 'PUT', 'route' => ['admin.loans.update', $loan->id], 'enctype' => 'multipart/form-data']) !!}
            @include('admin.loans.forms.loan')
            {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
            {!! Form::close() !!}
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Предложения на витринах вебмастеров
        </div>

        <div class="panel-body">
            {!! Form::open(['method' => 'POST', 'route' => ['admin.loan-offers.webmaster']]) !!}
            {!! Form::hidden('loan_id', $loan->id) !!}
            @include('admin.loans.forms.loan_webmaster_offers')
            {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
            {!! Form::close() !!}
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Ссылки
        </div>

        <div class="panel-body">
            {!! Form::open(['method' => 'POST', 'route' => ['admin.loan-links.store']]) !!}
            {!! Form::hidden('loan_id', $loan->id) !!}
            @include('admin.loans.forms.loan_links')
            {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
            {!! Form::close() !!}
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Предложения на витринах
        </div>

        <div class="panel-body">
            {!! Form::open(['method' => 'POST', 'route' => ['admin.loan-offers.store']]) !!}
            {!! Form::hidden('loan_id', $loan->id) !!}
            @include('admin.loans.forms.loan_offers')
            {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
            {!! Form::close() !!}
        </div>
    </div>

@stop
