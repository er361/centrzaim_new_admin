<?php

namespace App\Services\BannerService;

use App\Constants\ChannelConstants;
use App\Models\Banner;
use App\Models\Source;
use App\Models\Webmaster;
use Illuminate\Support\Facades\DB;

class ChannelService
{
    /**
     * Получает список всех каналов с использованием таблицы связей banner_webmaster
     *
     * @return array
     */
    public static function getAllChannels(): array
    {
        $channels = [];

        // 1. Каналы с вебмастерами - берем напрямую из таблицы banner_webmaster и связанных таблиц
        $webmasterChannels = DB::table('banner_webmaster as bw')
            ->join('webmasters as w', 'bw.webmaster_id', '=', 'w.id')
            ->join('sources as s', 'w.source_id', '=', 's.id')
            ->select('bw.banner_id', 'bw.webmaster_id', 's.id as source_id')
            ->get();

        foreach ($webmasterChannels as $relation) {
            $channel = sprintf(
                ChannelConstants::CHANNEL_TEMPLATE,
                $relation->banner_id,
                $relation->source_id,
                $relation->webmaster_id
            );

            $channels[] = $channel;
        }

        // 2. Каналы без вебмастеров - берем напрямую из таблицы banner_source
        $sourceChannels = DB::table('banner_source')
            ->select('banner_id', 'source_id')
            ->get();

        foreach ($sourceChannels as $relation) {
            $channel = sprintf(
                ChannelConstants::CHANNEL_TEMPLATE_NO_WEBMASTER,
                $relation->banner_id,
                $relation->source_id
            );

            $channels[] = $channel;
        }

        return $channels;
    }
}