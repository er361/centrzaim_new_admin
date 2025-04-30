<div class="bg-black-text pb-14 sm:pt-16 pt-6 flex flex-col gap-6 px-3 sm:px-10
            w-full mx-auto
            sm:bg-[url(/assets/miazaim/imgs/vector_full.svg)] sm:bg-no-repeat sm:bg-cover bg-[url(/assets/miazaim/imgs/vector_mob.png)] bg-no-repeat bg-cover bg-center'
">
    <div class="container text-white text-center flex gap-3 flex-col">
        <span class="text-[28px] sm:text-[40px] font-semibold">Отчет</span>
        <div class="text-base sm:text-lg max-w-[856px] mx-auto flex flex-row sm:gap-[4px]
                gap-[2px] flex-wrap justify-center">
                <span>
                    Мы можем отправить Вашу анкету посредством каналов API сразу в несколько финансовых учреждений,
                благодаря чему экономится Ваше время и увеличивается шанс получения займа.
                </span>
            <span>
                    Тут отображен статус отправленных заявок
                </span>
        </div>
        <div class="sm:grid grid-cols-3 flex flex-col gap-4">
{{--            @include('blocks.components.profile-vitrina-item',['status' => 'denied'])--}}
            @include('blocks.components.profile-vitrina-item',['status' => 'pending', 'send_at' => $sendAt])
{{--            @include('blocks.components.profile-vitrina-item',['status' => 'success'])--}}
        </div>
    </div>
</div>