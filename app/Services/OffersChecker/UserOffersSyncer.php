<?php

namespace App\Services\OffersChecker;

use App\Models\User;

class UserOffersSyncer
{
    public function syncOffers(string $phone, array $offers): void
    {
        $user = User::where('mphone', $phone)
            ->with('offers')
            ->first();

        if (!$user) {
            throw new \Exception('User not found');
        }

        $user->offers()->updateOrCreate([],[
            'repeated_offers' => $offers,
        ]);
    }
}