<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->chunkById(100,
                function (Collection $users) {
                    $paymentSuccessCount = DB::table('payments')
                        ->selectRaw('count(*) as count, user_id')
                        ->where('type', 2) // Payment::TYPE_RECURRENT
                        ->where('status', 10) // Payment::STATUS_PAYED
                        ->whereIn('user_id', $users->pluck('id')->toArray())
                        ->groupBy('user_id')
                        ->get()
                        ->keyBy('user_id')
                        ->map(function ($item) {
                            return $item->count;
                        });

                    foreach ($users as $user) {
                        $count = $paymentSuccessCount->get($user->id, 0);

                        $recurrentPaymentConsequentErrorCount = 0;
                        $userPayments = DB::table('payments')
                            ->where('type', 2) // Payment::TYPE_RECURRENT
                            ->where('user_id', $user->id)
                            ->orderByDesc('id')
                            ->get();

                        foreach ($userPayments as $payment) {
                            if ($payment->status === 10) { // Payment::STATUS_PAYED
                                break;
                            }

                            $recurrentPaymentConsequentErrorCount++;
                        }

                        if ($recurrentPaymentConsequentErrorCount > 0 || $count > 0) {
                            DB::table('users')
                                ->where('id', $user->id)
                                ->update([
                                    'recurrent_payment_consequent_error_count' => $recurrentPaymentConsequentErrorCount,
                                    'recurrent_payment_success_count' => $count,
                                ]);
                        }
                    }
                }
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Миграция изменяет данные, необратима
    }
};
