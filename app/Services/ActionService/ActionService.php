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
     * @param null|string $siteId
     * @param null|string $placeId
     * @param null|string $bannerId
     * @param null|string $campaignId
     * @return Action
     */
    public function registerAction(
        int $sourceId, 
        string $webmasterId, 
        string $ip, 
        ?string $userAgent, 
        ?string $transactionId,
        ?string $siteId = null,
        ?string $placeId = null,
        ?string $bannerId = null,
        ?string $campaignId = null
    ): Action
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
            'site_id' => $siteId,
            'place_id' => $placeId,
            'banner_id' => $bannerId,
            'campaign_id' => $campaignId,
        ]);

        return $action;
    }
}