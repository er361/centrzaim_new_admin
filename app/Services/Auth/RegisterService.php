<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterService
{
    /**
     * Array of available payment plans
     *
     * @var array
     */
    protected array $availablePlans = [0, 1];

    /**
     * Distribution percentages for payment plans (must sum to 100)
     * Keys are plan values, values are percentage weights
     *
     * @var array
     */
    protected array $planDistribution = [
        0 => 50,
        1 => 50,
    ];
    
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make(Str::random());
        $data['ip_address'] = request()->ip() === null ? null : substr(request()->ip(), 0, 46);
        $data['webmaster_id'] = request()->cookie('webmaster_id');
        $data['transaction_id'] = request()->cookie('transaction_id');
        $data['additional_transaction_id'] = request()->cookie('additional_transaction_id');
        $data['mphone'] = $this->convertPhone($data['mphone']);
        $data['birthdate'] = isset($data['birthdate']) ? Carbon::parse($data['birthdate'])->toDateString() : null;
        $data['payment_plan'] = $this->getPaymentPlan();
        $data['role_id'] = Role::ID_USER;
        
        // Save credit amount and days if provided
        if (isset($data['amount'])) {
            $data['credit_sum'] = $data['amount'];
        }
        
        if (isset($data['days'])) {
            $data['credit_days'] = $data['days'];
        }

        if (empty($data['email'])) {
            $data['email'] = Str::random() . '@' . config('app.domain');
        }

        $fullnameParts = explode(' ', $data['fullname']);
        $data['last_name'] = $fullnameParts[0] ?? '';
        $data['name'] = $fullnameParts[1] ?? '';
        $data['middlename'] = $fullnameParts[2] ?? '';
        return User::query()->create($data);
    }

    /**
     * Get a payment plan based on distribution settings
     * Currently returns 0 or 1 with 50/50 probability
     *
     * @return int
     */
    public function getPaymentPlan(): int
    {
        return 0;
        // Fallback to config if needed
        if (empty($this->availablePlans)) {
            return config('payments.users_payment_plan');
        }
        
        // Simple 50/50 distribution if no custom distribution set
        if (empty($this->planDistribution)) {
            return $this->availablePlans[array_rand($this->availablePlans)];
        }
        
        // Use weighted distribution based on percentages
        $rand = mt_rand(1, 100);
        $cumulativePercent = 0;
        
        foreach ($this->planDistribution as $plan => $percent) {
            $cumulativePercent += $percent;
            
            if ($rand <= $cumulativePercent) {
                return $plan;
            }
        }
        
        // Fallback to first available plan
        return $this->availablePlans[0];
    }

    public function convertPhone(string $phone): string
    {
        return str_replace(['(', ')', '-', ' '], '', $phone);
    }
}