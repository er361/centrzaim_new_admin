@props([
    'logo' => '',
    'siteName' => '',
    'rating' => '',
    'sum' => '',
    'percent' => '',
    'duration' => '',
    'license' => '',
    'offerUrl' => '',
])

<div {{ $attributes->class(['flex flex-col bg-white gap-4 p-3 justify-start rounded-[20px] border']) }}>
    <img src="{{ $logo }}" alt="{{ $siteName }}" class="max-h-[50px] mr-auto"/>
    <div class="flex flex-col">
        <div class="flex flex-row sm:gap-2 gap-1 items-center">
            <span class="sm:text-lg text-base font-bold opacity-70">{{ $rating }}</span>
            <img src="/assets/miazaim/imgs/star.svg" alt="star" class="max-h-[16px]"/>
        </div>
        <div class="flex flex-row justify-between">
            <span class="sm:text-base text-sm opacity-60 font-medium">Сумма</span>
            <span class="font-bold sm:text-xl text-base">{{ $sum }}</span>
        </div>
        <div class="flex flex-row justify-between">
            <span class="sm:text-base text-sm opacity-60 font-medium">Ставка</span>
            <span class="font-bold sm:text-xl text-base">от {{ $percent }}%</span>
        </div>
        <div class="flex flex-row justify-between">
            <span class="sm:text-base text-sm opacity-60 font-medium">Срок</span>
            <span class="font-bold sm:text-xl text-base">{{ $duration }}</span>
        </div>
    </div>
    <span class="opacity-40 text-xs">{{ $license ?: 'Лицензия не указана' }}</span>
    <a target="_blank" href="{{ $offerUrl }}" class="offer_click flex justify-center">
        @include('blocks.components.get-money-btn', ['btnText' => 'Получить деньги',
            'class' => 'max-sm:px-2.5 max-sm:text-sm max-sm:py-2.5 rounded-[16px]'])
    </a>
</div>
