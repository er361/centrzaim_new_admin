<?php

namespace App\Http\Actions;

use App\Models\Conversion;
use App\Models\Source;
use App\Services\LinkService\LinkServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessPostbackAction
{
    /**
     * @var LinkServiceFactory
     */
    protected LinkServiceFactory $linkServiceFactory;

    /**
     * @param LinkServiceFactory $linkServiceFactory
     */
    public function __construct(LinkServiceFactory $linkServiceFactory)
    {
        $this->linkServiceFactory = $linkServiceFactory;
    }

    /**
     * @param Request $request
     * @param Source $source
     * @return void
     */
    public function handle(Request $request, Source $source): void
    {
        $requestData = $request->all();
        $linkParsingService = $this->linkServiceFactory->getParsingInstance($source, $requestData);

        try {
            $conversionData = $linkParsingService->getConversionEntity($requestData);
        } catch (\Exception $e) {
            Log::error('Ошибка при обработке конверсии', [
                'request' => $request->all(),
                'message' => $e->getMessage(),
                'source_id' => $source->id,
            ]);
            report($e);
            return;
        }


        if ($conversionData === null) {
            return;
        }

        $conversionDataArray = $conversionData->toArray();
        $convertedConversionData = [];

        foreach ($conversionDataArray as $key => $value) {
            $convertedConversionData[Str::snake($key)] = $value;
        }

        $convertedConversionData = array_filter($convertedConversionData);
        $convertedConversionData['source_id'] = $source->id;

        Conversion::query()->updateOrCreate([
            'api_conversion_id' => $conversionData->apiConversionId,
        ], $convertedConversionData);
    }
}