<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LoanOfferResourceCollection extends AnonymousResourceCollection
{
    protected array $urlParameters = [];

    public function setUrlParameters(array $urlParameters): static
    {
        $this->urlParameters = $urlParameters;
        return $this;
    }

    public function toArray($request)
    {
        return $this->collection->map(function (LoanOfferResource $resource) use ($request) {
            return $resource->setUrlParameters($this->urlParameters)->toArray($request);
        })->all();
    }
}