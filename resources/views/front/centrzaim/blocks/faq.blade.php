<div class="md:px-10 bg-gray-bg flex flex-row justify-center w-full py-12 text-[#0A1838]" x-data="{ open: null }">
    <div class="container">
        <h1 class="text-2xl font-bold text-center mb-8">Часто задаваемые вопросы</h1>

        <div class="space-y-4">
            <!-- Вопрос 1 -->
            <div class="bg-white rounded-xl py-4">
                <button @click="open === 1 ? open = null : open = 1"
                        class="w-full  text-lg text-left font-medium  py-3 px-4
                        rounded-lg flex flex-row justify-between">
                    География сервиса?
                    <img src="/assets/miazaim/imgs/plus_blue.svg" :class="open === 1 ? 'rotate-45' : 'rotate-0'"/>
                </button>
                <div x-show="open === 1"
                     class="mt-2 px-4  opacity-60 text-base "
                     x-cloak
                >
                    Работаем на всей территории Российской Федерации.
                </div>
            </div>

            <!-- Вопрос 2 -->
            <div class="bg-white rounded-xl py-4">
                <button @click="open === 2 ? open = null : open = 2"
                        class="w-full text-lg text-left font-medium  py-3 px-4 rounded-lg
                        flex flex-row justify-between">
                    Сколько стоит услуга?
                    <img src="/assets/miazaim/imgs/plus_blue.svg" :class="open === 2 ? 'rotate-45' : 'rotate-0'"/>
                </button>
                <div x-show="open === 2" class="mt-2 px-4  opacity-60 text-base" x-cloak>
                    Вы оформляете подписку стоимостью {{config('payments_miazaim.monthly.amount')}} руб. в месяц согласно <a target="_blank"
                            href="/docs/miazaim/Тарифы.pdf"><b>тарифам</b></a>.
                </div>
            </div>

            <!-- Вопрос 3 - Развернутый FAQ -->
            <div class="bg-white rounded-xl py-4">
                <button @click="open === 3 ? open = null : open = 3"
                        class="w-full text-lg text-left font-medium  py-3 px-4
                        rounded-lg flex flex-row justify-between">
                    Что дает подписка?
                    <img src="/assets/miazaim/imgs/plus_blue.svg" :class="open === 3 ? 'rotate-45' : 'rotate-0'"/>
                </button>
                <div x-show="open === 3" class="mt-2 px-4  space-y-2 opacity-60 text-base flex flex-col gap-4"
                     x-cloak>
                    <p><b>Доступ в личный кабинет</b><br>
                        Все услуги собраны в Вашем
                        <x-dashboard-link :text="'личном кабинете'" />, где вы можете ими легко управлять.</p>

                    <p><strong>Отправка анкеты по API в несколько МФО</strong><br>
                        Исходя из предоставленных Вами данных, мы можем отправить вашу анкету более чем в 20 различных
                        финансовых учреждений.</p>

                    <p><strong>Расчет оценки финансовой репутации</strong><br>
                        Оценка финансовой репутации является одним из ключевых факторов при принятии решения о выдаче
                        займа.</p>

                    <div>
                        <strong>Проверка по ФССП</strong><br>
                        Мы проверим наличие у Вас долгов через Федеральную службу судебных приставов.
                        <ul class="list-disc list-inside pl-2">
                            <li>Проверка по ФССП</li>
                            <li>Проверка по БКИ</li>
                            <li>Проверка по НБКИ</li>
                        </ul>
                    </div>

                    <p><strong>Push-уведомления с обновлениями и акциями от МФО</strong><br>
                        Вы будете получать уведомления об акциях МФО.</p>

                    <p><strong>Витрина предложений от МФО 24/7</strong><br>
                        Мы подберем для Вас наиболее подходящие предложения по займам.</p>

                    <p><strong>Рассылка SMS-уведомлений</strong><br>
                        Подходящие предложения по займам будут отправлены вам в виде SMS-уведомлений.</p>

                    <p><strong>Рассылка e-mail уведомлений</strong><br>
                        Вы также будете получать предложения по займам по электронной почте.</p>
                </div>
            </div>

            <!-- Вопрос 4 -->
            <div class="bg-white rounded-xl py-4">
                <button @click="open === 4 ? open = null : open = 4"
                        class="w-full text-lg text-left font-medium  py-3 px-4
                        rounded-lg flex flex-row justify-between">
                    Как отказаться от услуги сервиса?
                    <img src="/assets/miazaim/imgs/plus_blue.svg" :class="open === 4 ? 'rotate-45' : 'rotate-0'"/>
                </button>
                <div x-show="open === 4" class="mt-2 px-4  opacity-60 text-base" x-cloak>
                    Вы можете отменить подписку на данную услугу через ваш
                    <x-dashboard-link :text="'личный кабинет'" />.
                </div>
            </div>
        </div>
    </div>

</div>