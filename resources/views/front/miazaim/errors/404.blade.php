@extends('layouts.app')
@section('content')
    <div class="bg-gray-bg">
        <div class="container pt-10 px-8 sm:pb-36 pb-16 flex xl:flex-row justify-between flex-col">
            <div class=" gap-8 flex flex-col py-5">
                <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                    <span>
                        <span class="text-red">Error 404</span>
                    </span>
                    <span class="text-xl font-medium">Cтраницы не существует</span>
                </div>

                <div class="get-money-wrapper flex sm:flex-row flex-col  sm:justify-start w-full sm:w-auto gap-4">
                    <a href="{{route('home')}}" >@include('blocks.components.get-money-btn', ['btnText' => 'Вернуться на главную'])</a>
                </div>
            </div>
            <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px] shrink-0 max-lg:mx-auto">
        </div>
    </div>
@endsection
