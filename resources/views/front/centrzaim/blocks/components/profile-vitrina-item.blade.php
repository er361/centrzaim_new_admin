@props(['status','send_at'])
<div class="flex flex-col gap-4 bg-white justify-start p-6 text-black-text rounded sm:w-[415px] w-full">
    <img src="/assets/miazaim/imgs/mig_credit_logo.png" class="h-[65px] self-start"/>
    <span class="text-left sm:text-xl text-lg">Анкета отправлена</span>
    <span class="text-left opacity-60 sm:text-base text-sm">{{$send_at}}</span>
    <div class="flex flex-row justify-between">
        <span class="opacity-60 sm:text-base text-sm">Статус</span>
        @if($status === 'denied')
            <span class="bg-red rounded-lg text-white py-0.5 px-8">Отказ</span>
        @elseif($status === 'success')
            <span class="rounded-lg text-white py-0.5 px-8 bg-green-500">Одобрено</span>
        @else($status === 'pending')
            <span class="rounded-lg text-black-text py-0.5 px-8 bg-gray-bg">На рассмотрении</span>
        @endif
    </div>
</div>
