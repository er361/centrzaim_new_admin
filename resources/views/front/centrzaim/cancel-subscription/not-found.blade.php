@extends('layouts.app')
@section('content')
    <div class="bg-gray-bg">
        <div class="container pt-10 sm:pb-36 pb-16 flex xl:flex-row justify-between flex-col">
            <div class=" gap-8 flex flex-col py-5">
                <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                    <span>
                        Отписка
                    </span>
                    <span class="text-xl font-medium">Подписка не найдена</span>
                </div>
                <div class="bg-white flex flex-row gap-4 max-w-[856px] p-3 rounded sm:text-base text-sm opacity-90">
                    <img src="/assets/miazaim/imgs/card/i.svg" alt="visa" class="">
                    <p>На указанный вами телефон {{format_phone($phone)}} не найдены оформленные страховки. </p>
                </div>
                <div class="get-money-wrapper flex sm:flex-row flex-col  sm:justify-start w-full sm:w-auto gap-4">
                    <a href="{{route('front.unsubscribe.index')}}" >@include('blocks.components.get-money-btn', ['btnText' => 'Проверить другой номер'])</a>
                    <a href="{{route('front.index')}}" >@include('blocks.components.outfill-btn', ['btnText' => 'Вернуться на главную'])</a>
                </div>
            </div>
            <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px] shrink-0 max-lg:mx-auto">
        </div>
    </div>
@endsection
