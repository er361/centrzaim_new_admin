<?php

namespace App\Builders;


use App\Models\Banner;
use App\Models\Source;
use App\Models\Webmaster;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Banner create(array $attributes = [])
 *
 * @template TModelClass of Model&Banner
 *
 * @extends BaseBuilder<Banner>
 */
class BannerBuilder extends BaseBuilder
{
    /**
     * Фильтр по источнику и вебмастеру.
     * @param Source $source
     * @param Webmaster|null $webmaster
     * @return BannerBuilder
     */
    public function whereShouldBeVisibleFor(Source $source, ?Webmaster $webmaster): BannerBuilder
    {
        return $this->where(function (BannerBuilder $query) use ($source, $webmaster) {
            $query
                ->whereHas('sources', function (SourceBuilder $query) use ($source) {
                    $query->where('id', $source->id);
                })
                ->orWhereDoesntHave('sources');

            if ($webmaster !== null) {
                $query->orWhereHas('webmasters', function (WebmasterBuilder $query) use ($webmaster) {
                    $query->where('id', $webmaster->id);
                });
            }
        });
    }

}