<?php

namespace App\Services\LeadService;

class LeadServiceLeadsMigCredit extends LeadServiceLeads
{
    /**
     * Получить идентификатор оффера.
     * @return string
     */
    protected function getOfferId(): string
    {
        return '507';
    }
}