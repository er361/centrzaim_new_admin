<?php

namespace App\Services\PaymentConditions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Models\User;

class QueueSizeCondition implements PaymentConditionInterface
{
    protected string $queueName;

    public function __construct(string $queueName = 'payments')
    {
        $this->queueName = $queueName;
    }

    public function check(User $user, array $planConfiguration, CarbonImmutable $now, string $iterationUuid): bool
    {
        $queueSize = Queue::size($this->queueName);

        if ($queueSize !== 0) {
            Log::debug("В очереди {$this->queueName} имеются записи, не начинаем списание.", [
                'queue_size' => $queueSize,
            ]);
            return false;
        }

        return true;
    }
}
