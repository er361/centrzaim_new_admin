<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use App\Services\OffersChecker\Settings;
use Illuminate\Support\Facades\Auth;

class UserProfileService
{
    private UserOfferService $userOfferService;

    public function __construct()
    {
        $this->userOfferService = new UserOfferService();
    }

    public function getProfileData(User $user): array
    {
        $user->load('fccpInfo');
        return [
            'name' => $user->getFullName(),
            'phone' => $user->mphone,
            'birthdate' => $user->birthdate,
            'payment_sub_type' => $this->mapSubType($user->calculatePaymentSubType()),
            'fccp' => $user->fccpInfo,
        ];
    }

    public function getPassportData(User $user): array
    {
        return [
            'title' => $user->passport_title,
            'date' => $user->passport_date,
            'code' => $user->passport_code,
            'reg_address' => $user->fullRegAddress,
            'fact_address' => $user->fullFactAddress,
        ];
    }

    public function mapSubType($subType): string
    {

        return match ($subType) {
            -1 => 'Не активна',
            Payment::SUBTYPE_MONTHLY => 'Активна 1998 руб. в месяц',
            Payment::SUBTYPE_WEEKLY => 'Активна 499 руб. в неделю',
            default => 'Неизвестно',
        };
    }


    public function getOffers(User $user): array
    {
        return $this->userOfferService->getOffers($user);
    }

}
