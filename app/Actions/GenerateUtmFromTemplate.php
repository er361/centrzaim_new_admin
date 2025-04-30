<?php

namespace App\Actions;

use App\Models\Source;
use App\Models\Webmaster;
use App\Services\LinkService\LinkServiceFactory;
use App\Services\SettingsService\Enums\FrontendSettingsEnum;
use App\Services\SettingsService\SettingsService;

class GenerateUtmFromTemplate
{
    private LinkServiceFactory $linkServiceFactory;

    public function __construct(LinkServiceFactory $linkServiceFactory)
    {
        $this->linkServiceFactory = $linkServiceFactory;
    }

    public function run(string $url)
    {
        $source = Source::find(Source::ID_LEADS);

        $linkService = $this->linkServiceFactory->getCreatorInstance(
            $source
        );

        $webmaster = Webmaster::query()->find(request()->cookie('webmaster_id'));
        $sourceDomain = request()->getHost();

        $publicDashboardLink = $linkService->getPublicDashboardLink($url, $webmaster, $sourceDomain);

        return $publicDashboardLink;
    }
}
