<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = now();

        $currentLoanNames = DB::table('loans')
            ->selectRaw('DISTINCT name')
            ->pluck('name');

        foreach ($currentLoanNames as $currentLoanName) {
            $currentLoans = DB::table('loans')
                ->where('name', 'LIKE', "%{$currentLoanName}%")
                ->get();

            DB::table('loans')
                ->where('name', 'LIKE', "%{$currentLoanName}%")
                ->update(['deleted_at' => $now]);

            $newLoan = DB::table('loans')
                ->insertGetId([
                    'image_path' => $currentLoans->first()->image_path,
                    'name' => $currentLoans->first()->name,
                    'description' => $currentLoans->first()->description,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            foreach ($currentLoans as $currentLoan) {
                DB::table('loan_offers')
                    ->insert([
                       'link' => $currentLoan->link,
                        'priority' => $currentLoan->priority,
                        'type' => $currentLoan->type,
                        'description' => $currentLoan->description,
                        'loan_id' => $newLoan,
                        'source_id' => $currentLoan->source_id,
                        'link_source_id' => $currentLoan->link_source_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Миграция изменяет данные, необратима
    }
};
