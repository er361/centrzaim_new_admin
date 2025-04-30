<?php

namespace App\Builders;

use App\Models\Role;
use App\Models\Statistic;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModelClass of Model&Statistic
 *
 * @extends BaseBuilder<Statistic>
 */
class StatisticBuilder extends BaseBuilder
{
    /**
     * Фильтр источников, доступных пользователю.
     * @param User $user
     * @return StatisticBuilder
     */
    public function forUser(User $user): StatisticBuilder
    {
        if ($user->role_id === Role::ID_ADMIN || $user->isSuperAdmin() || $user->isSuperAdmin()) {
            return $this;
        }

        if ($user->role_id !== Role::ID_TRAFFIC_SOURCE) {
            return $this->whereRaw('0 = 1');
        }

        return $this->whereHas('webmaster', function (WebmasterBuilder $query) use ($user) {
            $query->forUser($user);
        });
    }

    public function groupBySourceId(): StatisticBuilder
    {
        if (!$this->joined('webmasters')) {
            $this->leftJoin('webmasters', 'webmasters.id', '=', 'statistics.webmaster_id');
        }

        return $this
            ->selectRaw('webmasters.source_id')
            ->groupBy(['source_id']);
    }

    public function groupByWebmasterApiId(): StatisticBuilder
    {

        if (!$this->joined('webmasters')) {
            $this->leftJoin('webmasters', 'webmasters.id', '=', 'statistics.webmaster_id');
        }

        return $this
            ->selectRaw('webmasters.source_id')
            ->selectRaw('webmasters.api_id')
            ->groupBy(['source_id', 'api_id']);
    }

    public function selectAndGroupByDay(): StatisticBuilder
    {
        return $this
            ->selectRaw('DATE_FORMAT(statistics.date, \'%d.%m.%Y\') as formatted_date')
            ->groupBy('formatted_date');
    }

    public function selectAndGroupByMonth(): StatisticBuilder
    {
        return $this
            ->selectRaw('DATE_FORMAT(statistics.date, \'%m.%Y\') as formatted_date')
            ->groupBy('formatted_date');
    }

    public function selectAndGroupByYear(): StatisticBuilder
    {
        return $this
            ->selectRaw('DATE_FORMAT(statistics.date, \'%Y\') as formatted_date')
            ->groupBy('formatted_date');
    }
}