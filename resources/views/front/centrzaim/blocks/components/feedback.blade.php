<div class="bg-white p-6 rounded-xl flex flex-col gap-2 xl:w-[416px] w-[345px]">
    <div class="flex flex-row justify-between">

        <span class="font-bold text-lg">{{$name}}</span>
        <div class="flex flex-row items-center self-start gap-1">
            <span class="text-base opacity-50 font-semibold">{{$rating}}</span>
            <img src="{{ asset('imgs/star.svg') }}" alt="star">
        </div>
    </div>
    <span class="text-sm opacity-40">{{$phone}}</span>
    <p class="text-base opacity-60">{{$text}}</p>
</div>
