<?php

namespace App\ViewComposers;

use App\Actions\GenerateUtmFromTemplate;
use App\Facades\SettingsServiceFacade;
use App\Services\RedirectUrlService;
use App\Services\SettingsService\Enums\FrontendSettingsEnum;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Contracts\View\View;

class ConfigViewComposer
{
    /**
     * @param View $view
     */
    public function compose(View $view): void
    {
        $redirectUrlService = app(RedirectUrlService::class);

        $sliderAmount = SettingsServiceFacade::getByKey(FrontendSettingsEnum::SLIDER_AMOUNT);
        $redirectDelay = SettingsServiceFacade::getByKey(FrontendSettingsEnum::REDIRECT_TIMING);

        $redirectUrl = $redirectUrlService->generateUrl(SettingsServiceFacade::getByKey(FrontendSettingsEnum::REDIRECT_URL));

        $isRedirectEnabled = SettingsServiceFacade::getByKey(FrontendSettingsEnum::IS_REDIRECT_ENABLED);

        $dadataToken = SettingsService::getByKey(SettingNameEnum::DadataToken);
        $sliderSumm = $sliderAmount ?? 20000;

        $view->with('dadataToken', $dadataToken);
        $view->with('sliderSumm', $sliderSumm);
        $view->with('redirectDelay', $redirectDelay);
        $view->with('redirectUrl', $redirectUrl);
        $view->with('isRedirectEnabled', $isRedirectEnabled);
    }
}
