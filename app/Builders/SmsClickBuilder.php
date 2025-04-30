<?php

namespace App\Builders;

use App\Models\SmsClick;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModelClass of Model&SmsClick
 *
 * @extends BaseBuilder<SmsClick>
 */
class SmsClickBuilder extends BaseBuilder
{
    /**
     * Фильтр по дате создания до переданной даты.
     *
     * @param CarbonInterface $date
     * @return SmsClickBuilder
     */
    public function whereCreatedAtBefore(CarbonInterface $date): SmsClickBuilder
    {
        return $this->where('created_at', '<=', $date);
    }
}