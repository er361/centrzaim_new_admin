<?php


namespace App\Services\PostbackService;


use App\Models\Source;
use App\Services\PostbackService\Providers\AdsFinPostbackNotifyService;
use App\Services\PostbackService\Providers\AffisePostbackNotifyService;
use App\Services\PostbackService\Providers\AlliancePostbackNotifyService;
use App\Services\PostbackService\Providers\BankirosPostbackNotifyService;
use App\Services\PostbackService\Providers\Click2MoneyPostbackNotifyService;
use App\Services\PostbackService\Providers\DirectPostbackNotifyService;
use App\Services\PostbackService\Providers\FinCPANetworkPostbackNotifyService;
use App\Services\PostbackService\Providers\FinkortPostbackNotifyService;
use App\Services\PostbackService\Providers\GuruLeadsPostbackNotifyService;
use App\Services\PostbackService\Providers\LeadBitPostbackNotifyService;
use App\Services\PostbackService\Providers\LeadCraftPostbackNotifyService;
use App\Services\PostbackService\Providers\LeadGidPostbackNotifyService;
use App\Services\PostbackService\Providers\LeadsPostbackNotifyService;
use App\Services\PostbackService\Providers\LeadsTechPostbackNotifyService;
use App\Services\PostbackService\Providers\LeadTargetPostbackNotifyService;
use App\Services\PostbackService\Providers\LinkMoneyPostbackNotifyService;
use App\Services\PostbackService\Providers\XPartnersPostbackNotifyService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class PostbackServiceFactory
{
    /**
     * @param int $sourceId
     * @return PostbackService|null
     */
    public function createPostbackService(int $sourceId): ?PostbackService
    {
        $mapping = [
            Source::ID_LEADS => LeadsPostbackNotifyService::class,
            Source::ID_GURU_LEADS => GuruLeadsPostbackNotifyService::class,
            Source::ID_DIRECT => DirectPostbackNotifyService::class,
            Source::ID_LEAD_GID => LeadGidPostbackNotifyService::class,
            Source::ID_LEAD_CRAFT => LeadCraftPostbackNotifyService::class,
            Source::ID_LEAD_BIT => LeadBitPostbackNotifyService::class,
            Source::ID_CLICK_2_MONEY => Click2MoneyPostbackNotifyService::class,
            Source::ID_LEADS_TECH => LeadsTechPostbackNotifyService::class,
            Source::ID_AFFISE => AffisePostbackNotifyService::class,
            Source::ID_FIN_CPA_NETWORK => FinCPANetworkPostbackNotifyService::class,
            Source::ID_X_PARTNERS => XPartnersPostbackNotifyService::class,
            Source::ID_LEAD_TARGET => LeadTargetPostbackNotifyService::class,
            Source::ID_FINKORT => FinkortPostbackNotifyService::class,
            Source::ID_LINK_MONEY => LinkMoneyPostbackNotifyService::class,
            Source::ID_ALLIANCE => AlliancePostbackNotifyService::class,
            Source::ID_BANKIROS => BankirosPostbackNotifyService::class,
            Source::ID_ADSFIN => AdsfinPostbackNotifyService::class,
        ];

        if (!isset($mapping[$sourceId])) {
            return null;
        }

        $notifyServiceClass = Arr::get($mapping, $sourceId);

        /** @var PostbackNotifyServiceInterface $notifyService */
        $notifyService = App::make($notifyServiceClass);

        /** @var PostbackCostService $costService */
        $costService = App::make(PostbackCostService::class);

        return new PostbackService($notifyService, $costService);
    }
}