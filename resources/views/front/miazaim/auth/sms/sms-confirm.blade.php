@extends('layouts.app')
@section('content')
    <x-sms-confirm :phone="$phone"
                   :change-number-url="route('sms.login')"
                   :title="'Вход в личный кабинет'"
                   :send-code-url="route('sms.confirm')"
                   :resend-code-url="route('sms.resend',['phone' => $phone])"
    />
@endsection

