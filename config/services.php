<?php

use App\Models\Source;

return [
    'my_smpp' => [
        'base_url' => env('MY_SMPP_URL'),
    ],

    'impaya' => [
        'env' => env('PAYMENT_MODE', 'test'), // Переключатель между test и prod
        // Общие параметры
        'base_url' => env('IMPAYA_BASE_URL', 'https://api-stage.impaya.ru'),

        // Настройки для теста и продакшн
        'test' => [
            'terminal_name' => env('IMPAYA_TERMINAL_NAME_TEST', 'leads_3ds_test'),
            'recurrent_terminal_name' => env('IMPAYA_RECURRENT_TERMINAL_NAME_TEST', 'leads_non3ds_test'),
            'terminal_password' => env('IMPAYA_TERMINAL_PASSWORD_TEST', 'daxo$no40hvhNZ*2S6*v'),
            'merchant_name' => env('IMPAYA_MERCHANT_NAME_TEST', 'IP_Kuznecova_test'),
            'merchant_password' => env('IMPAYA_MERCHANT_PASSWORD_TEST', 'GFIHrHl0I@zk89z%5OEN'),
            'base_url' => env('IMPAYA_BASE_URL_TEST', 'https://api-stage.impaya.ru'),
        ],

        'prod' => [
            'terminal_name' => env('IMPAYA_TERMINAL_NAME_PROD', 'leads_3ds_prod'),
            'recurrent_terminal_name' => env('IMPAYA_RECURRENT_TERMINAL_NAME_PROD', 'leads_non3ds_prod'),
            'terminal_password' => env('IMPAYA_TERMINAL_PASSWORD_PROD', 'prod_terminal_password'),
            'merchant_name' => env('IMPAYA_MERCHANT_NAME_PROD', 'IP_Kuznecova_prod'),
            'merchant_password' => env('IMPAYA_MERCHANT_PASSWORD_PROD', 'prod_merchant_password'),
            'base_url' => env('IMPAYA_BASE_URL_PROD', 'https://api.impaya.ru'),
        ],

        'commission' => [
            // Комиссия за успешный платеж, в процентах
            'default_successful' => 0.095,

            // Минимальная комиссия за успешный платеж
            'min_successful' => 25,

            // Комиссия за неуспешный платеж
            'default_unsuccessful' => 0.25,
        ],

        'error_codes' => [
            // После которых нужно увеличивать задержку платежа
            'delay' => [
                'ISSUER_LIMIT_FAIL',
                'ISSUER_LIMIT_COUNT_FAIL',
                'ISSUER_LIMIT_AMOUNT_FAIL',
                'NOT_ALLOWED',
            ],
            // После которых надо отключать списания
            'disable' => [
                'ISSUER_FAIL',
                'WRONG_CARD_PAN',
                'WRONG_CARD_EXP',
                'ISSUER_BLOCKED_CARD',
                'ISSUER_CARD_FAIL',
                'WRONG_CARD_PAN',
                'WRONG_CARD_INFO',
                'MERCHANT_RESTRICTION_BY_PS',
            ],
        ],
    ],

    'sources' => [
        [
            'route_key' => 'leads',
            'webmaster_key' => 'wmid',
            'transaction_key' => 'subid',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_LEADS,

            'conversion' => [
                // Поле на нашей стороне -> поле в постбэке от ПП
                'fields' => [
                    'apiStatus' => 'status',
                    'apiConversionId' => 'conversion_id',
                    'apiTransactionId' => 'transaction_id',
                    'apiAdvSubId' => 'adv_sub',
                    'apiCreatedAt' => 'created',
                    'apiPayout' => 'payout',
                    'apiPayoutType' => 'payout_type',
                    'apiUserAgent' => 'user_agent',
                    'apiOfferId' => 'offer_id',
                    'apiAffiliateId' => 'affiliate_id',
                    'apiSource' => 'source',
                    'apiIp' => 'ip',
                    'apiIsTest' => 'is_test',
                    'apiCurrency' => null,
                ],
                'date_format' => null,
                'statuses' => [
                    'approved' => ['approved'],
                    'pending' => ['pending'],
                    'rejected' => ['rejected'],
                ],
                'subs' => [
                    1 => 'aff_sub1',
                    2 => 'aff_sub2',
                    3 => 'aff_sub3',
                    4 => 'aff_sub4',
                ],
                // Префиксы, используемые в первой версии генерации сабов
                'v1_prefixes' => [
                    'user' => 'user:',
                    'webmaster' => 'webmaster:',
                ],
            ],
        ],
        [
            'route_key' => 'guru-leads',
            'webmaster_key' => 'pid',
            'transaction_key' => 'click_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_GURU_LEADS,

            'conversion' => [
                // Поле на нашей стороне -> поле в постбэке от ПП
                'fields' => [
                    'apiStatus' => 'status',
                    'apiConversionId' => 'conversion_id',
                    'apiTransactionId' => 'conversion_id',
                    'apiAdvSubId' => null,
                    'apiCreatedAt' => 'date',
                    'apiPayout' => 'sum',
                    'apiPayoutType' => null,
                    'apiUserAgent' => 'uagent',
                    'apiOfferId' => 'offerid',
                    'apiAffiliateId' => null,
                    'apiSource' => 'source',
                    'apiIp' => 'ip',
                    'apiIsTest' => null,
                    'apiCurrency' => null,
                ],
                'date_format' => null,
                'statuses' => [
                    'approved' => ['1'],
                    'pending' => ['2'],
                    'rejected' => ['3'],
                ],
                'subs' => [
                    1 => 'sub1',
                    2 => 'sub2',
                    3 => 'sub3',
                    4 => 'sub4',
                ],
                // Префиксы, используемые в первой версии генерации сабов
                'v1_prefixes' => [
                    'user' => 'user:',
                    'webmaster' => 'webmaster:',
                ],
            ],
        ],
        [
            'route_key' => 'direct',
            'webmaster_key' => 'webmaster_id',
            'transaction_key' => null,
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_DIRECT,
        ],
        [
            'route_key' => 'leadgid',
            'webmaster_key' => 'affiliate_id',
            'transaction_key' => 'transaction_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_LEAD_GID,

            'conversion' => [
                // Поле на нашей стороне -> поле в постбэке от ПП
                'fields' => [
                    'apiStatus' => 'status',
                    'apiConversionId' => 'lead_id',
                    'apiTransactionId' => 'transaction_id',
                    'apiAdvSubId' => null,
                    'apiCreatedAt' => 'lead_date',
                    'apiPayout' => 'payout',
                    'apiPayoutType' => null,
                    'apiUserAgent' => null,
                    'apiOfferId' => 'offer_id',
                    'apiAffiliateId' => null,
                    'apiSource' => null,
                    'apiIp' => null,
                    'apiIsTest' => null,
                    'apiCurrency' => 'currency',
                ],
                'date_format' => 'Y-m-d\TH:i:s ****',
                'statuses' => [
                    'approved' => ['approved'],
                    'pending' => ['pending'],
                    'rejected' => ['rejected'],
                ],
                'subs' => [
                    1 => 'aff_sub1',
                    2 => 'aff_sub2',
                    3 => 'aff_sub3',
                    4 => 'aff_sub4',
                ],
                // Префиксы, используемые в первой версии генерации сабов
                'v1_prefixes' => [
                    'user' => 'user:',
                    'webmaster' => 'webmaster:',
                ],
            ],
        ],
        [
            'route_key' => 'leadcraft',
            'webmaster_key' => 'campaignID',
            'transaction_key' => 'clickID',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_LEAD_CRAFT,

            'conversion' => [
                // Поле на нашей стороне -> поле в постбэке от ПП
                'fields' => [
                    'apiStatus' => 'status',
                    'apiConversionId' => 'clickID',
                    'apiTransactionId' => 'clickID',
                    'apiAdvSubId' => 'advertiserID',
                    'apiCreatedAt' => null,
                    'apiPayout' => 'price',
                    'apiPayoutType' => null,
                    'apiUserAgent' => null,
                    'apiOfferId' => 'offerID',
                    'apiAffiliateId' => null,
                    'apiSource' => null,
                    'apiIp' => null,
                    'apiIsTest' => null,
                    'apiCurrency' => 'currency',
                ],
                'date_format' => null,
                'statuses' => [
                    'approved' => ['approved'],
                    'pending' => ['pending'],
                    'rejected' => ['cancelled'],
                ],
                'subs' => [
                    1 => 'sub',
                    2 => 'sub2',
                    3 => 'sub3',
                    4 => 'sub4',
                ],
                // Префиксы, используемые в первой версии генерации сабов
                'v1_prefixes' => [
                    'user' => 'user_',
                    'webmaster' => 'webmaster_',
                ],
            ],
        ],
        [
            'route_key' => 'leadbit',
            'webmaster_key' => 'wmid',
            'transaction_key' => 'click_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_LEAD_BIT,
        ],
        [
            'route_key' => 'click2money',
            'webmaster_key' => 'user_id',
            'transaction_key' => 'click_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_CLICK_2_MONEY,

            'conversion' => [
                // Поле на нашей стороне -> поле в постбэке от ПП
                'fields' => [
                    'apiStatus' => 'action_type',
                    'apiConversionId' => 'cid',
                    'apiTransactionId' => 'cid',
                    'apiAdvSubId' => 'lead_id',
                    'apiCreatedAt' => 'date',
                    'apiPayout' => 'payout',
                    'apiPayoutType' => null,
                    'apiUserAgent' => null,
                    'apiOfferId' => 'offer_id',
                    'apiAffiliateId' => null,
                    'apiSource' => 'source',
                    'apiIp' => null,
                    'apiIsTest' => null,
                    'apiCurrency' => 'currency',
                ],
                'date_format' => null,
                'statuses' => [
                    'approved' => [
                        'approved',
                        'secondary',
                        'payout',
                    ],
                    'pending' => ['hold'],
                    'rejected' => ['rejected'],
                ],
                'subs' => [
                    1 => 'subid1',
                    2 => 'subid2',
                    3 => 'subid3',
                    4 => 'subid4',
                ],
                // Префиксы, используемые в первой версии генерации сабов
                'v1_prefixes' => [
                    'user' => 'user:',
                    'webmaster' => 'webmaster:',
                ],
            ],
        ],
        [
            'route_key' => 'leadstech',
            'webmaster_key' => 'web_id',
            'transaction_key' => 'click_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_LEADS_TECH,

            'conversion' => [
                // Поле на нашей стороне -> поле в постбэке от ПП
                'fields' => [
                    'apiStatus' => 'status',
                    'apiConversionId' => 'offerConversionId',
                    'apiTransactionId' => 'clickId',
                    'apiAdvSubId' => null,
                    'apiCreatedAt' => 'date',
                    'apiPayout' => 'sum',
                    'apiPayoutType' => null,
                    'apiUserAgent' => null,
                    'apiOfferId' => null,
                    'apiAffiliateId' => null,
                    'apiSource' => null,
                    'apiIp' => null,
                    'apiIsTest' => null,
                    'apiCurrency' => null,
                ],
                'date_format' => null,
                'statuses' => [
                    'approved' => ['1'],
                    'pending' => ['0'],
                    'rejected' => ['2'],
                ],
                'subs' => [
                    1 => 'sub1',
                    2 => 'sub2',
                    3 => 'sub3',
                    4 => 'sub4',
                ],
                // Префиксы, используемые в первой версии генерации сабов
                'v1_prefixes' => [
                    'user' => 'user:',
                    'webmaster' => 'webmaster:',
                ],
            ],
        ],
        [
            'route_key' => 'affise',
            'webmaster_key' => 'pid',
            'transaction_key' => 'click_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_AFFISE,

            'conversion' => [
                // Поле на нашей стороне -> поле в постбэке от ПП
                'fields' => [
                    'apiStatus' => 'status',
                    'apiConversionId' => 'transactionid',
                    'apiTransactionId' => 'transactionid',
                    'apiAdvSubId' => null,
                    'apiCreatedAt' => 'date',
                    'apiPayout' => 'sum',
                    'apiPayoutType' => null,
                    'apiUserAgent' => 'uagent',
                    'apiOfferId' => 'offerid',
                    'apiAffiliateId' => null,
                    'apiSource' => null,
                    'apiIp' => 'ip',
                    'apiIsTest' => null,
                    'apiCurrency' => 'currency',
                ],
                'date_format' => null,
                'statuses' => [
                    'approved' => ['1'],
                    'pending' => [
                        '2',  // conversion is "Pending"
                        '5', // action is approved and put on "Hold"
                    ],
                    'rejected' => ['3'],
                ],
                'subs' => [
                    1 => 'sub1',
                    2 => 'sub2',
                    3 => 'sub3',
                    4 => 'sub4',
                ],
                // Префиксы, используемые в первой версии генерации сабов
                'v1_prefixes' => [
                    'user' => 'user:',
                    'webmaster' => 'webmaster:',
                ],
            ],
        ],
        [
            'route_key' => 'fincpanetwork',
            'webmaster_key' => 'webmaster_id',
            'transaction_key' => 'click_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_FIN_CPA_NETWORK,
        ],
        [
            'route_key' => 'xpartners',
            'webmaster_key' => 'webmaster_id',
            'transaction_key' => 'click_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_X_PARTNERS,

            'conversion' => [
                // Поле на нашей стороне -> поле в постбэке от ПП
                'fields' => [
                    'apiStatus' => 'status',
                    'apiConversionId' => 'transactionid',
                    'apiTransactionId' => 'transactionid',
                    'apiAdvSubId' => null,
                    'apiCreatedAt' => 'date',
                    'apiPayout' => 'sum',
                    'apiPayoutType' => null,
                    'apiUserAgent' => 'uagent',
                    'apiOfferId' => 'offerid',
                    'apiAffiliateId' => null,
                    'apiSource' => null,
                    'apiIp' => 'ip',
                    'apiIsTest' => null,
                    'apiCurrency' => null,
                ],
                'date_format' => null,
                'statuses' => [
                    'approved' => ['confirmed'],
                    'pending' => [
                        'pending',
                        'hold',
                    ],
                    'rejected' => ['declined'],
                ],
                'subs' => [
                    1 => 'sub1',
                    2 => 'sub2',
                    3 => 'sub3',
                    4 => 'sub4',
                ],
                // Префиксы, используемые в первой версии генерации сабов
                'v1_prefixes' => [
                    'user' => 'user:',
                    'webmaster' => 'webmaster:',
                ],
            ],
        ],
        [
            'route_key' => 'leadtarget',
            'webmaster_key' => 'wmid',
            'transaction_key' => 'clickid',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_LEAD_TARGET,
        ],
        [
            'route_key' => 'finkort',
            'webmaster_key' => 'wmid',
            'transaction_key' => 'clickid',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_FINKORT,
        ],
        [
            'route_key' => 'linkmoney',
            'webmaster_key' => 'webmaster_id',
            'transaction_key' => 'transaction_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_LINK_MONEY,
        ],
        [
            'route_key' => 'alliance',
            'webmaster_key' => 'webmaster_id',
            'transaction_key' => 'click_id',
            'additional_transaction_key' => 'sub_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_ALLIANCE,
        ],
        [
            'route_key' => 'bankiros',
            'webmaster_key' => 'webmaster_id',
            'transaction_key' => 'click_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_BANKIROS,
        ],
        [
            'route_key' => 'sravni',
            'webmaster_key' => 'webmaster_id',
            'transaction_key' => 'click_id',
            'cookie_lifetime' => 31 * 24 * 60 * 60,  // 1 месяц
            'source_id' => Source::ID_SRAVNI,

            'conversion' => [
                // Поле на нашей стороне -> поле в постбэке от ПП
                'fields' => [
                    'apiStatus' => 'status',
                    'apiConversionId' => 'conversion_id',
                    'apiTransactionId' => 'transaction_id',
                    'apiAdvSubId' => null,
                    'apiCreatedAt' => null,
                    'apiPayout' => 'payout',
                    'apiPayoutType' => null,
                    'apiUserAgent' => null,
                    'apiOfferId' => 'offer_id',
                    'apiAffiliateId' => null,
                    'apiSource' => 'source',
                    'apiIp' => null,
                    'apiIsTest' => null,
                    'apiCurrency' => null,
                ],
                'date_format' => null,
                'statuses' => [
                    'approved' => ['Completed', null], // null приходит вместо статуса для офферов с оплатой за клик
                    'pending' => ['Hold',],
                    'rejected' => ['Cancelled', 'Duplicate',],
                ],
                'subs' => [
                    1 => 'aff_sub2',
                    2 => 'aff_sub3',
                    3 => 'aff_sub4',
                    4 => 'aff_sub5',
                ],
                // Префиксы, используемые в первой версии генерации сабов
                'v1_prefixes' => [
                    'user' => 'user:',
                    'webmaster' => 'webmaster:',
                ],
            ],
        ],
    ],

    'leads_tech' => [
        'statuses' => [
            'approved' => 1,
        ]
    ],

    'affise' => [
        'statuses' => [
            'approved' => 1,
        ],
        'goal' => 'ftd',
    ],

    'fin_cpa_network' => [
        'statuses' => [
            'approved' => 1,
        ],
        'goal_id' => '2',
    ],

    'x_partners' => [
        'statuses' => [
            'approved' => 1,
        ],
    ],

    'showcases' => [
        'token' => env('SHOWCASES_TOKEN'),
        'is_extended' => (bool)env('SHOWCASES_IS_EXTENDED', false), // Нужно ли отображать расширенные настройки для витрин
    ],

    'digital_contact' => [
        'country' => 'RU',
    ],

    'finkort' => [
        'statuses' => [
            'approved' => 'approve',
        ],
        'action' => '3',
    ],

    'leadShortener' =>
        [
            'api_url' => env('LEADS_API_URL', 'http://api.leads.su/webmaster/linkShortener/bulkShort'),
            'token' => env('LEADS_API_TOKEN', 'de91b9234bbfd113de2171e70dcd343c'),
        ],
    'goosuShortener' => [
        'url' => 'https://goo.su/api',
        'key' => 'fXj8XzjDaWfmVk4IDeZ662ycdcMrpzsezrZZwG8nwEA9ypmNI1XL7r1iOfGO',
    ],
    'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN'),
    ],
    'banner' => [
        'adsfin' => [
            'token' => env('ADSFIN_TOKEN','1609bcfada185d24e613439f1aaca510242f3c8b'),
        ]
    ]
];
