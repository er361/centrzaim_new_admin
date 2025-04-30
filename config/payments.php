<?php

$month = 60 * 24 * 30;
$week = 60 * 24 * 7;
$fiveMinutes = 5;

$monthAmount = 1196;
$weekAmount = 299;

$firstPaymentDelay = 30;


return [
    'monthly_weekly' => [
        /*
  |--------------------------------------------------------------------------
  | Периодичность платежей
  |--------------------------------------------------------------------------
  |
  | Здесь можно задать разные типы платежей с их интервалами и условиями
  | для повторных попыток, если платеж не удалось провести.
  |
  */

        'monthly' => [
            'interval_in_minutes' => env('PAYMENT_INTERVAL_MONTHLY', $month),
            'amount' => env('PAYMENT_AMOUNT_MONTHLY', $monthAmount),
        ],
        'weekly' => [
            'interval_in_minutes' => env('PAYMENT_INTERVAL_WEEKLY', $week),
            'amount' => env('PAYMENT_AMOUNT_WEEKLY', $weekAmount),
        ],
        'retry' => [
            'interval_in_minutes' => env('PAYMENT_INTERVAL_RETRY', $fiveMinutes),
            'amount' => env('PAYMENT_AMOUNT_RETRY', $weekAmount),
        ],

        /*
        |--------------------------------------------------------------------------
        | Общие настройки интервалов
        |--------------------------------------------------------------------------
        |
        | Здесь можно указать дополнительные настройки, такие как время начала
        | или другие параметры, которые могут быть применены к платежам.
        |
        */
        'common_props' => [
            'timezone' => 'UTC', // Временная зона по умолчанию
            'recurrent_payments_per_hour' => 1000, // Количество платежей в час
            'first_payment_delay' => $firstPaymentDelay, // Задержка перед первым платежом
        ],
    ],

    'scenario2' =>[
        /*
        |--------------------------------------------------------------------------
        | Scenario 2 Payment Configuration
        |--------------------------------------------------------------------------
        |
        | Конфигурация для сценария списания 299 рублей
        |
        */

        // Сумма платежа
        'amount' => 299,

        // Первый платеж через 10 минут после регистрации
        'initial_delay_minutes' => 10,

        // Повторная попытка через 120 минут после неудачного платежа
        'retry_interval_minutes' => 120,

        // Регулярные платежи через 7 дней после успешного
        'recurring_interval_days' => 7,
    ]



];
