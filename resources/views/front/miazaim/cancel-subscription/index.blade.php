@extends('layouts.app')
@section('content')
    <div class="bg-gray-bg">
        <div class="container pt-10 sm:pb-36 pb-16 flex xl:flex-row flex-col justify-between">
            <div class=" gap-8 flex flex-col">
                <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                    <span>
                        Отписка
                    </span>
                    <span class="text-xl font-medium">Отписаться по номеру телефона</span>
                </div>
                <div class="flex flex-col gap-4">
                    <div>
                        {!! \App\Services\BannerService\BannerService::get('unsub_1') !!}
                    </div>

                    <div class="bg-white flex flex-row gap-4 max-w-[856px] p-3 rounded sm:text-base text-sm opacity-90">
                        <img src="/assets/miazaim/imgs/card/i.svg" alt="visa" class="">
                        <p>Укажите номер телефона, который вы использовали при регистрации.</p>
                    </div>
                </div>

                <div class="flex lg:flex-row flex-col gap-4 justify-between">
                    <div class="basis-8/12 flex flex-col gap-8">
                        <x-form-errors/>
                        <form method="POST" action="{{route('front.unsubscribe.send.code')}}" class="flex flex-col gap-4">
                            @csrf
                            <div class="max-w-[416px] flex xl:flex-row flex-col gap-4">
                                <input required name="phone" placeholder="Телефон" class="p-3 rounded w-full">
                            </div>

                            <div class="get-money-wrapper flex flex-row justify-start w-full sm:w-auto">
                                @include('blocks.components.get-money-btn', ['btnText' => 'Продолжить'])
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
