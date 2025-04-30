<?php

namespace App\Services\LinkService;

use App\Models\Source;
use App\Services\LinkService\Contracts\LinkCreatorServiceContract;
use App\Services\LinkService\Contracts\LinkParsingServiceContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @todo Возможно, маппинг стоит вынести в конфигурацию приложения
 */
class LinkServiceFactory
{
    /**
     * Получить сервис для управления ссылками по типу ссылки.
     * @param Source $source
     * @return LinkCreatorServiceContract
     */
    public function getCreatorInstance(Source $source): LinkCreatorServiceContract
    {
        $configurations = collect(config('services.sources'))
            ->keyBy('source_id');

        if (!$configurations->has($source->id)) {
            return new NullLinkService();
        }

        $sourceConversionConfiguration = Arr::get($configurations->get($source->id), 'conversion');

        if ($sourceConversionConfiguration === null) {
            // Попытка сгенерировать ссылку для ПП, для которой мы не поддерживаем размещение ссылок на витрине
            return new NullLinkService();
        }

        return new LinkCreatorService($sourceConversionConfiguration);
    }

    /**
     * Получить сервис для парсинга постбэка по его типу.
     * @param Source $source Источник
     * @param array $request Данные запроса
     * @return LinkParsingServiceContract
     */
    public function getParsingInstance(Source $source, array $request): LinkParsingServiceContract
    {
        $configurations = collect(config('services.sources'))
            ->keyBy('source_id');

        if (!$configurations->has($source->id)) {
            Log::error('Получена конверсия с неподдерживаемым source_id.', [
                'source_id' => $source->id,
            ]);
            return new NullLinkService();
        }

        $sourceConversionConfiguration = Arr::get($configurations->get($source->id), 'conversion');

        if ($sourceConversionConfiguration === null) {
            Log::error('Получена конверсия с source_id без конфигурации.', [
                'source_id' => $source->id,
            ]);
            return new NullLinkService();
        }

        $sub1FieldName = $sourceConversionConfiguration['subs'][1];
        $requestSub1 = Arr::get($request, $sub1FieldName);

        if (!Str::startsWith($requestSub1, LinkCreatorService::V2_SUB1_PREFIX)) {
            Log::debug('Получена конверсия с неподдерживаемой версией.', [
                'source_id' => $source->id,
                'sub1' => $requestSub1,
            ]);
            return new NullLinkService();
        }

        return new LinkParsingServiceV2($sourceConversionConfiguration);
    }
}