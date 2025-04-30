<?php

namespace App\Services\LoanService\Entities;

use App\Models\Loan;

class LoanEntity
{
    /**
     * @var string
     */
    public string $link;

    /**
     * @var Loan
     */
    public Loan $loan;

    /**
     * @param string $link
     * @param Loan $loan
     */
    public function __construct(string $link, Loan $loan)
    {
        $this->link = $link;
        $this->loan = $loan;
    }
}