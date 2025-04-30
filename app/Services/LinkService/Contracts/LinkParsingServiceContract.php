<?php

namespace App\Services\LinkService\Contracts;

use App\Services\LinkService\Entities\ConversionEntity;

interface LinkParsingServiceContract
{
    /**
     * Получить сущность конверсии.
     *
     * @param array $request
     * @return ConversionEntity|null
     */
    public function getConversionEntity(array $request): ?ConversionEntity;

}