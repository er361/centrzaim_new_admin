<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SourceShowcaseLoansEntityResource;
use App\Models\Showcase;
use App\Models\Sms;
use App\Models\SmsClick;
use App\Models\User;
use App\Models\Webmaster;
use App\Services\LoanService\LoanServiceBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoanController extends Controller
{
    /**
     * Получение списка ссылок.
     *
     * @param Request $request
     * @param LoanServiceBuilder $loanServiceBuilder
     * @return SourceShowcaseLoansEntityResource
     */
    public function index(Request $request, LoanServiceBuilder $loanServiceBuilder): SourceShowcaseLoansEntityResource
    {
        if ($request->headers->get('X-TOKEN') !== config('services.showcases.token')) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        /** @var Showcase $showcase */
        $showcase = Showcase::query()->find($request->input('showcase_id'));

        /** @var null|User $user */
        $user = empty($request->input('user_id'))
            ? null
            : User::query()->find($request->input('user_id'));

        /** @var null|Webmaster $webmaster */
        $webmaster = Webmaster::query()
            ->find($request->input('webmaster_id'))
            ?? $user?->webmaster;

        /** @var null|Sms $sms */
        $sms = empty($request->input('sms_id'))
            ? null
            : Sms::query()->find($request->input('sms_id'));

        $sourceDomain = empty($request->headers->get('X-SOURCE-DOMAIN'))
            ? null
            : $request->headers->get('X-SOURCE-DOMAIN');

        if ($user !== null && $sms !== null) {
            SmsClick::query()->create([
                'sms_id' => $sms->id,
                'user_id' => $user->id,
            ]);
        }

        $sourceShowcaseLoansEntity = $loanServiceBuilder
            ->setShowcase($showcase)
            ->setWebmaster($webmaster)
            ->setSms($sms)
            ->setUser($user)
            ->setSourceDomain($sourceDomain)
            ->getLoanService()
            ->getSourceShowcaseLoans();

        return new SourceShowcaseLoansEntityResource($sourceShowcaseLoansEntity);
    }
}