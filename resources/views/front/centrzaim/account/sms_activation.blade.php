@extends('layouts.app')

@section('styles')
    <style>
        body {
            background: #FFFFFF;
        }
    </style>
@endsection

@section('content')
    <div class="main-info">
        <div class="container flex flex-col gap-8">
            <h1 class="title main-info__title">Подтверждение аккаунта</h1>

            <div class="container pt-10 pb-16 flex xl:flex-row justify-between flex-col bg-white rounded-3xl shadow-lg">
                <div class="gap-8 flex flex-col py-5">
                    <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                        <div class="text-xl font-medium flex flex-row gap-2 items-center">
                            <img src="/assets/ctr/img/ctr_sms_icon.svg" alt="smartphone" width="42" height="42" class="">
                            <span>через SMS</span>
                        </div>
                    </div>
                    <div class="bg-[#EDF2FE] flex flex-row gap-4 max-w-[856px] p-3 rounded sm:text-base text-sm opacity-90">
                        <img src="/assets/miazaim/imgs/card/i.svg" alt="info" width="22" height="22">
                        <p>На указанный вами телефон {{format_phone(auth()->user()->phone)}} отправлен код, введите его в поле ниже.</p>
                    </div>

                    <div class="flex lg:flex-row flex-col gap-4 justify-between">
                        <div class="basis-8/12 flex flex-col gap-8">
                            <x-sms-confirm :phone="auth()->user()->phone"/>
                        </div>
                    </div>
                </div>
                <img src="/assets/ctr/img/ctr-img-girl.svg" alt="step1" class="max-w-[345px] max-lg:mx-auto">
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        validateAndSubmitForm(
            'smsForm',
            document.getElementById('smsForm').attributes.validateurl.value,
            'complete_confirm_sms'
        );
    </script>
@endsection