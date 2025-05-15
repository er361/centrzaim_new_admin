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


            <div class="container pt-10 sm:pb-36 pb-16 flex xl:flex-row justify-between flex-col bg-white rounded-3xl shadow-lg">

                <div class=" gap-8 flex flex-col py-5">
                    <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                        <div class="text-xl font-medium flex flex-row gap-2 items-center">
                            <img src="/assets/miazaim/imgs/telega.svg" alt="visa" width="42" height="42" class="">
                            <span>через Telegram</span>
                        </div>
                    </div>
                    <div class="bg-[#EDF2FE] flex flex-row gap-4 max-w-[856px] p-3 rounded sm:text-base text-sm opacity-90">
                        <img src="/assets/miazaim/imgs/card/i.svg" alt="visa" width="22" height="22">
                        <p>Для получения кода нажмите кнопку, перейдите в <b>Telegram</b> и активируйте бота.</p>
                    </div>
                    <div class="flex lg:flex-row flex-col gap-4 justify-between">
                        <div class="basis-8/12 flex flex-col gap-8">
                            <div class="get-money-wrapper flex flex-row justify-start w-full sm:w-auto">
                                <div class="flex flex-col justify-center gap-8 justify-start w-full sm:w-auto">

                                    <a href="{{ route('account.activation.method.telegram') }}"
                                       class="get-code-btn"
                                       data-redirect="{{ route('account.activation.method.telegram.code') }}">
                                        @include('blocks.components.get-money-btn', ['btnText' => 'Получить код', 'class' => '!bg-[#2F76E2]', 'activeBtn' => true])
                                    </a>
                                    <a href="{{ route('account.activation.method.sms') }}"
                                       class="text-[#484E63] font-bold text-sm text-center cursor-pointer">Подтвердить
                                        по СМС </a>
                                </div>
                            </div>
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
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.querySelector('.get-code-btn');
            if (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    window.open(btn.href, '_blank');
                    setTimeout(function () {
                        window.location.href = btn.dataset.redirect;
                    }, 500);
                });
            }
        });
    </script>
@endsection

