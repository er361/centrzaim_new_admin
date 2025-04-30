<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoanLinkStoreRequest;
use App\Models\Loan;
use App\Models\LoanLink;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class LoanLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param LoanLinkStoreRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(LoanLinkStoreRequest $request): RedirectResponse
    {
        /** @var Loan $loan */
        $loan = Loan::query()
            ->with([
                'loanLinks' => function (HasMany $query) {
                    $query->withTrashed(); // @phpstan-ignore-line
                },
            ])
            ->find($request->input('loan_id'));

        $links = $request->collect('loan_links');
        $loanLinks = $loan->loanLinks->keyBy('source_id');

        $links->each(function (?string $link, int $sourceId) use ($loan, $loanLinks) {
            /** @var null|LoanLink $existingLoanLink */
            $existingLoanLink = $loanLinks->get((int)$sourceId);

            if ($existingLoanLink === null && $link !== null) {
                // Если нет ни удаленной, ни существующей ссылки, то создаем новую
                LoanLink::query()->create([
                    'link' => $link,
                    'source_id' => $sourceId,
                    'loan_id' => $loan->id,
                ]);
            } elseif ($existingLoanLink !== null && $link !== null) {
                // Если есть существующая ссылка, то обновляем ее
                $existingLoanLink->update([
                    'link' => $link,
                ]);

                // Если при этом ссылка удалена, то дополнительно восстанавливаем ее
                if ($existingLoanLink->trashed()) {
                    $existingLoanLink->restore();
                }
            } elseif ($existingLoanLink !== null && !$existingLoanLink->trashed() && $link === null) {
                // Если есть существующая неудаленная ссылка, теперь она не нужна, удаляем ее
                $existingLoanLink->delete();
            }
        });

        return Redirect::back()
            ->with('success', 'Настройки ссылок для оффера успешно сохранены.');
    }
}
