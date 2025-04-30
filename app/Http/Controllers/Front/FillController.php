<?php


namespace App\Http\Controllers\Front;


use App\Events\UserRegistrationFinished;
use App\Http\Controllers\Controller;
use App\Http\Requests\FillRequest;
use App\Models\User;
use App\Services\SettingsService\SettingsService;
use App\Services\SiteService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FillController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // Пользователь уже завершил регистрацию
        if ($user->fill_status === User::FILL_STATUS_FINISHED) {
            return redirect()->route('account.dashboard');
        }

        $config = SiteService::getActiveSiteConfiguration();
        $steps = array_keys($config['fill_steps']);
        $enabledSteps = SettingsService::getEnabledFillSteps();

        foreach ($steps as $step) {
            if (in_array($step, $enabledSteps) && $user->fill_status < $step) {
                return view('account.fill.step' . $step);
            } elseif (!in_array($step, $enabledSteps) && $user->fill_status < $step) {
                // Пользователь не может заполнить этот шаг
                $user->update([
                    'fill_status' => $step,
                ]);
            }
        }

        // Пользователь или не может заполнить ни один из шагов, или уже заполнил
        $user->update([
            'fill_status' => User::FILL_STATUS_FINISHED,
        ]);

        event(new UserRegistrationFinished($user));

        return redirect()->route('vitrina');
    }

    /**
     * @param FillRequest $request
     * @return RedirectResponse
     */
    public function store(FillRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Пользователь уже завершил регистрацию
        if ($user->fill_status === User::FILL_STATUS_FINISHED) {
//            return redirect()->route('account.dashboard');
            return redirect()->route('vitrina');
        }

        $data = $request->validated();

        if (!empty($data['passport_date'])) {
            $data['passport_date'] = Carbon::parse($data['passport_date'])->toDateString();
        }

        $data['fill_status'] = $request->input('fill_step');
        unset($data['fill_step']);

        if (!empty($data['birthdate'])) {
            $data['birthdate'] = Carbon::parse($data['birthdate'])->toDateString();
        }

        $user->update($data);

        return redirect()->back();
    }

    public function validateStep(FillRequest $request)
    {
        return response()->json([], 200);
    }
}