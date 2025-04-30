<?php

namespace App\Services\OffersChecker;

class ReportProcessor
{
    public function extractOffers(array $report): array
    {
        return collect($report['data'])
            ->transform(function ($item) {
                return [
                    'is_repeat' => $item['is_repeat'],
                    'offers' => $item['offers'],
                ];
            })
            ->filter(function ($item) {
                return $item['is_repeat'] === 1;
            })
            ->pluck('offers')
            ->flatten()
            ->unique()
            ->toArray();
    }
}