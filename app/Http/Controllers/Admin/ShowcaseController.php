<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShowcaseIndexRequest;
use App\Models\LoanOffer;
use App\Models\Showcase;
use App\Models\Source;
use App\Models\SourceShowcase;
use App\Models\Webmaster;
use App\Models\WebmasterTemplate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class ShowcaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ShowcaseIndexRequest $request
     * @return Factory|View|Application
     */
    public function index(ShowcaseIndexRequest $request): Factory|View|Application
    {
        $showcases = Showcase::query()->pluck('name', 'id')->prepend('Пожалуйста, выберите', null);
        $sources = Source::query()->pluck('name', 'id')->prepend('Пожалуйста, выберите', null);
        $webmasters = Webmaster::query()->pluck('api_id', 'id')->prepend('Пожалуйста, выберите', null);
        $isTemplate = false;

        if ($request->has(['showcase_id', 'source_id'])) {
            // @todo Подумать о привязке LoanOffer к модели SourceShowcase
            $loanOffers = LoanOffer::query()
                ->has('loan')
                ->has('loanLink')
                ->with([
                    'loan',
                    'loanLink'
                ])
                ->where('source_id', $request->input('source_id'))
                ->where('showcase_id', $request->input('showcase_id'))
                ->where('webmaster_id', $request->input('webmaster_id'))
                ->orderBy('priority')
                ->get();

            $featuredLoanOffers = $loanOffers
                ->mapWithKeys(function (LoanOffer $loanOffer) {
                    return [$loanOffer->id => $loanOffer->loan->name];
                })
                ->prepend('Не выбран', null);

            $sourceShowcase = SourceShowcase::query()
                ->where('source_id', $request->input('source_id'))
                ->where('showcase_id', $request->input('showcase_id'))
                ->where('webmaster_id', $request->input('webmaster_id'))
                ->first();
        } else {
            $loanOffers = null;
            $featuredLoanOffers = null;
            $sourceShowcase = null;
        }

        if (
            WebmasterTemplate::where('showcase_id', $request->input('showcase_id'))
            ->where('source_id', $request->input('source_id'))
            ->where('webmaster_id', $request->input('webmaster_id'))
            ->exists()
        ) {
            $isTemplate = true;
        }

        return view('admin.showcases.index', compact(
            'showcases',
            'sources',
            'webmasters',
            'loanOffers',
            'featuredLoanOffers',
            'sourceShowcase',
            'isTemplate'
        ));
    }
}
