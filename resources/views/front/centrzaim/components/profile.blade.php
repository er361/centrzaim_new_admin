@props(['profile' => []])
<div class="gap-8 flex flex-row justify-between">
    <div class="flex flex-col gap-4 lg:w-[856px]">
        <div class="sm:text-3xl text-2xl font-semibold">
            <span>Личный кабинет</span>
        </div>
        <div class="bg-white flex flex-row text-base px-3 py-2 gap-2 rounded">
            <img src="/assets/miazaim/imgs/card/i.svg">
            <span>На этой странице собрана информация обо всех предоставленных Вам услугах</span>
        </div>
        <div class="sm:grid flex flex-col grid-cols-2 grid-rows-2 gap-4">
            <div class="bg-white p-3">
                {{ $profile['name'] ?? '' }}
            </div>
            <div class="bg-white p-3">
                {{ $profile['phone'] ?? '' }}
            </div>
            <div class="bg-white p-3">
                {{ $profile['birthdate'] ?? '' }}
            </div>
        </div>
        <span class="text-xl font-semibold py-4">Паспортные данные</span>
    </div>
    <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px] hidden sm:block">
</div>