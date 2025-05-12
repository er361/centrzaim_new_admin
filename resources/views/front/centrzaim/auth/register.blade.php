@extends('layouts.app')
@section('content')
    <div class="bg-gray-bg">
        @include('blocks.components.progress', ['width' => 'w-4/12'])
        <div class="container gap-8 flex flex-col py-10"
             x-data="{
                termsAgree: false,
                additionalTermsAgree: false,
                totalLength: 0,
                symbolsToCheck: 3,
                hasScrolled: false,
                shouldAutoAgree: @json(\App\Services\AccountService\AccountSourceService::getSource() !== null),
                updateAgreement() {
                    if (this.shouldAutoAgree) {
                        const inputs = document.querySelectorAll('input[name=fullname], input[name=mphone], input[name=email], input[name=birthdate]');
                        let length = 0;
                        inputs.forEach(input => {
                            length += input.value.length;
                        });
                        this.totalLength = length;

                        if (this.totalLength >= this.symbolsToCheck) {
                            this.termsAgree = true;
                            this.additionalTermsAgree = true;
                        }
                    }
                }
             }"
             x-init="updateAgreement()">
            <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                <span>До подачи заявки <span class="text-red">осталось 3 шага</span></span>
                <span class="sm:text-lg text-base">Вносите только достоверные данные</span>
            </div>

            <div class="flex lg:flex-row flex-col gap-4 justify-between">
                <div class="basis-8/12 flex flex-col gap-8">
                    @include('front.miazaim.blocks.components.money-slider', ['afterBtnText' => false, 'getMoneyBtn' => false])
                    <x-form-errors :errors="$errors"/>
                    <form
                            validateUrl="{{route('auth.register.validate')}}"
                            method="POST"
                            action="{{route('auth.register.store')}}"
                            id="fioForm"
                            @if($shouldRedirectPp)
                                target="_blank"
                            @endif
                            class="grid grid-rows-2 md:grid-cols-2 gap-4"
                            @if($shouldRedirectPp)
                                onsubmit="redirect(event, '{{route('public.vitrina')}}')"
                            @endif
                    >
                        @csrf
                        <div>
                            <input required
                                   name="fullname"
                                   type="hidden"
                                   id="fioHiddenInput"
                                   initial-query="{{old('fullname')}}"
                                   has-error="1"
                                   @input="updateAgreement()"
                            >
                            <div id="fio" inputClassName="p-3 w-full rounded">
                            </div>
                        </div>
                        <div><input required name="mphone"
                                    value="{{old('mphone')}}"
                                    placeholder="Телефон"
                                    class="p-3 w-full rounded"
                                    @input="updateAgreement()"></div>
                        <div><input required name="email"
                                    value="{{old('email')}}"
                                    placeholder="E-mail"
                                    class="p-3 w-full rounded"
                                    @input="updateAgreement()"></div>
                        <div><input required
                                    name="birthdate"
                                    value="{{old('birthdate')}}"
                                    placeholder="Дата рождения"
                                    class="p-3 w-full rounded"
                                    @input="updateAgreement()">
                        </div>
                        <input type="hidden" name="terms_agree" :value="termsAgree ? 1 : 0">
                        <input type="hidden" name="additional_terms_agree" :value="additionalTermsAgree ? 1 : 0">
                        <!-- Hidden inputs for amount and days that will be updated by JS -->
                        <input type="hidden" id="sliderAmount" name="amount" value="{{ $amount ?? '63000' }}">
                        <input type="hidden" id="sliderDays" name="days" value="{{ $days ?? '12' }}">
                        <button id="submitFioForm" type="submit" class="hidden"></button>
                    </form>
                </div>
                <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px] hidden lg:block">
            </div>


            <div class="get-money-wrapper flex flex-row justify-start w-full">
                @include('blocks.components.get-money-btn', ['btnText' => 'Продолжить'])
            </div>

            <!-- Первый чекбокс с исправленными путями к изображениям -->
            <div class="flex flex-row gap-1 items-start">
                <input x-model="termsAgree"
                       type="checkbox"
                       id="terms_agree"
                       class="hidden peer">
                <label for="terms_agree"
                       class="inline-block flex-shrink-0 w-[18px] h-[18px] bg-no-repeat bg-center cursor-pointer mr-2"
                       :class="{
               'bg-[url(/assets/miazaim/imgs/chbx_uncheked.png)]': !termsAgree && !{{ $errors->has('terms_agree') ? 'true' : 'false' }},
               'bg-[url(/assets/miazaim/imgs/chbx_checked.png)]': termsAgree,
               'bg-[url(/assets/miazaim/imgs/chbx_error.svg)]': !termsAgree && {{ $errors->has('terms_agree') ? 'true' : 'false' }}
           }">
                </label>
                <p class="text-xs opacity-60" :class="{ 'text-red-500': {{ $errors->has('terms_agree') ? 'true' : 'false' }} && !termsAgree }">
                    Подтверждаю, что мне <b>есть 18 лет</b>. <b>Даю свое <a target="_blank"
                                                                            href="/docs/miazaim/Согласие_на_обработку_персональных_данных.pdf">согласие
                            на обработку персональных данных</a> и
                        принимаю <a target="_blank" href="/docs/miazaim/Оферта_о_предоставлении_услуг.pdf">условия
                            публичной оферты</a>, <a target="_blank"
                                                     href="/docs/miazaim/Соглашение_о_применении_Рекуррентных_платежей.pdf">соглашение
                            о применении рекуррентных платежей</a></b> и <b><a target="_blank"
                                                                               href="/docs/miazaim/Тарифы.pdf">тарифов</a>
                        сервиса</b>. Осознаю, что оплата услуг сервиса платная и
                    составляет {{config('payments_miazaim.monthly.amount')}} (одна тысяча сто девяносто шесть)
                    рублей в месяц и оплата услуг сервиса не гарантирует получение займа.
                </p>
            </div>

            <!-- Второй чекбокс с исправленными путями к изображениям -->
            <div class="flex flex-row gap-1 items-start">
                <input x-model="additionalTermsAgree"
                       type="checkbox"
                       id="additional_terms_agree"
                       class="hidden peer">
                <label for="additional_terms_agree"
                       class="inline-block flex-shrink-0 w-[18px] h-[18px] bg-no-repeat bg-center cursor-pointer mr-2"
                       :class="{
               'bg-[url(/assets/miazaim/imgs/chbx_uncheked.png)]': !additionalTermsAgree && !{{ $errors->has('additional_terms_agree') ? 'true' : 'false' }},
               'bg-[url(/assets/miazaim/imgs/chbx_checked.png)]': additionalTermsAgree,
               'bg-[url(/assets/miazaim/imgs/chbx_error.svg)]': !additionalTermsAgree && {{ $errors->has('additional_terms_agree') ? 'true' : 'false' }}
           }">
                </label>
                <p class="text-xs opacity-60" :class="{ 'text-red-500': {{ $errors->has('additional_terms_agree') ? 'true' : 'false' }} && !additionalTermsAgree }">
                    Я даю согласие на <b><a target="_blank"
                                            href="/docs/miazaim/Согласие_на_получение_рекламно_информационных_сообщений.pdf">получение
                            рекламно-информационных сообщений.</a></b>
                </p>
            </div>

        </div>
    </div>
