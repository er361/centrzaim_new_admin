@extends('layouts.app')
@section('content')
    <div class="bg-gray-bg">
        @include('blocks.components.progress', ['width' => 'w-full'])
        <div class="container lg:px-8 gap-8 flex  flex-col py-10">
            <div class="sm:text-3xl text-2xl  font-semibold">
                <span>Вход в личный кабинет</span>
            </div>
            <div class="flex flex-row justify-between">
                <div class="lg:basis-4/12 w-full">
                    <div class="flex flex-col gap-4">
                        @csrf
                        <div><input required name="region" placeholder="Ф.И.О." class="p-3 w-full  rounded"></div>
                        <div><input required name="phone" placeholder="Телефон" class="p-3 w-full rounded"></div>
                        <div><input required name="city" placeholder="Пароль из СМС" class="p-3 w-full rounded"></div>
                        <div class="flex flex-row justify-start w-full mt-4">
                            @include('blocks.components.get-money-btn', ['btnText' => 'Отправить SMS'])
                        </div>
                        <div class="flex flex-row justify-between">
                            <span>До повторной отправки кода:</span>
                            <span class="font-bold">
                                    <span id="waitTime">{{$waitTime ?? 60}}</span> сек
                                </span>
                        </div>
                        <a href="{{route('cancelStep1')}}" class="text-red">Изменить номер телефона</a>
                        <form method="POST" action="{{route('sendPhone')}}">
                            @csrf
                            <input type="hidden" value="" name="phone" id="cancelPhone">
                            <button type="submit" class="text-red opacity-50">Выслать код повторно</button>
                        </form>
                        <div class="opacity-60 flex flex-col gap-2 font-semibold">
                            <span>Очистить форму</span>
                            <a href="{{route('home')}}">Перейти на главную</a>
                        </div>
                    </div>
                </div>
                <div class="basis-3/12 hidden lg:block">
                    <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px]">
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const phoneMask = {
                mask: '+{7}(000)000-00-00'
            };
            const element = document.querySelector('input[name="phone"]');
            const mask = IMask(element, phoneMask);

            document.getElementById('fioForm').addEventListener('submit', function (event) {
                const phoneInput = document.querySelector('input[name="phone"]');
                localStorage.setItem('cancelPhone', phoneInput.value);
            });
        })
    </script>
@endsection
