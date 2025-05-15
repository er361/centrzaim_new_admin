@props([
    'title' => 'Подтверждение аккаунта',
    'subTitle' =>'Подтвердите номер телефона',
    'sendCodeUrl' => route('account.activation.store'),
    'resendCodeUrl' => route('account.activation.resend'),
    'changeNumberUrl' => false,
    'phone',
    'waitTime' => 60
  ])
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
    class="flex flex-col gap-8 w-full">
    
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
                   placeholder="Пароль из смс" 
                   class="p-3 rounded w-full max-sm:text-center bg-gray-100 h-[60px] rounded-xl">
            <input type="hidden" name="phone" value="{{$phone}}">
        </div>
        <div class="get-money-wrapper flex flex-row justify-start w-full sm:w-auto">
            <div class="flex flex-col justify-center gap-8 justify-start w-full sm:w-auto">
                <button type="submit" class="money-btn bg-blue text-white text-center sm:w-auto w-full min-w-[313px] h-[60px] px-14 sm:py-3 py-3 rounded-2xl cursor-pointer text-lg !bg-[#2F76E2]"
                       :class="{'opacity-30': !isActive(), 'opacity-100': isActive()}"
                       :disabled="!isActive()">
                    Подтвердить
                </button>
                
                <div class="flex flex-col gap-2">
                    <span>До повторной отправки кода: <span class="font-bold"><span
                            x-text="waitTime"></span> сек</span></span>
                    @if($changeNumberUrl)
                        <a href="{{$changeNumberUrl}}" class="text-red">Изменить номер телефона</a>
                    @endif
                    <form method="GET" action="{{$resendCodeUrl}}">
                        @csrf
                        <button type="submit"
                                class="text-[#484E63] font-bold text-sm text-center cursor-pointer"
                                :disabled="waitTime > 0"
                                :class="{'opacity-50': waitTime > 0, 'opacity-100': waitTime === 0}"
                        >Выслать код повторно
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </form>
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