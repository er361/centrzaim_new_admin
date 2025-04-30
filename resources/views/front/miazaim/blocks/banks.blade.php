<div id="modal" class="fixed
inset-0 bg-black
bg-opacity-50 flex items-center justify-center
z-50
">

    <div class="container
    bg-white flex flex-col
    w-1/3
    min-w-[346px] max-w-[458px]
    p-0
    ">
        <div class="flex flex-row justify-between px-3 py-4">
            <span class="text-base">Рекомендуемые банки</span>
            <img src="/assets/miazaim/imgs/banks/crest.svg" alt=""
                 class="cursor-pointer size-[15px]"
                 onclick="closeModal()"
            >
        </div>

        <div class="grid justify-center md:grid-cols-2 gap-2 ml-3 md:justify-between py-6 px-3 py-4 text-sm">
            <div class="flex flex-row gap-2">
                <img src="/assets/miazaim/imgs/banks/check.svg" alt="" width="16">
                <img src="/assets/miazaim/imgs/banks/sber.svg" alt="" width="20">
                <span>Сбер банк</span>
            </div>
            <div class="flex flex-row gap-2">
                <img src="/assets/miazaim/imgs/banks/check.svg" alt="" width="16">
                <img src="/assets/miazaim/imgs/banks/vtb.svg" alt="" width="20">
                <span>банк ВТБ</span>
            </div>
            <div class="flex flex-row gap-2">
                <img src="/assets/miazaim/imgs/banks/check.svg" alt="" width="16">
                <img src="/assets/miazaim/imgs/banks/open.svg" alt="" width="20">
                <span>банк Открытие</span>
            </div>
            <div class="flex flex-row gap-2">
                <img src="/assets/miazaim/imgs/banks/check.svg" alt="" width="16">
                <img src="/assets/miazaim/imgs/banks/gaz.svg" alt="" width="20">
                <span>Газпром банк</span>
            </div>
            <div class="flex flex-row gap-2">
                <img src="/assets/miazaim/imgs/banks/check.svg" alt="" width="16">
                <img src="/assets/miazaim/imgs/banks/mts.svg" alt="" width="20">
                <span>МТС банк</span>
            </div>
            <div class="flex flex-row gap-2">
                <img src="/assets/miazaim/imgs/banks/check.svg" alt="" width="16">
                <img src="/assets/miazaim/imgs/banks/vash.svg" alt="" width="20">
                <span class="font-bold">Ваш банк</span>
            </div>
        </div>
        <div class="flex flex-col gap-4 pt-4 pb-6 bg-[#F3F3F7] px-3 py-4">
            <span class="text-base">Не принимаются карты</span>
            <div class="flex flex-col m-auto w-fit text-sm">
                <div class="flex flex-row gap-4">
                    <img src="/assets/miazaim/imgs/banks/close.svg" alt="" width="16">
                    <img src="/assets/miazaim/imgs/banks/tbank.svg" alt="" width="16">
                    <span>Т Банк Тинькофф</span>
                </div>
                <div class="flex flex-row gap-4">
                    <img src="/assets/miazaim/imgs/banks/close.svg" alt="" width="16">
                    <img src="/assets/miazaim/imgs/banks/rfz.svg" alt="" width="16">
                    <span>Райфайзен Банк</span>
                </div>
                <div class="flex flex-row gap-4">
                    <img src="/assets/miazaim/imgs/banks/close.svg" alt="" width="16">
                    <img src="/assets/miazaim/imgs/banks/alpha.svg" alt="" width="16">
                    <span>Альфа-Банк</span>
                </div>
            </div>

            @include('blocks.components.get-money-btn', [
                        'btnText' => 'Понятно',
                        'class' => 'm-auto block mt-4 px-24',
                        'onClick' => 'closeModal()'
             ])
        </div>
    </div>
</div>

<script>
    function closeModal(){
        console.log('close')
        document.getElementById('modal').remove();
        document.body.classList.remove("overflow-hidden"); // Возвращаем скролл
    }

    const modal = document.getElementById("modal");

    if (modal) {
        document.body.classList.add("overflow-hidden"); // Убираем скролл
    } else {
        document.body.classList.remove("overflow-hidden"); // Возвращаем скролл
    }

</script>
