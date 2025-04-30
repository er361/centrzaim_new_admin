<?php


namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Models\Sms;
use App\Models\SmsClick;
use App\Models\User;
use App\Services\LinkService\LinkServiceFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    /**
     * Редирект по ссылке из SMS.
     *
     * @param Sms $sms
     * @param Request $request
     * @param LinkServiceFactory $linkServiceFactory
     * @return RedirectResponse
     */
    public function redirect(Sms $sms, Request $request, LinkServiceFactory $linkServiceFactory): RedirectResponse
    {
        /** @var null|User $user */
        $user = null;

        if ($request->has(['key', 'user_id'])) {
            /** @var null|User $requestUser */
            $requestUser = User::query()->find($request->input('user_id'));

            if ($requestUser !== null) {
                if ($sms->getSecretKey((string)$requestUser->id) === $request->input('key')) {
                    $user = $requestUser;
                }
            }
        }

        if ($user !== null) {
            SmsClick::query()->create([
                'sms_id' => $sms->id,
                'user_id' => $user->id,
            ]);
        }

        if (empty($sms->link)) {
            Log::warning('Обнаружен переход по неизвестной ссылке в SMS', [
                'sms_id' => $sms->id,
                'url' => $request->url(),
                'request' => $request->all(),
            ]);

            return redirect()->route('front.loans');
        }

        if ($sms->linkSource === null) {
            $redirectLink = $sms->link;
        } else {
            $linkService = $linkServiceFactory->getCreatorInstance($sms->linkSource);
            $redirectLink = $linkService->getSmsLink($sms->link, $user, $sms, null);
        }

        return redirect()->to($redirectLink);
    }
}