@extends('layouts.app')
@section('content')
    <x-sms-confirm :phone="auth()->user()->phone"/>
@endsection

