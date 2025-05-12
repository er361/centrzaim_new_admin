
@extends('layouts.app')
@section('content')
    <main class="main">
        <div class="offers-page">
            <div class="container">
                <div class="offers-page__row">
                    <div class="offers-page__info">
                        <h1 class="title offers-page__title">
                            <span data-user-name class="capitalize">Имя юзера, (или нет)</span>, данные компании готовы выдать Вам заём
                        </h1>
                        <p class="offers-page__text">Если Вам не одобряют требуемую сумму, разделите её на части и подайте заявки в несколько МФО.</p>
                    </div>
                    <div class="offers-page__img">
                        <img srcset="/img/methods@2x.webp 2x, /img/methods.webp"
                             src="/img/methods_origin.png" alt="Займ">
                    </div>
                </div>
            </div>
        </div>
        <div class="container sm:py-10 py-5">
            <x-offer-grid :offers="$offers"
                          :offers-type="\App\View\Components\OfferGrid::OFFER_TYPE_NEW"
                          :source-showcase-loans-entity="$sourceShowcaseLoansEntity"
            />
        </div>
    </main>
@endsection
