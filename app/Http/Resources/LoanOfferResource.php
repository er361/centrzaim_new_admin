<?php

namespace App\Http\Resources;

use App\Models\LoanOffer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read LoanOffer $resource
 */
class LoanOfferResource extends JsonResource
{
    protected array $urlParameters = [];

    public function setUrlParameters(array $urlParameters): static
    {
        $this->urlParameters = $urlParameters;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'link' => $this->resource->getShowLink($this->urlParameters),
            'loan' => new LoanResource($this->resource->loan),
        ];
    }

    /**
     * @param $resource
     * @return LoanOfferResourceCollection
     */
    public static function collection($resource): LoanOfferResourceCollection
    {
        return new LoanOfferResourceCollection($resource, static::class);
    }
}