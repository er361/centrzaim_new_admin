<?php

namespace App\Constants;

/**
 * Константы для работы с каналами баннеров
 */
class ChannelConstants
{
    /** @var string Шаблон канала с вебмастером */
    public const CHANNEL_TEMPLATE = 'banner_%d_source_%d_webmaster_%d';

    /** @var string Шаблон канала без вебмастера */
    public const CHANNEL_TEMPLATE_NO_WEBMASTER = 'banner_%d_source_%d';
}
