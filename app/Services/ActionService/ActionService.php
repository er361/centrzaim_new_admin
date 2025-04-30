<?php


namespace App\Services\ActionService;


use App\Events\WebmasterRegistered;
use App\Models\Action;
use App\Models\Webmaster;

class ActionService
{
    /**
     * Зарегистрировать действие по ссылке вебмастера.
     * @param int $sourceId
     * @param string $webmasterId
     * @param string $ip
     * @param null|string $userAgent
     * @param null|string $transactionId
     * @return Action
     */
    public function registerAction(int $sourceId, string $webmasterId, string $ip, ?string $userAgent, ?string $transactionId): Action
    {
        /** @var Webmaster $webmaster */
        $webmaster = Webmaster::query()
            ->firstOrNew([
                'source_id' => $sourceId,
                'api_id' => $webmasterId,
            ]);

        if (!$webmaster->exists) {
            $webmaster->save();
            event(new WebmasterRegistered($webmaster)); // Вызываем только если новый
        }

        /** @var Action $action */
        $action = $webmaster->actions()->create([
            'ip' => $ip,
            'user_agent' => $userAgent,
            'api_transaction_id' => $transactionId,
        ]);

        return $action;
    }
}