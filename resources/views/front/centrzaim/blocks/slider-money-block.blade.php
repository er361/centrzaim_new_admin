<!-- resources/views/blocks/slider-money-blocks.blade.php -->
@php
    $isWhiteBg = $isWhiteBg ?? false;
@endphp

<div class="{{ $isWhiteBg ? 'bg-gray-bg' : 'bg-black-text' }} pb-14 sm:pt-16 pt-6 flex flex-col gap-6 min-h-[740px] px-3 sm:px-10
            w-full mx-auto
            {{ $isWhiteBg ? '' : 'sm:bg-[url(\'/assets/miazaim/imgs/vector_full.svg\')] sm:bg-no-repeat sm:bg-cover
                                     bg-[url(\'/assets/miazaim/imgs/vector_mob.png\')] bg-no-repeat bg-cover bg-center' }}">

    <div class="{{ $isWhiteBg ? 'text-black-text' : 'text-white' }} text-center flex gap-3 flex-col">
        <span class="text-[28px] sm:text-[40px] font-semibold">Получите <span class="text-red">деньги</span> без проверок</span>

        <div class="text-base sm:text-xl min-w-[345px] mx-auto flex flex-col sm:gap-4
                    gap-2 flex-wrap justify-center font-bold
                    mb-4">
            <div class="flex gap-4 flex-row">
                <img src="/assets/miazaim/imgs/green_galka.svg" width="16" alt="galka">
                <span>С любой кредитной историей</span>
            </div>
            <div class="flex gap-4 flex-row">
                <img src="/assets/miazaim/imgs/green_galka.svg" width="16" alt="galka">
                <span>Без справок с работы</span>
            </div>
            <div class="flex gap-4 flex-row">
                <img src="/assets/miazaim/imgs/green_galka.svg" width="16" alt="galka">
                <span>Нужен только паспорт</span>
            </div>
        </div>

    </div>
    <div class="max-w-[1400px] mx-auto w-full flex flex-col sm:p-10 p-5 sm:gap-8 gap-6 rounded bg-white">

        <div class="text-lg sm:text-[26px] text-center font-semibold
        flex flex-row justify-center
        sm:gap[4px] gap-[6px] flex-wrap">
            <span>Ваш</span> <span>займ</span> <span>может</span> <span>быть</span> <span>одобрен</span> <span>уже</span>
            <span class="text-red">в</span>
            <span class="text-red timeGetMoney ml-1.5">18:32</span>
        </div>
        @include('blocks.components.money-slider')
    </div>
</div>
