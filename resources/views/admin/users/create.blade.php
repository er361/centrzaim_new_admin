@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Пользователи</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.users.store']]) !!}

    @include('admin.users.form')

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

