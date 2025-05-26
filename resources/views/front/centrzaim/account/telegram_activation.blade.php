@extends('layouts.app')

@section('styles')
    <style>
        body {
            background: #FFFFFF;
        }
    </style>
@endsection

@section('content')
    <div class="main-info">
        <div class="container flex flex-col gap-8">
            <h1 class="title main-info__title">Подтверждение аккаунта</h1>

            <div class="container pt-10 pb-16 flex xl:flex-row justify-between flex-col bg-white rounded-3xl shadow-lg">
                <div class="gap-8 flex flex-col py-5">
                    <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                        <div class="text-xl font-medium flex flex-row gap-2 items-center">
                            <img src="/assets/miazaim/imgs/telega.svg" alt="visa" width="42" height="42" class="">
                            <span>через Telegram</span>
                        </div>
                    </div>
                    <div class="bg-[#EDF2FE] flex flex-row gap-4 max-w-[856px] p-3 rounded sm:text-base text-sm opacity-90">
                        <img src="/assets/miazaim/imgs/card/i.svg" alt="visa" width="22" height="22">
                        <p>Для подтверждения аккаунта, введите полученный в <b>Telegram</b> код.</p>
                    </div>

                    <div class="flex lg:flex-row flex-col gap-4 justify-between">
                        <div class="basis-8/12 flex flex-col gap-8">
                            <x-form-errors :errors="$errors"/>
                            <form
                                    validateUrl="{{route('account.activation.validateCode')}}"
                                    method="POST"
                                    action="{{route('account.activation.store')}}" id="tgForm"
                                    class="flex flex-col gap-4">
                                @csrf
                                <div class="max-w-[416px] flex xl:flex-row flex-col gap-4">
                                    <input maxlength="6" required name="code"
                                           value="{{old('activation_code')}}"
                                           placeholder="Пароль из смс"
                                           class="p-3 rounded w-full max-sm:text-center bg-gray-100 h-[60px] rounded-xl">
                                    <input type="hidden" name="phone" value="{{$phone}}">
                                </div>
                                <div class="get-money-wrapper flex flex-row justify-start w-full sm:w-auto">
                                    <div class="flex flex-col justify-center gap-8 justify-start w-full sm:w-auto">
                                        <button type="submit" class="money-btn bg-blue text-white text-center sm:w-auto w-full min-w-[313px] h-[60px] px-14 sm:py-3 py-3 rounded-2xl cursor-pointer text-lg !bg-[#2F76E2]">
                                            Подтвердить
                                        </button>
                                        <a href="{{route('account.activation.method.sms')}}"
                                           class="text-[#484E63] font-bold text-sm text-center cursor-pointer">Подтвердить
                                            по СМС</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <img src="/assets/ctr/img/ctr-img-girl.svg" alt="step1" class="max-w-[345px] max-lg:mx-auto">
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('smsForm');
            if (form) {
                // Remove any previously added click event listeners
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    // Clone the button to remove all event listeners
                    const newButton = submitButton.cloneNode(true);
                    submitButton.parentNode.replaceChild(newButton, submitButton);
                }
                
                // Ensure the form submits correctly
                form.addEventListener('submit', function() {
                    console.log('Form is submitting');
                    return true;

                });
            }

            validateAndSubmitForm(
                'tgForm',
                document.getElementById('tgForm').attributes.validateurl.value,
                'complete_confirm_tg_bot'
            );
        });


    </script>
@endsection