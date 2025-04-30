@props([
    'title' => 'Подтверждение аккаунта',
    'subTitle' =>'Подтвердите номер телефона',
    'sendCodeUrl' => route('account.activation.store'),
    'resendCodeUrl' => route('account.activation.resend'),
    'changeNumberUrl' => false,
    'phone',
    'waitTime' => 60
  ])
<div class="bg-gray-bg">
    <div class="container pt-10 sm:pb-36 pb-16 flex xl:flex-row justify-between flex-col">
        <div
                x-data="{
                    codeLength: 6,
                    code: '',
                    isActive(){return this.code.length === this.codeLength},
                    waitTime: {{$waitTime ?? 60}},
                    startTimer(){
                        if (this.waitTime > 0) {
                            let timer = setInterval(() => {
                                this.waitTime--;
                                if (this.waitTime <= 0) {
                                    clearInterval(timer);
                                }
                            }, 1000);
                        }
                    }}"
                x-init="startTimer()"
                class=" gap-8 flex flex-col py-5">
            <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                    <span>
                        {{$title}}
                    </span>
                <span class="text-xl font-medium">{{$subTitle}}</span>
            </div>
            <div class="bg-white flex flex-row gap-4 max-w-[856px] p-3 rounded sm:text-base text-sm opacity-90">
                <img src="/assets/miazaim/imgs/card/i.svg" alt="visa" class="">
                <p>На указанный вами телефон {{format_phone($phone)}} отправлен пароль, введите его
                    в поле ниже.</p>
            </div>
            <div class="flex lg:flex-row flex-col gap-4 justify-between">
                <div class="basis-8/12 flex flex-col gap-8">
                    <x-form-errors :errors="$errors"/>
                    <form
                            validateUrl="{{route('account.activation.validateCode')}}"
                            method="POST"
                            action="{{$sendCodeUrl}}" id="smsForm"
                            class="flex flex-col gap-4">
                        @csrf
                        <div class="max-w-[416px] flex xl:flex-row flex-col gap-4">
                            <input x-model="code" :maxlength="codeLength" required name="code"
                                   value="{{old('activation_code')}}"
                                   placeholder="Пароль из смс" class="p-3 rounded w-full max-sm:text-center">
                            <input type="hidden" name="phone" value="{{$phone}}">
                        </div>
                        <div class="get-money-wrapper flex flex-row justify-start w-full sm:w-auto">
                            @include('blocks.components.get-money-btn', ['btnText' => 'Продолжить', 'class' => 'opacity-30', 'activeBtn' => true])
                        </div>
                    </form>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <span>До повторной отправки кода: <span class="font-bold"><span
                                x-text="waitTime"></span> сек</span></span>
                @if($changeNumberUrl)
                    <a href="{{$changeNumberUrl}}" class="text-red">Изменить номер телефона</a>
                @endif
                <form method="GET" action="{{$resendCodeUrl}}">
                    @csrf
                    <button type="submit"
                            class="text-red opacity-50"
                            :disabled="waitTime > 0"
                            :class="{'opacity-50': waitTime > 0, 'opacity-100': waitTime === 0}"
                    >Выслать код повторно
                    </button>
                </form>
            </div>
        </div>
        <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px] shrink-0 max-lg:mx-auto">
    </div>
</div>
@section('scripts')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            validateAndSubmitForm(
                'smsForm',
                document.getElementById('smsForm').attributes.validateurl.value,
                'complete_confirm_sms'
            );
        });
    </script>
@endsection