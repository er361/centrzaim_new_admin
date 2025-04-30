<div class="money-slider-container flex flex-col md:flex-row justify-evenly gap-8">
    <div class="flex flex-col grow">
        <div>
            <span class="amountLabel text-2xl sm:text-3xl font-medium font-numbers">63К ₽</span>
        </div>
        <div class="money-slider money-slider-css mt-6 mb-5"></div>
        <div class="flex justify-between text-xs sm:text-sm opacity-70">
            <span>1000 ₽</span>
            <span>100 000 ₽</span>
        </div>
    </div>

    <div class="flex flex-col grow">
        <div>
            <span class="daysLabel text-2xl sm:text-3xl font-medium font-numbers">12 дней</span>
        </div>
        <div class="day-slider money-slider-css mt-6 mb-5"></div>
        <div class="flex justify-between text-xs sm:text-sm opacity-70">
            <span>5 дней</span>
            <span>365 дней</span>
        </div>
    </div>
</div>

@if($getMoneyBtn ?? true)
    <div class="get-money-wrapper flex flex-col gap-2">
        <div class="flex flex-row justify-center w-full">
            @include('blocks/components/get-money-btn')
        </div>
        @if($afterBtnText ?? true)
            <div class="text-[9px] text-center max-w-[842px] mx-auto opacity-55 flex flex-col gap-2 mt-4">
                <span>
                    Сервис осуществляет подбор микрозаймов между лицом, желающим оформить займ и кредитными
                    учреждениями. Вы оформляете подписку стоимостью {{config('payments_miazaim.monthly.amount')}}₽ в месяц согласно тарифам.
                    Оформление подписки не гарантирует получение займа.
            </span>
            </div>

        @endif
    </div>
@endif
