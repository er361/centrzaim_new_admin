import BaseView from "./base.js";
import {Range} from "../components/range.js";
import Cleave from 'cleave.js';
import 'suggestions-jquery'

export default class extends BaseView {
    constructor() {
        super();

        // require('suggestions-jquery');

        Range.init();

        this.initInputMasks();
        this.initNameHints(frontConfig.dadataToken, $('#last_name'), $('#first_name'), $('#middlename'));
        this.setTermsAgree();
        this.initAddressHints(frontConfig.dadataToken);

        if (frontConfig.shouldAG) {
            // this.setAutoAgreement();
        }
    }

    initInputMasks() {
        const $mphone = $('#mphone');

        if ($mphone.length) {
            let mphone = new Cleave('#mphone', {
                numericOnly: true,
                blocks: [2, 0, 3, 0, 3, 2, 2],
                delimiters: [' ', '(', ')', ' ', '-'],
                prefix: '+7',
                noImmediatePrefix: true
            });

            const val = $mphone.attr('value');

            if (val) {
                mphone.setRawValue(val);
            }
        }

        if ($('#birthdate').length) {
            new Cleave('#birthdate', {
                date: true,
                datePattern: ['d', 'm', 'Y'],
                delimiter: '.'
            });
        }

        if ($('#passport_date').length) {
            new Cleave('#passport_date', {
                date: true,
                datePattern: ['d', 'm', 'Y'],
                delimiter: '.'
            });
        }

        if ($('#passport_title').length) {
            new Cleave('#passport_title', {
                blocks: [2, 2, 6],
            });
        }

        if ($('#passport_code').length) {
            new Cleave('#passport_code', {
                delimiter: '-',
                blocks: [3, 3],
            });
        }
    }

    /**
     *  Инициализирует подсказки по ФИО на указанном элементе
     * @param token
     * @param $surname
     * @param $name
     * @param $patronymic
     */
    initNameHints(token, $surname, $name, $patronymic) {
        let _this = this;
        let self = {};
        self.$surname = $surname;
        self.$name = $name;
        self.$patronymic = $patronymic;
        const fioParts = ["SURNAME", "NAME", "PATRONYMIC"];
        $.each([$surname, $name, $patronymic], function (index, $el) {
            $el.suggestions({
                token: token,
                type: "NAME",
                triggerSelectOnSpace: false,
                hint: "",
                noCache: true,
                params: {
                    // каждому полю --- соответствующая подсказка
                    parts: [fioParts[index]]
                },
                onSearchStart: function (params) {
                    // если пол известен на основании других полей,
                    // используем его
                    var $el = $(this);
                    params.gender = _this.isGenderKnown.call(self, $el) ? self.gender : "UNKNOWN";
                },
                onSelect: function (suggestion) {
                    // определяем пол по выбранной подсказке
                    self.gender = suggestion.data.gender;
                    $el.trigger('keyup');
                }
            });
        });
    }

    isGenderKnown($el) {
        const self = this;
        const surname = this.$surname.val(),
            name = this.$name.val(),
            patronymic = self.$patronymic.val();
        if (($el.attr('id') == self.$surname.attr('id') && !name && !patronymic) ||
            ($el.attr('id') == self.$name.attr('id') && !surname && !patronymic) ||
            ($el.attr('id') == self.$patronymic.attr('id') && !surname && !name)) {
            return false;
        } else {
            return true;
        }
    }

    setAutoAgreement() {
        const sumValues = obj => Object.values(obj).reduce((a, b) => a + b);

        const agreementCheckboxObject = $('input[name="terms_agree"]');

        if (!agreementCheckboxObject.length) {
            return;
        }

        let fieldTotalLength = 0;
        const symbolsToCheck = 3;
        const authFields = {
            first_name: 0,
            last_name: 0,
            middlename: 0,
            mphone: 0,
        };

        Object.keys(authFields).forEach((field) => {
            const $fieldObject = $('#' + field);

            if (!$fieldObject.length) {
                return;
            }

            $fieldObject.on('keyup change keypress', function (e) {
                authFields[field] = $fieldObject.val().length;
                fieldTotalLength = sumValues(authFields);

                const shouldCheck = fieldTotalLength >= symbolsToCheck;
                agreementCheckboxObject.prop("checked", shouldCheck);
            });
        })
    }

    setTermsAgree() {
        let hasScrolled = false;

        $('#additional_terms_agree_checkbox').on('change', function (e) {
            // If already checked
            if (!$(this).is(':checked')) {
                return;
            }

            if (!hasScrolled) {
                $(this).prop("checked", false)
                toastr["error"]("Пожалуйста, полностью прочитайте условия соглашения.");
            }
        });

        const agreement = document.getElementById('register_agreement');
        $('#register_agreement').on('scroll', function (e) {
            if (agreement.scrollHeight - agreement.scrollTop <= agreement.clientHeight) {
                hasScrolled = true;
            }
        })
    }

    initAddressHints(token) {
        console.log('token', token);
        const type = 'ADDRESS';

        const $city = $("#reg_city_name");
        const $street = $("#reg_street");
        const $house = $("#reg_house");
        const $cityFact = $("#fact_city_name");

        // Город и населенный пункт
        $city.suggestions({
            token: token,
            type: type,
            bounds: "city-settlement"
        });

        $cityFact.suggestions({
            token: token,
            type: type,
            bounds: "city-settlement"
        });

        // Улица
        $street.suggestions({
            token: token,
            type: type,
            bounds: "street",
            constraints: $city,
            count: 15
        });

        // Дом
        $house.suggestions({
            token: token,
            type: type,
            bounds: "house",
            constraints: $street
        });
    }
}