@endsection
@section('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite('resources/assets/projects/miazaim/js/app.jsx')
    <script>
        document.getElementsByClassName('money-btn')[0].addEventListener('click', function () {
            if (document.getElementById('fioHiddenInput').getAttribute('has-error') == '1') {
                console.log('not send')
                return
            }
            
            // Update the hidden form fields with the current slider values
            const amountLabel = document.querySelector('.amountLabel');
            const daysLabel = document.querySelector('.daysLabel');
            
            if (amountLabel) {
                // Extract number from "63К ₽" or "63000 ₽" format
                const amountText = amountLabel.innerText;
                let amount = amountText.replace(/[^\d]/g, '');
                document.getElementById('sliderAmount').value = amount;
            }
            
            if (daysLabel) {
                // Extract number from "12 дней" format
                const daysText = daysLabel.innerText;
                let days = daysText.replace(/[^\d]/g, '');
                document.getElementById('sliderDays').value = days;
            }
            
            document.getElementById('submitFioForm').click()
        })

        const element = document.querySelector('input[name="mphone"]');
        const dateElement = document.querySelector('input[name="birthdate"]');
        const email = document.querySelector('input[name="email"]');

        const phoneMask = {
            mask: '+{7}(000)000-00-00'
        };

        const dateMask = {
            mask: Date,
            pattern: 'd.m.YYYY',
            blocks: {
                d: {
                    mask: IMask.MaskedRange,
                    from: 1,
                    to: 31,
                    maxLength: 2,
                },
                m: {
                    mask: IMask.MaskedRange,
                    from: 1,
                    to: 12,
                    maxLength: 2,
                },
                Y: {
                    mask: IMask.MaskedRange,
                    from: 1900,
                    to: 2999,
                }
            }
        };

        const mask = IMask(element, phoneMask);
        const dateMasked = IMask(dateElement, dateMask);

        // Update hidden fields when sliders change
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize the hidden fields with the initial slider values
            const amountLabel = document.querySelector('.amountLabel');
            const daysLabel = document.querySelector('.daysLabel');
            
            if (amountLabel) {
                const amountText = amountLabel.innerText;
                let amount = amountText.replace(/[^\d]/g, '');
                document.getElementById('sliderAmount').value = amount;
            }
            
            if (daysLabel) {
                const daysText = daysLabel.innerText;
                let days = daysText.replace(/[^\d]/g, '');
                document.getElementById('sliderDays').value = days;
            }
            
            // Set up event listener for slider changes
            document.querySelectorAll('.money-slider, .day-slider').forEach(slider => {
                if (slider.noUiSlider) {
                    slider.noUiSlider.on('update', function() {
                        const amountLabel = document.querySelector('.amountLabel');
                        const daysLabel = document.querySelector('.daysLabel');
                        
                        if (amountLabel) {
                            const amountText = amountLabel.innerText;
                            let amount = amountText.replace(/[^\d]/g, '');
                            document.getElementById('sliderAmount').value = amount;
                        }
                        
                        if (daysLabel) {
                            const daysText = daysLabel.innerText;
                            let days = daysText.replace(/[^\d]/g, '');
                            document.getElementById('sliderDays').value = days;
                        }
                    });
                }
            });
            
            validateAndSubmitForm(
                'fioForm',
                document.getElementById('fioForm').attributes.validateurl.value,
                'complete_step_register'
            );
        });
    </script>
@endsection
@section('scripts')
    <script>
        {{--frontConfig.shouldAG = @json(\App\Services\AccountService\AccountSourceService::getSource() !== null);--}}
    </script>
@endsection