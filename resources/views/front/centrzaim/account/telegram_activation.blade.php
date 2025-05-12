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
                        <x-form-errors :errors="$errors"/>
                        <form
                                validateUrl="{{route('account.activation.validateCode')}}"
                                method="POST"
                                action="{{route('account.activation.store')}}" id="smsForm"
                                class="flex flex-col gap-4">
                            @csrf
                            <div class="max-w-[416px] flex xl:flex-row flex-col gap-4">
                                <input maxlength="6" required name="code"
                                       value="{{old('activation_code')}}"
                                       placeholder="Пароль из смс" class="p-3 rounded w-full max-sm:text-center">
                                <input type="hidden" name="phone" value="{{$phone}}">
                            </div>
                            <div class="get-money-wrapper flex flex-row justify-start w-full sm:w-auto">
                                <div class="flex flex-col justify-center gap-8 justify-start w-full sm:w-auto">
                                    @if(isset($telegram_link))
                                        <a href="{{ $telegram_link }}" target="_blank">
                                            @include('blocks.components.get-money-btn', ['btnText' => 'Подтвердить', 'class' => '!bg-[#29B6F6]', 'activeBtn' => true])
                                        </a>
                                        <script>
                                            // Автоматически открываем ссылку в новом окне
                                            window.open('{{ $telegram_link }}', '_blank');
                                        </script>
                                    @else
                                        <a href="{{ route('account.activation.method.telegram') }}">
                                            @include('blocks.components.get-money-btn', ['btnText' => 'Подтвердить', 'class' => '!bg-[#29B6F6]', 'activeBtn' => true])
                                        </a>
                                    @endif
                                    <a href="{{ route('account.activation.method.sms') }}" class="text-[#484E63] font-bold text-sm text-center cursor-pointer">Подтвердить по СМС </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px] shrink-0 max-lg:mx-auto">
        </div>
    </div>
@endsection

