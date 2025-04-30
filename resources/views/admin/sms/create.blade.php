@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">SMS</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.sms.store']]) !!}

    @include('admin.sms.form')

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection

