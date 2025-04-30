<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserCreateRequest;
use App\Http\Requests\Admin\UserEditRequest;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use App\Services\AccessService;
use App\Services\ExportService\UserExportService;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    /**
     * Количество секунд в рамках которых можно получить повторный доступ к пользователю.
     */
    protected const VIEW_LIFETIME_SECONDS = 60 * 15;

    /**
     * Количество поисков в единицу времени.
     */
    protected const SEARCH_ATTEMPTS_COUNT = 500;

    /**
     * Раз в сколько секунд выдается такое количество попыток.
     */
    protected const ATTEMPTS_PER_SECONDS = 24 * 60 * 60; // 24 часа

    /**
     * Display a listing of User.
     *
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        if (!Gate::allows('user_list')) {
           abort(401);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($request->ajax()) {
            $query = User::query()
                ->filter($request->all())
                ->forUser($user)
                ->select([
                    'users.id',
                    'users.email',
                    'users.created_at',
                    'users.role_id',
                    'users.recurrent_payment_success_count',
                ])
                ->with('accessibleWebmasters');

            $table = Datatables::of($query);

            $table->editColumn('created_at', function ($row) {
                return $row->created_at->format('d.m.Y H:i:s');
            });

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) {
                return view('admin.users.actionsTemplate', compact('row'));
            });


            $table->rawColumns(['actions']);

            return $table->make(true);
        }

        $webmasters = Webmaster::query()
            ->forUser($user)
            ->with('source')
            ->get()
            ->keyBy('id')
            ->map(function (Webmaster $webmaster) {
                return $webmaster->completeName;
            });
        $paymentPlans = collect(config('payments.plans'))
            ->pluck('name')
            ->toArray();

        $roles = Role::query()->pluck('title', 'id')->toArray();

        return view('admin.users.index', compact('webmasters', 'paymentPlans', 'roles'));
    }

    /**
     * Создание пользователя.
     *
     * @param UserCreateRequest $request
     * @return Application|Factory|View
     */
    public function create(UserCreateRequest $request): Factory|View|Application
    {
        $roles = Role::query()->pluck('title', 'id');
        $webmasters = Webmaster::query()
            ->where('source_id', Source::ID_DIRECT)
            ->with('source')
            ->get()
            ->keyBy('id')
            ->map(function (Webmaster $webmaster) {
                return $webmaster->completeName;
            });

        return view('admin.users.create', compact('roles', 'webmasters'));
    }

    /**
     * Создание пользователя.
     *
     * @param UserStoreRequest $request
     * @return RedirectResponse
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::query()->create($data);
        $user->accessibleWebmasters()->sync($request->validated('accessible_webmaster_id'));

        return redirect()->route('admin.users.show', $user);
    }

    /**
     * Редактирование пользователя.
     *
     * @param UserEditRequest $request
     * @param User $user
     * @return Application|Factory|View
     */
    public function edit(UserEditRequest $request, User $user): Factory|View|Application
    {
        $roles = Role::query()->pluck('title', 'id');
        $webmasters = Webmaster::query()
            ->where('source_id', Source::ID_DIRECT)
            ->with('source')
            ->get()
            ->keyBy('id')
            ->map(function (Webmaster $webmaster) {
                return $webmaster->completeName;
            });

        return view('admin.users.edit', compact('roles', 'webmasters', 'user'));
    }

    /**
     * Редактирование пользователя.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        $user->accessibleWebmasters()->sync($request->validated('accessible_webmaster_id'));

        return redirect()->route('admin.users.show', $user);
    }

    /**
     * Display User.
     *
     * @param User $user
     * @return Application|Factory|\Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function show(User $user)
    {
        $this->authorize('show', $user);

        $user->loadMissing([
            'webmaster.source',
        ]);

        $payments = $user->payments()->get();
        $wasPostbackSent = $user->postbacks()->whereNotNull('sent_at')->exists();
        $paymentPlans = config('payments.plans');

        return view('admin.users.show', compact('user', 'payments', 'wasPostbackSent', 'paymentPlans'));
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function export(Request $request)
    {
        if (!Gate::allows('user_full_access')) {
            abort(401);
        }

        $fileName = 'users_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $query = User::query()
            ->filter($request->all())
            ->with([
                'webmaster.source',
            ]);

        return Excel::download(new UserExport($query), $fileName);
    }

    /**
     * Поиск по пользователям для сотрудника КЦ.
     * @param Request $request
     * @return bool|Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function search(Request $request)
    {
        if (!Gate::allows('user_search')) {
            abort(401);
        }

        if ($request->hasAny(['mphone', 'email'])) {
            /** @var User $currentUser */
            $currentUser = Auth::user();

            $executed = RateLimiter::attempt(
                'search-user:' . $currentUser->id,
                self::SEARCH_ATTEMPTS_COUNT,
                function () use ($request, $currentUser) {
                    $user = User::query()
                        ->filter($request->all())
                        ->first();

                    if ($user !== null) {
                        $cacheKey = AccessService::getUserAccessCacheKey($currentUser, $user);

                        if (!Cache::has($cacheKey)) {
                            Cache::put(
                                $cacheKey,
                                true,
                                now()->addSeconds(self::VIEW_LIFETIME_SECONDS)
                            );
                        }

                        return redirect()->route('admin.users.show', compact('user'));
                    }

                    return redirect()
                        ->back()
                        ->withInput($request->all())
                        ->withErrors('Пользователь не найден');
                },
                self::ATTEMPTS_PER_SECONDS
            );

            if ($executed === false) {
                return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors('Слишком много попыток поиска, попробуйте позже.');
            } else {
                return $executed;
            }
        }

        return view('admin.users.search');
    }

    /**
     * Отписка пользователя от услуг.
     * @param User $user
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function unsubscribe(User $user): RedirectResponse
    {
        $this->authorize('unsubscribe', $user);

        $user->update([
            'is_disabled' => 1,
        ]);

        return redirect()
            ->back()
            ->with(['success' => 'Пользователь отписан.']);
    }

    public function document(User $user) {
        $this->authorize('show', $user);

        /** @var null|Payment $cardPayment */
        $cardPayment = $user->payments()
            ->whereNotNull('card_number')
            ->first();

        if ($cardPayment === null) {
            return redirect()->back()
                ->withErrors('Пользователь не привязывал карту, генерация договора невозможна');
        }

        $url = route('front.index');
        $urlDetails = parse_url($url);
        $supportEmail = 'support@' . $urlDetails['host'];

        $cardNumber = $cardPayment->card_number;
        $userName = $user->getFullName();
        $cardPaymentDate = $cardPayment->updated_at;
        $ipAddress = $user->ip_address ?? '{нет данных}';

        $baseTemplatePath = 'docs/template.docx';
        $fullTemplatePath = Storage::path($baseTemplatePath);

        $fileName = "user_docs_{$user->id}.docx";
        $fullDocumentPath = Storage::path("docs/users/$fileName");

        $templateProcessor = new TemplateProcessor($fullTemplatePath);
        $templateProcessor->setValue('{url}', $url);
        $templateProcessor->setValue('{card_number}', $cardNumber);
        $templateProcessor->setValue('{name}', $userName);
        $templateProcessor->setValue('{support_email}', $supportEmail);
        $templateProcessor->setValue('{date_day}', $cardPaymentDate->format('d'));
        $templateProcessor->setValue('{date_month}', $cardPaymentDate->format('m'));
        $templateProcessor->setValue('{date_year}', $cardPaymentDate->format('Y'));
        $templateProcessor->setValue('{date_time}', $cardPaymentDate->format('H:i'));
        $templateProcessor->setValue('{created_at}', $user->created_at->format('d.m.Y H:i:s'));
        $templateProcessor->setValue('{ip_address}', $ipAddress);
        $templateProcessor->saveAs($fullDocumentPath);

        return response()
            ->download($fullDocumentPath, $fileName)
            ->deleteFileAfterSend();
    }
}
