<?php

namespace App\Http\Resources;

use App\Services\LoanService\Entities\SourceShowcaseLoansEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read SourceShowcaseLoansEntity $resource
 */
class SourceShowcaseLoansEntityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'loan_offers' => [
                'featured' => (new LoanOfferResource($this->resource->featuredLoan))
                    ->setUrlParameters($this->resource->urlParameters),
                'default' => LoanOfferResource::collection($this->resource->loanOffers)
                    ->setUrlParameters($this->resource->urlParameters)
            ],
        ];
    }
}