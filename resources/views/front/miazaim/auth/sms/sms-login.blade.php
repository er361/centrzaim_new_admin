@extends('layouts.app')
@section('content')
    <div class="bg-gray-bg">
        <div class="container pt-10 sm:pb-36 pb-16 flex xl:flex-row flex-col justify-between">
            <div class=" gap-8 flex flex-col">
                <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                    <span>
                        Вход в личный кабинет
                    </span>
                </div>
                <div class="flex lg:flex-row flex-col gap-4">
                    <div  class="flex flex-col gap-8">
                        <x-form-errors/>
                        <form method="POST" action="{{route('sms.send-code')}}" class="flex flex-col gap-4">
                            @csrf
                            <div class="">
                                <input required name="phone" value="{{old('phone')}}" placeholder="Телефон" class="p-3 rounded w-full">
                            </div>

                            <div class="get-money-wrapper flex flex-row justify-start w-full sm:w-auto">
                                @include('blocks.components.get-money-btn', ['btnText' => 'Отправить CМС'])
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px] shrink-0 max-lg:mx-auto">
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
