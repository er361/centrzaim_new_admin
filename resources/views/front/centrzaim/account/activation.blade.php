@extends('layouts.app')
@section('content')
    <div class="bg-gray-bg">
        <div class="container pt-10 sm:pb-36 pb-16 flex xl:flex-row justify-between flex-col">
            <div class=" gap-8 flex flex-col py-5">
                <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                    <span>
                        Подтверждение аккаунта
                    </span>
                    <div class="text-xl font-medium flex flex-row gap-2 items-center">
                        <img src="/assets/miazaim/imgs/telega.svg" alt="visa" width="42" height="42" class="">
                        <span>через Telegram</span>
                    </div>
                </div>
                <div class="bg-white flex flex-row gap-4 max-w-[856px] p-3 rounded sm:text-base text-sm opacity-90">
                    <img src="/assets/miazaim/imgs/card/i.svg" alt="visa" width="22" height="22">
                    <p>Для подтверждения аккаунта нажмите кнопку получить код, перейдите в Telegram и нажмите
                        <b>старт</b>, введите полученный код</p>
                </div>
                <div class="flex lg:flex-row flex-col gap-4 justify-between">
                    <div class="basis-8/12 flex flex-col gap-8">
                        <div class="get-money-wrapper flex flex-row justify-start w-full sm:w-auto">
                            <div class="flex flex-col justify-center gap-8 justify-start w-full sm:w-auto">
                                @if(request()->has('telegram_link'))
                                    <script>
                                        // Автоматически открываем ссылку в новом окне
                                        window.open('{{ request('telegram_link') }}', '_blank');
                                        // Перенаправляем на страницу ввода кода
                                        setTimeout(function() {
                                            window.location.href = '{{ route('account.activation.method.telegram.code') }}';
                                        }, 500);
                                    </script>
                                    <a href="{{ request('telegram_link') }}" target="_blank" onclick="window.location.href = '{{ route('account.activation.method.telegram.code') }}'">
                                        @include('blocks.components.get-money-btn', ['btnText' => 'Получить код', 'class' => '!bg-[#29B6F6]', 'activeBtn' => true])
                                    </a>
                                @else
                                    <a href="{{ route('account.activation.method.telegram') }}">
                                        @include('blocks.components.get-money-btn', ['btnText' => 'Получить код', 'class' => '!bg-[#29B6F6]', 'activeBtn' => true])
                                    </a>
                                @endif
                                <a href="{{ route('account.activation.method.sms') }}" class="text-[#484E63] font-bold text-sm text-center cursor-pointer">Подтвердить по СМС </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px] shrink-0 max-lg:mx-auto">
        </div>
    </div>
@endsection

