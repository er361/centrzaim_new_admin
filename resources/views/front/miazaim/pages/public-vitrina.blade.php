@extends('layouts.app')
@section('content')
    <div class="container py-5 flex xl:flex-row flex-col gap-8">
        <div class="flex flex-col gap-4">
            <div class="text-[28px]">
                <span class="capitalize font-bold ">Эти МФО готовы выдать Вам займ!<br>В течении 30 минут!</span>
            </div>
            <p class="text-base opacity-80 font-bold">
                Для гарантированного получения денег рекомендуем отправить 3 и более заявок!<br>Если отказывают в
                получении крупной суммы, просто разбейте ее на несколько частей и отправьте заявки в несколько МФО.
            </p>
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


