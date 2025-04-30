@extends('layouts.app')
@section('content')
    <div class="bg-gray-bg">
        @include('blocks.components.progress', ['width' => 'w-8/12'])
        <div class="container  gap-8 flex flex-col py-10">
            <div class="sm:text-3xl text-2xl font-semibold flex flex-col gap-2">
                <span>До подачи заявки <span class="text-red">осталось 2 шага</span></span>
                <span class="sm:text-lg text-base">Заполните паспортные данные</span>
            </div>
            <div class="flex lg:flex-row flex-col gap-4 justify-between">
                <div class="basis-8/12 flex flex-col gap-8">
                    <x-form-errors :errors="$errors"/>
                    <form method="POST"
                          action="{{route('account.fill.store')}}"
                          id="passportForm"
                          validateUrl="{{route('account.fill.validate')}}"
                    >
                        @csrf
                        <input type="hidden" name="fill_step" value="1">
                        <input type="hidden" value="" name="phone" id="stored_phone">

                        <div class="grid grid-rows-2 md:grid-cols-2 gap-4">
                            <div><input id="passportNumber" required name="passport_title" placeholder="Серия и номер" class="p-3 w-full rounded"></div>
                            <div><input id="givenDate" required name="passport_date" placeholder="Дата выдачи" class="p-3 w-full rounded"></div>
                            <div><input id="code" required name="passport_code"  placeholder="Код подразделения" class="p-3 w-full rounded"></div>
                        </div>

                        <div class="get-money-wrapper flex flex-row justify-start w-full mt-4">
                            @include('blocks.components.get-money-btn',['btnText' => 'Продолжить'])
                        </div>
                        <button id="submitPassportForm" type="submit" class="hidden"></button>
                    </form>
                    <p class="text-base opacity-60">
                        Внимание! Вносите только достоверную информацию, так как ложные данные могут стать причиной для отказа в получении средств.
                    </p>
                </div>
                <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px]">
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // Маска для серии и номера паспорта РФ (формат: 0000 000000)
        var passportMask = IMask(
            document.getElementById('passportNumber'), {
                mask: '00-00-000000'
            });

        // Маска для даты выдачи (формат: ДД.ММ.ГГГГ)
        var dateMask = IMask(
            document.getElementById('givenDate'), {
                mask: Date,
                pattern: 'd.`m.`Y',
                blocks: {
                    d: {
                        mask: IMask.MaskedRange,
                        from: 1,
                        to: 31,
                        maxLength: 2
                    },
                    m: {
                        mask: IMask.MaskedRange,
                        from: 1,
                        to: 12,
                        maxLength: 2
                    },
                    Y: {
                        mask: IMask.MaskedRange,
                        from: 1900,
                        to: 2099,
                        maxLength: 4
                    }
                },
                format: function (date) {
                    var day = date.getDate();
                    var month = date.getMonth() + 1;
                    var year = date.getFullYear();
                    return [
                        day > 9 ? day : '0' + day,
                        month > 9 ? month : '0' + month,
                        year
                    ].join('.');
                },
                parse: function (str) {
                    var dayMonthYear = str.split('.');
                    return new Date(dayMonthYear[2], dayMonthYear[1] - 1, dayMonthYear[0]);
                }
            });

        // Маска для кода подразделения (формат: 000-000)
        var codeMask = IMask(
            document.getElementById('code'), {
                mask: '000-000'
            });

        document.addEventListener('DOMContentLoaded', function () {
            const storedPhone = localStorage.getItem('phone');
            if (storedPhone) {
                document.getElementById('stored_phone').value = storedPhone;
            }

            validateAndSubmitForm(
                'passportForm',
                document.getElementById('passportForm').attributes.validateurl.value,
                'complete_my_fill_passport'
            );
        });



    </script>
@endsection
