<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read User $resource
 */
class UserResource extends JsonResource
{
    /**
     * @param User $resource
     */
    public function __construct(User $resource)
    {
        parent::__construct($resource);
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
            'email' => $this->resource->email,
            'mphone' => $this->resource->mphone,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'middlename' => $this->resource->middlename,

        ];
    }
}