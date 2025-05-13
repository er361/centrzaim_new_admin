@props(['offers' => []])
<div class="bg-gray-bg">
    <div class="container sm:py-20 py-5 flex flex-col gap-8">
        <span class="text-3xl font-bold">Индивидуальные предложения</span>
        <x-offer-grid :offers="$data['offers']"
                      :offers-type="\App\View\Components\OfferGrid::OFFER_TYPE_NEW"
                      :source-showcase-loans-entity="$sourceShowcaseLoansEntity"
        />
    </div>
</div>
