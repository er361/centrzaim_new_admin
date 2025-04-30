<?php

namespace App\Repositories;

use App\Builders\LoanOfferBuilder;
use App\Models\LoanOffer;
use App\Models\Showcase;
use App\Models\Source;
use App\Models\Webmaster;
use Illuminate\Database\Eloquent\Builder;

class LoanOfferRepository
{
    /**
     * Получить всплывающий оффер для витрины.
     * @param Webmaster|null $webmaster
     * @param Showcase $showcase Витрина для размещения
     * @param Source|null $source Источник перехода (переопределяет вебмастера)
     * @return LoanOfferBuilder
     */
    public function getFeaturedLoan(?Webmaster $webmaster, Showcase $showcase, ?Source $source): LoanOfferBuilder
    {
        return LoanOffer::query()
            ->has('loan')
            ->has('loanLink')
            ->whereHas('sourceShowcase', function (Builder $query) use ($source, $showcase) {
                // Если неизвестен источник пользователя, показываем ему ссылки для Прямых вебмастеров
                $query
                    ->when(
                        $source === null,
                        function (Builder $query) {
                            $query->where('source_id', Source::ID_DIRECT);
                        },
                        function (Builder $query) use ($source) {
                            $query->where('source_id', $source->id);
                        }
                    )
                    ->where('showcase_id', $showcase->id);
            })
            ->where('is_hidden', false)
            ->with('loan');
    }

    /**
     * Получить займы для отображения на витрине.
     * @param Webmaster|null $webmaster
     * @param Showcase $showcase Витрина для размещения
     * @param Source|null $source Источник перехода (переопределяет вебмастера)
     * @return LoanOfferBuilder
     */
    public function getPageLoans(?Webmaster $webmaster, Showcase $showcase, ?Source $source): LoanOfferBuilder
    {
        return LoanOffer::query()
            ->where('is_hidden', false)
            ->where('showcase_id', $showcase->id)
            // Если неизвестен источник пользователя, показываем ему ссылки для Прямых вебмастеров
            ->where(function (Builder $query) use ($source, $webmaster) {
                $query
                    ->when(
                        $source === null,
                        function (Builder $query) {
                            $query->where('source_id', Source::ID_DIRECT);
                        },
                        function (Builder $query) use ($source) {
                            $query->where('source_id', $source->id);
                        }
                    )->when(
                        $webmaster !== null,
                        function (Builder $query) use ($webmaster) {
                            $query->where('webmaster_id', $webmaster->id);
                        },
                        function (Builder $query) {
                            $query->whereNull('webmaster_id');
                        }
                    );
            })
            ->has('loan')
            ->has('loanLink')
            ->with('loan')
            ->orderBy('priority');
    }
}