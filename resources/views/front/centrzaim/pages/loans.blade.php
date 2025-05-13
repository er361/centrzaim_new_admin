@extends('layouts.app')
@section('content')
    <div class="container py-5 flex xl:flex-row flex-col gap-8 justify-between">
        <div class="flex flex-col gap-4 justify-end">
            <div class="text-[28px]">
                @php(/** @var $user \App\Models\User */ $user = auth()->user())
                <span class="capitalize font-bold">{{ $user?->name }}</span>, мы подобрали Вам займы
            </div>
            <div class="text-base opacity-80 flex items-center gap-2">
                <img src="/assets/miazaim/imgs/alert_green.svg" class="w-5 h-5 flex-shrink-0">
                <span>Отправьте заявку <b>минимум в 3 компании</b> для повышения вероятности получения денег</span>
            </div>
        </div>

        <img src="/assets/miazaim/imgs/wallet_credit.svg" alt="flower" class="max-w-[306px] hidden lg:block">
    </div>
    <div class="bg-gray-bg">
        <div class="container sm:py-10 py-5">
            <x-offer-grid :offers="$offers"
                          :offers-type="\App\View\Components\OfferGrid::OFFER_TYPE_NEW"
                          :source-showcase-loans-entity="$sourceShowcaseLoansEntity"
            />
        </div>
    </div>

@endsection
@section('scripts')
    @include('blocks.scripts.offer_click')
    @include('blocks.scripts.offer_click_redirect')
@endsection

