<?php

namespace App\Services;

use App\Actions\GenerateUtmFromTemplate;

class RedirectUrlService
{
    public function __construct(GenerateUtmFromTemplate $action)
    {
        $this->generateUtmAction = $action;
    }

    public function generateUrl(string $initialLink)
    {
        return $this->generateUtmAction->run($initialLink);
    }
}
