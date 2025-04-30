<?php

namespace App\Services\BannerService;

use App\Constants\ChannelConstants;
use App\Models\Banner;
use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class BannerService
{
    /** @var string */
    protected const REPLACEABLE_VALUE = '{value}';

    /** @var string */
    protected const DEFAULT_PARAM_NAME = 'subid1';

    /**
     * Возвращает имя параметра по умолчанию
     *
     * @return string
     */
    public static function getDefaultParamName(): string
    {
        return self::DEFAULT_PARAM_NAME;
    }

    /**
     * @param string $position
     * @return string
     */
    public static function get(string $position): string
    {
        /** @var User $user */
        $user = Auth::user();
        $webmasterId = $user?->webmaster_id ?? Cookie::get('webmaster_id');
        if ($webmasterId === null) {
            $webmaster = null;
            $source = Source::query()->find(Source::ID_DIRECT);
        } else {
            $webmaster = Webmaster::query()->find($webmasterId);
            $source = $webmaster?->source ?? Source::query()->find(Source::ID_DIRECT);
        }
        $banners = Banner::query()
            ->where('position', $position)
            ->whereShouldBeVisibleFor($source, $webmaster)
            ->get();
        $bannerCodes = $banners
            ->map(function (Banner $banner) use ($webmaster, $source) {
                if ($webmaster === null) {
                    $channel = sprintf(ChannelConstants::CHANNEL_TEMPLATE_NO_WEBMASTER, $banner->id, $source->id);
                } else {
                    $channel = sprintf(ChannelConstants::CHANNEL_TEMPLATE, $banner->id, $source->id, $webmaster->id);
                }
                $code = $banner->code;

                // Заменяем {value} на subid1={канал}
                $code = Str::replace(self::REPLACEABLE_VALUE, self::DEFAULT_PARAM_NAME . '=' . $channel, $code);

                return $code;
            });
        $join = $bannerCodes->join(PHP_EOL);
        return $join;
    }
}