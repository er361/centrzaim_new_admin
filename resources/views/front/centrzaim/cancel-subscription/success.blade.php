@extends('layouts.app')
@section('content')
    <div class="bg-gray-bg">
        <div class="container pt-10 sm:pb-36 pb-16 flex xl:flex-row justify-between flex-col">
            <div class=" gap-8 flex flex-col py-5">
                <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                    <span>
                        Отписка
                    </span>
                    {!! \App\Services\BannerService\BannerService::get('unsub_3') !!}
                    <span class="text-xl font-medium">Вы успешно отписались</span>
                </div>
                <div>
                    <img src="/assets/miazaim/imgs/green_check.svg" class="max-w-[54px]"/>
                </div>
                <div class="get-money-wrapper flex flex-row justify-start w-full sm:w-auto">
                    <a href="{{route('front.index')}}" >@include('blocks.components.get-money-btn', ['btnText' => 'Вернуться на главную'])</a>
                </div>
            </div>
            <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px] shrink-0 max-lg:mx-auto">
        </div>
    </div>
@endsection

