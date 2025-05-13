@props(['activeBtn' => false, 'onClick' => ''])
<button
        onclick="{{$onClick}}"
        class="money-btn bg-black-text text-white text-center sm:w-auto w-full
                    px-14 sm:py-3 py-3 rounded cursor-pointer text-lg {{$class ?? ''}}"
        @if($activeBtn)
            :class="{'opacity-30': !isActive()}"
            :disabled="!isActive()"
        @endif

>
    {{$btnText ?? 'Отправить заявку'}}
</button>
