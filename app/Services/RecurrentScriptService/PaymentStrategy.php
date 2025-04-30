<?php

namespace App\Services\RecurrentScriptService;



use Illuminate\Support\Facades\Log;
use App\Models\User;

abstract class PaymentStrategy
{
    protected string $name;
    protected \Psr\Log\LoggerInterface $logger;
    protected int $chunk_size = 100;

    public function __construct(int $chunk_size = 100)
    {
        $this->name = $this->defineName();
        $this->logger = Log::channel('payments');
        $this->chunk_size = $chunk_size;
    }

    abstract protected function defineName(): string;

    public function getName(): string
    {
        return $this->name;
    }
    
    public function getChunkSize(): int
    {
        return $this->chunk_size;
    }
    
    public function setChunkSize(int $chunk_size): self
    {
        $this->chunk_size = $chunk_size;
        return $this;
    }

    public function charge(User $user)
    {
        $this->logToStartCharge($user);
        $result = $this->processCharge($user);
        $this->logToEndCharge($user, $result);
        return $result;
    }

    abstract protected function processCharge(User $user);

    protected function logToStartCharge(User $user)
    {
        $this->logger->info("Начало списания для пользователя {$user->id} ({$this->getName()})");
    }

    protected function logToEndCharge(User $user, $result)
    {
        $this->logger->info("Конец списания для пользователя {$user->id} ({$this->getName()}). Результат: {$result}");
    }


}
