<?php

namespace App\Http\Resources;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Loan $resource
 */
class LoanResource extends JsonResource
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
            'image_url' => $this->resource->image_url,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'user_rating' => $this->resource->user_rating,
            'amount' => $this->resource->amount,
            'issuing_time' => $this->resource->issuing_time,
            'issuing_period' => $this->resource->issuing_period,
            'issuing_bid' => $this->resource->issuing_bid,
        ];
    }
}