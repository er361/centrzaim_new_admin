@extends('layouts.app')
@section('content')
    <x-banks-notification-modal/>
    <div class="bg-gray-bg">
        @include('blocks.components.progress', ['width' => 'w-8/12'])
        <div class="container py-10 flex xl:flex-row flex-col">
            <div class="gap-8 flex flex-col">
                <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                    <span class="font-bold">ОФОРМЛЕНИЕ ПОДПИСКИ</span>
                </div>
                <div class="bg-white flex flex-row gap-4 max-w-[856px] p-5 rounded sm:text-base text-sm opacity-90">
                    <img src="/assets/miazaim/imgs/card/i.svg" alt="visa" class="">
                    <p>Проверка подлинности карты. Укажите корректные данные своей банковской карты. Мы спишем и вернем 1 рубль для ее проверки.</p>
                </div>
                <div class="flex lg:flex-row flex-col gap-4 justify-between">
                    <div class="flex flex-col gap-2">
                        <iframe class="max-w-[856px] bg-white" id="paymentFrame"
                                src="{{ route('account.payments.form', request()->all()) }}" height="500">
                        </iframe>
                        <span class="bg-white mt-[-10px] max-w-[856px] text-center py-3">
                            <a href="{{route('vitrina')}}" onclick="sendYm(event, this)">
                                У меня нет карты
                            </a>
                        </span>
                        <p class="text-xs opacity-55 font-bold max-w-[856px]">
                            Сервис осуществляет подбор микрозаймов между лицом, желающим оформить займ и кредитными
                            учреждениями. Вы оформляете подписку стоимостью
                            <b>{{config('payments_miazaim.monthly.amount')}} руб.</b> в месяц согласно <a
                                    target="_blank" href="/docs/miazaim/Тарифы.pdf"><b>тарифам</b></a>.
                            Оформление подписки не гарантирует получение займа.
                        </p>
                        <p class="text-xs opacity-55 font-bold max-w-[856px]">
                            Если услуга больше не актуальна для вас (вы больше не нуждаетесь в займе или уже получили
                            его),
                            вы можете самостоятельно отменить подписку в
                            <x-dashboard-link :text="'личном кабинете'"/>
                            .
                        </p>
                    </div>
                </div>
                <div class="flex flex-col gap-4 max-w-[856px]">
                    <span class="text-xl font-semibold">Требования к карте:</span>
                    <div class="flex xl:flex-row flex-col justify-start gap-4 sm:text-base text-sm font-medium">
                        <div class="flex flex-row gap-4">
                            <img src="/assets/miazaim/imgs/card/romb.svg" alt="" class="size-[16px]">
                            <span class="xl:shrink-0">Не нулевой баланс</span>
                        </div>
                        <div class="flex flex-row gap-4">
                            <img src="/assets/miazaim/imgs/card/romb.svg" alt="" class="size-[16px]">
                            <p class="xl:shrink-0">Наличие транзакций за последние 30 дней</p>
                        </div>
                    </div>
                    <div class="flex flex-row gap-4 items-center">
                        <img src="/assets/miazaim/imgs/card/shield.svg" alt="" class="size-[16px]">
                        <p>Данные вашей карты надежно защищены. вся передаваемая информация будет зашифрована по
                            стандарту PCI DSS.</p>
                    </div>
                </div>
                {!! \App\Services\BannerService\BannerService::get('pay') !!}
            </div>

            <img src="/assets/miazaim/imgs/zamok.svg" alt="step1" class="max-w-[306px] shrink-0 hidden sm:block">
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function sendYm(event, link) {
            event.preventDefault(); // Останавливаем мгновенный переход

            ym(99015882, 'reachGoal', 'click_no_card', null, function () {
                console.log('Цель отправлена, отправляем форму...');
                window.location.href = link.href; // Делаем редирект после отправки цели
            });
        }
    </script>
@endsection
