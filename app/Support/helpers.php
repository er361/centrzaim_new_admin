<?php

if (!function_exists('format_phone')) {
    function format_phone($value): string
    {
        // Убираем все символы, кроме цифр
        $value = preg_replace('/\D+/', '', $value);

        // Проверяем, что номер содержит 11 цифр
        if (strlen($value) === 11) {
            // Форматируем номер в нужный вид
            $formated = preg_replace('/^(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})$/', '+$1 ($2) $3-$4-$5', $value);
            return $formated;
        }

        // Если номер имеет неправильное количество цифр, возвращаем оригинальное значение
        return $value;
    }
}