<?php

namespace App\Enums;

use App\Enums\Traits\EnumValues;

enum SmsTypeEnum: int
{
    use EnumValues;

    /**
     * По умолчанию.
     */
    case Default = 1;

    /**
     * Пользователи без привязки карты.
     */
    case NoCard = 2;

    /**
     * После клика в предыдущей SMS, "по касанию".
     */
    case AfterClick = 3;

    /**
     * @return string[] Массив названий типов.
     */
    public static function getLabels(): array
    {
        return [
            self::Default->value => 'По умолчанию',
            self::NoCard->value => 'Пользователи без привязки карты',
            self::AfterClick->value => 'После клика в предыдущей SMS, "по касанию"',
        ];
    }

    /**
     * @return string Текстовое описание типа сообщения.
     */
    public function getLabel(): string
    {
        return self::getLabels()[$this->value];
    }
}
