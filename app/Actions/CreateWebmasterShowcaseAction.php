<?php

namespace App\Actions;

use App\Models\LoanOffer;
use App\Models\Webmaster;
use App\Models\WebmasterTemplate;

class CreateWebmasterShowcaseAction
{

    public function run(Webmaster $webmaster)
    {
        LoanOffer::withoutEvents(function () use ($webmaster) {
            LoanOffer::whereWebmasterId($webmaster->id)->delete();

            WebmasterTemplate::where('source_id', $webmaster->source_id)
                ->each(function (WebmasterTemplate $webmasterTemplate) use ($webmaster) {
                    $webmasterTemplate->loanOffer()->each(function (LoanOffer $loanOffer) use ($webmaster) {
                        $templateOffer = $loanOffer->replicate(['webmaster_id']);
                        $templateOffer->webmaster_id = $webmaster->id;
                        $templateOffer->save();
                        $templateOffer->setHighestOrderNumber();
                    });
                });
        });


    }
}