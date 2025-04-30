@extends('layouts.app')
@section('content')
<x-sms-confirm
    :title="'Отмена подписки'"
    :subTitle="'Подтвердите отмену подписки'"
    :sendCodeUrl="route('front.unsubscribe.confirm.code')"
    :change-number-url="route('front.unsubscribe.index')"
    :resend-code-url="route('sms.resend',['phone' => $phone])"
    :phone="$phone"
/>
@endsection