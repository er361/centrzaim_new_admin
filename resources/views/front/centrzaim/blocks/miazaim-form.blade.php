<div class="calc main-info__calc"
     x-data="{
                termsAgree: false,
                additionalTermsAgree: false,
                totalLength: 0,
                symbolsToCheck: 3,
                hasScrolled: false,
                shouldAutoAgree: @json(\App\Services\AccountService\AccountSourceService::getSource() !== null),
                updateAgreement() {
                    if (this.shouldAutoAgree) {
                        const inputs = document.querySelectorAll('input[name=fullname], input[name=mphone]');
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
     x-init="updateAgreement()"
>
    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-8">

            <p class="calc__title title"><span class="text-info">Заполните</span> заявку прямо
                сейчас и
                получите <span class="text-attention">решение в <span class="timeGetMoney"
                                                                      data-time>18:32</span></span>
            </p>

            <div class="money-slider-container flex flex-col md:flex-row justify-evenly gap-8">
                <div class="flex flex-col grow">
                    <div>
                        <span class="amountLabel text-2xl sm:text-3xl font-medium font-numbers">63К ₽</span>
                    </div>
                    <div class="money-slider money-slider-css mt-6 mb-5"></div>
                    <div class="flex justify-between text-xs sm:text-sm opacity-70">
                        <span>1000 ₽</span>
                        <span>100 000 ₽</span>
                    </div>
                </div>

                <div class="flex flex-col grow">
                    <div>
                        <span class="daysLabel text-2xl sm:text-3xl font-medium font-numbers">12 дней</span>
                    </div>
                    <div class="day-slider money-slider-css mt-6 mb-5"></div>
                    <div class="flex justify-between text-xs sm:text-sm opacity-70">
                        <span>5 дней</span>
                        <span>365 дней</span>
                    </div>
                </div>
            </div>

            <div class="shortForm">

                <div class="flex flex-row">
                    <div class="w-8/12 flex flex-col gap-4">
                        <x-form-errors :errors="$errors"/>
                        <form
                                validateUrl="{{route('auth.register.validate')}}"
                                method="POST"
                                action="{{route('auth.register.store')}}"
                                id="fioForm"
                                @if($shouldRedirectPp)
                                    target="_blank"
                                @endif
                                @if($shouldRedirectPp)
                                    onsubmit="redirect(event, '{{route('public.vitrina')}}')"
                                @endif
                        >
                            @csrf
                            <input required
                                   name="fullname"
                                   type="hidden"
                                   id="fioHiddenInput"
                                   initial-query="{{old('fullname')}}"
                                   has-error="1"
                                   @input="updateAgreement()"
                            >
                            <div class="flex flex-row !w-full gap-2">

                                <div id="fio"
                                     inputClassName="rounded-xl !h-[60px]"
                                     class="w-full"
                                >
                                </div>

                                <div class="w-full h-[60px]"><input required name="mphone"
                                                                    value="{{old('mphone')}}"
                                                                    placeholder="Телефон"
                                                                    class="
            p-3 w-full rounded w-full h-full rounded-xl focus:border focus:border-blue !bg-[#F6F6F6]"
                                                                    @input="updateAgreement()">
                                </div>
                            </div>


                            <input type="hidden" name="terms_agree" :value="termsAgree ? 1 : 0">
                            <input type="hidden" name="additional_terms_agree" :value="additionalTermsAgree ? 1 : 0">
                            <!-- Hidden inputs for amount and days that will be updated by JS -->
                            <input type="hidden" id="sliderAmount" name="amount" value="{{ $amount ?? '63000' }}">
                            <input type="hidden" id="sliderDays" name="days" value="{{ $days ?? '12' }}">
                            <button id="submitFioForm" type="submit" class="hidden"></button>
                        </form>
                    </div>
                    <div class="w-4/12">
                        <div class="flex flex-col gap-4 w-full">
                            <div class="flex flex-row justify-center">
                                <button class="money-btn bg-blue text-white text-center
                            xl:w-auto w-full min-w-[280px] h-[60px]
                            px-14 sm:py-3 py-3 rounded-2xl cursor-pointer text-lg">
                                    Получить деньги
                                </button>
                            </div>
                            <div class="flex flex-row items-center justify-center gap-2">
                                <img src="/assets/ctr/img/new-site/checkbox-blue.svg" class="size-[20px]" alt="checkbox"/>
                                <span class="text-sm text-center opacity-55">Быстро и надежно</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <!-- Первый чекбокс с исправленными путями к изображениям -->
            <div class="flex flex-row gap-1 items-start">
                <input x-model="termsAgree"
                       type="checkbox"
                       id="terms_agree"
                       class="hidden peer">
                <label for="terms_agree"
                       class="inline-block flex-shrink-0 w-[18px] h-[18px] bg-no-repeat bg-center cursor-pointer mr-2 border border-blue"
                       :class="{
               'bg-[url(/assets/ctr/img/chbx_uncheked.png)]': !termsAgree && !{{ $errors->has('terms_agree') ? 'true' : 'false' }},
               'bg-[url(/assets/ctr/img/chbx_checked.svg)]': termsAgree,
               'bg-[url(/assets/ctr/img/chbx_error.svg)]': !termsAgree && {{ $errors->has('terms_agree') ? 'true' : 'false' }}
           }">
                </label>
                <p class="text-xs opacity-60" :class="{ 'text-red-500': {{ $errors->has('terms_agree') ? 'true' : 'false' }} && !termsAgree }">
                    Подтверждаю, что мне <b>есть 18 лет</b>. <b>Даю свое
                        <a target="_blank" href="/docs/ctr/Согласие_на_обработку_персональных_данных.docx">
                            согласие
                            на обработку персональных данных
                        </a>
                </p>
            </div>

            <!-- Второй чекбокс с исправленными путями к изображениям -->
            <div class="flex flex-row gap-1 items-start">
                <input x-model="additionalTermsAgree"
                       type="checkbox"
                       id="additional_terms_agree"
                       class="hidden peer">
                <label for="additional_terms_agree"
                       class="inline-block flex-shrink-0 w-[18px] h-[18px] bg-no-repeat bg-center cursor-pointer mr-2 border border-blue"
                       :class="{
               'bg-[url(/assets/ctr/img/chbx_uncheked.png)]': !additionalTermsAgree && !{{ $errors->has('additional_terms_agree') ? 'true' : 'false' }},
               'bg-[url(/assets/ctr/img/chbx_checked.svg)]': additionalTermsAgree,
               'bg-[url(/assets/ctr/img/chbx_error.svg)]': !additionalTermsAgree && {{ $errors->has('additional_terms_agree') ? 'true' : 'false' }}
           }">
                </label>
                <p class="text-xs opacity-60" :class="{ 'text-red-500': {{ $errors->has('additional_terms_agree') ? 'true' : 'false' }} && !additionalTermsAgree }">
                    Я даю согласие на <b><a target="_blank"
                                            href="/docs/ctr/data.docx">получение
                            рекламно-информационных сообщений.</a></b>
                </p>
            </div>

        </div>
    </div>
    <!-- end calc -->
</div>