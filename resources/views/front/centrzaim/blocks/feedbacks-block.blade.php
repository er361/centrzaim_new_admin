<div id="feedback" class="bg-black-text pb-14 sm:pt-16 pt-6 flex flex-col gap-6 min-h-[740px] px-3 sm:px-10
            w-full mx-auto
            sm:bg-[url('/assets/miazaim/imgs/vector_full.svg')] sm:bg-no-repeat sm:bg-cover
            bg-[url('/assets/miazaim/imgs/vector_mob.png')] bg-no-repeat bg-cover bg-center
"
>
    <div class="text-white text-center flex gap-3 flex-col">
        <span class="text-[28px] sm:text-[40px] font-semibold">Отзывы</span>
        <span class="text-base sm:text-xl max-w-[856px] mx-auto">Нам доверяют более 1000 клиентов по всей России</span>
    </div>

    <div class="flex container xl:flex-row flex-col items-center xl:justify-evenly gap-4">
        <div class="flex flex-col gap-4">
            @include('blocks.components.feedback',[
               'name' => 'Алексей Чернов',
               'rating' => 4.5,
               'phone' => '+7 (903) 992-ХХ-ХХ',
               'text' => 'Выдали займ реально без проверки кредитной истории! Очень рад, что смог купить бытовую технику по скидкам в новую квартиру'
            ])
            @include('blocks.components.feedback',[
               'name' => 'Александр Белов',
               'rating' => 5,
               'phone' => '+7 (903) 332-ХХ-ХХ',
               'text' => 'Хороший сервис. быстро'
            ])
        </div>
        <div class="flex flex-col gap-4">
            @include('blocks.components.feedback',[
               'name' => 'Алена Романова',
               'rating' => 5,
               'phone' => '+7 (937) 924-ХХ-ХХ',
               'text' => 'Спасибо ВАМ!!! за займ без проверки кредитной истории. Теперь я могу оплатить счета и не беспокоиться о проблемах с хозяйкой квартиры))
        Только 5 звезд!!!'
            ])
            @include('blocks.components.feedback',[
               'name' => 'Юсуп Белов',
               'rating' => 5,
               'phone' => '+7 (997) 449-ХХ-ХХ',
               'text' => 'Мне норм'
            ])
        </div>

        <div class="flex flex-col gap-4">
            @include('blocks.components.feedback',[
                 'name' => 'Виктор Боярский',
                 'rating' => 4.8,
                 'phone' => '+7 (999) 223-ХХ-ХХ',
                 'text' => 'Я оплатил медицинские счета быстро как только потребовалось спасибо'
            ])
            @include('blocks.components.feedback',[
                'name' => 'Александр Белов',
                'rating' => 4.4,
                'phone' => '+7 (997) 449-ХХ-ХХ',
                'text' => 'поменял тормозные колодки, а то зарплата через две недели а ездить надо'
            ])
        </div>
    </div>
</div>


