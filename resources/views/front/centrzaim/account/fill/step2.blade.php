@extends('layouts.app')
@section('content')
    <div class="bg-gray-bg">
        @include('blocks.components.progress', ['width' => 'w-8/12'])
        <div class="container  gap-8 flex  flex-col py-10">
            <div class="sm:text-3xl text-2xl  font-semibold flex flex-col gap-2">
                <span>До подачи заявки <span class="text-red">остался 1 шаг</span></span>
                <span class="sm:text-lg text-base">Заполните адрес регистрации</span>
            </div>
            <div class="flex lg:flex-row flex-col gap-8 justify-between">
                <form
                        method="POST"
                        action="{{ route('account.fill.store') }}"
                        class="basis-8/12"
                        id="addressForm"
                        validateurl="{{ route('account.fill.validate') }}"
                >
                    <input type="hidden" name="fill_step" value="2">
                    <x-form-errors :errors="$errors"/>
                    <div class="grid grid-rows-2 md:grid-cols-2 gap-4">
                        @csrf
                        <div><input required value="{{old('reg_city_name')}}" name="reg_city_name" placeholder="Город*" id="reg_city_name"
                                    class="p-3 w-full rounded"></div>
                        <div><input required value="{{old('reg_street')}}" name="reg_street" placeholder="Улица*" id="reg_street"
                                    class="p-3 w-full rounded"></div>
                        <div><input required value="{{old('reg_house')}}" name="reg_house" placeholder="Номер дома*" id="reg_house"
                                    class="p-3 w-full rounded"></div>
                        <div><input  value="{{old('reg_flat')}}" name="reg_flat" placeholder="Номер квартиры"
                                    type="number"
                                    class="p-3 w-full rounded"></div>
                    </div>
                    <div class="sm:text-3xl text-2xl font-semibold mb-5 my-5">
                        Место проживания
                    </div>
                    <div class="flex sm:flex-row flex-col gap-4">
                        <div class="w-full"><input required value="{{old('fact_city_name')}}" id="fact_city_name"
                                                   name="fact_city_name" placeholder="Город проживания*"
                                                    class="p-3 w-full rounded"></div>
                        <button id="submitFioForm" type="submit" class="hidden"></button>
                    </div>

                </form>
                <img src="/assets/miazaim/imgs/flower-girl.svg" alt="step1" class="max-w-[306px] hidden lg:block">
            </div>

            <div class="get-money-wrapper flex flex-row justify-start w-full">
                @include('blocks.components.get-money-btn', ['btnText' => 'Продолжить'])
            </div>
            <div class="flex flex-row items-start font-bold sm:text-base text-sm opacity-60 max-w-[856px]">
                Внимание! Вносите только достоверную информацию, так как ложные данные могут стать причиной для
                отказа в получении средств.
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.getElementsByClassName('money-btn')[0].addEventListener('click', function () {
            document.getElementById('submitFioForm').click()
        })

        document.addEventListener('DOMContentLoaded', function () {
            validateAndSubmitForm(
                'addressForm',
                document.getElementById('addressForm').attributes.validateurl.value,
                'complete_my_fill_address'
            );
        })

    </script>
@endsection
