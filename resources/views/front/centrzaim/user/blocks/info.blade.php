<div class="bg-gray-bg">
    <div class="container gap-8 flex pt-14 flex-col text-black-text">
        <x-payment-info/>
        {{--        <span class="sm:text-[34px] text-xl font-bold mt-20">Оценка финансовой репутации</span>--}}
        {{--        <div class="sm:w-[300px]">--}}
        {{--            @include('blocks.components.get-money-btn',['btnText' => 'Запросить оценку'])--}}
        {{--        </div>--}}
        <span class="sm:text-[34px] text-xl font-bold mt-20">Данные проверки по ФССП</span>
        <div class="p-4 pb-20 bg-white rounded w-full overflow-y-auto">
            @if(data_get($data,'profile.fccp'))
                <x-fccp-table :fccp="data_get($data,'profile.fccp')"/>
            @else
                <b>Запрос обрабатывается, пожалуйста, подождите или обновите страницу позднее</b>
            @endif
        </div>
        <span class="sm:text-[34px] text-xl font-bold">Услуга</span>
        <div class="p-8 bg-white flex sm:flex-row flex-col justify-between sm:w-[639px]">
            <div class="flex flex-col gap-4">
                <span class="text-lg font-bold">Услуга платного подбора займов</span>
                <span class="text-sm">{{$data['profile']['payment_sub_type']}}</span>

                <a href="{{route('front.unsubscribe.index')}}">
                    @include('blocks.components.get-money-btn',['btnText' => 'Отказаться' , 'class' => 'sm:w-[300px]'])
                </a>

            </div>
            <img src="/assets/miazaim/imgs/wallet.svg" alt="wallet">
        </div>
    </div>
    <div class="bg-white py-10 my-20">
        <div class="container flex flex-col gap-4">
            <span class="sm:text-[34px] text-xl font-semibold">О сервисе</span>
            <div class="flex flex-col gap-4">
                <span class="sm:text-2xl text-xl font-semibold">Одна заявка во все МФО</span>
                <span class="sm:text-base text-sm">
                    Благодаря тесному сотрудничеству с кредитными организациями и большой экспертизе в сфере микрофинансов,
                    сервис предоставляет самые выгодные и актуальные кредитные предложения на рынке.
                    Для того, чтобы воспользоваться услугой, вам необходимо лишь заполнить анкету на сайте, все остальное сервис
                    сделает за вас, сэкономив вам большое количество времени и сил. Наш сервис работает со всеми клиентами, нам не
                    важно, какая у вас кредитная история и имеются ли текущие просрочки.
                </span>
            </div>
            <div class="flex flex-col gap-4">
                <span class="sm:text-2xl text-xl font-semibold">Принцип работы сервиса</span>
                <div class="sm:text-base text-sm">
                    <p>
                        Вы заполняете анкету на сайте.
                    </p>
                    <br>
                    <p>
                        По вашим анкетным данным сервис автоматически выбира ет наиболее выгодные предложения и работает
                        непосредственно с кредитными организациями, предоставляющими микрозаймы.
                    </p>
                    <br>
                    <p>
                        Ваша заявка будет отправляться в МФО с наибольшей вероятностью одобрения в данный момент
                        времени с
                        соблюдением необходимых пауз между отправками. При предварительном одобрении кредитные
                        организации
                        сами
                        связываются с вами, и вам необходимо лишь подтвердить своё согласие на займ и забрать деньги.
                    </p>
                    <br>
                    <p>
                        Список МФО, в которые была отправлена ваша заявка, и другую необходимую информацию о работе
                        в рамках
                        платной услуги вы можете наблюдать в личном кабинете, в разделе Отчёты.
                    </p>
                    <br>
                    <p>
                        Стоимость услуги — 1 998,00 ₽
                    </p>
                    <br>
                    <p>
                        Если вы получили займ и более не нуждаетесь в услугах сервиса — вы можете остановить обработку
                        вашей
                        заявки
                        самостоятельно и в любое время в разделе Отказаться от услуги.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
