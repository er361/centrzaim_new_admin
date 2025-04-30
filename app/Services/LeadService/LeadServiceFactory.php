<?php

namespace App\Services\LeadService;

use App\Models\LeadService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;

class LeadServiceFactory
{
    /**
     * Получить сервис для отправки.
     * @param LeadService $leadService
     * @return LeadServiceContract
     */
    public function getInstance(LeadService $leadService): LeadServiceContract
    {
        $mappings = [
            LeadService::ID_Q_ZAEM => LeadServiceQZaem::class,
            LeadService::ID_LEADS_TECH => LeadServiceLeadsTech::class,
            LeadService::ID_LEADS_MIG_CREDIT => LeadServiceLeadsMigCredit::class,
            LeadService::ID_DIGITAL_CONTACT => LeadServiceDigitalContact::class,
        ];

        if (!isset($mappings[$leadService->id])) {
            throw new InvalidArgumentException('LeadService с идентификатором ' . $leadService->id . ' не поддерживает отправку.');
        }

        $class = Arr::get($mappings, $leadService->id);
        return App::make($class);
    }
}