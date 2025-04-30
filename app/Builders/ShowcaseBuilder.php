<?php

namespace App\Builders;

use App\Models\Showcase;
use Illuminate\Database\Eloquent\Model;

/**
 * @method null|Showcase find($id, $columns = ['*'])
 * @method null|Showcase first($columns = ['*'])
 *
 * @template TModelClass of Model&Showcase
 *
 * @extends BaseBuilder<Showcase>
 */
class ShowcaseBuilder extends BaseBuilder
{
    /**
     * Фильтр по публичности витрины.
     * @param bool $value
     * @return ShowcaseBuilder
     */
    public function whereIsPublic(bool $value = true): ShowcaseBuilder
    {
        return $this->where('is_public', $value);
    }

    /**
     * Фильтр по приватности витрины.
     * @param bool $value
     * @return ShowcaseBuilder
     */
    public function whereIsPrivate(bool $value = true): ShowcaseBuilder
    {
        return $this->whereIsPublic(!$value);
    }

    /**
     * Фильтр по витринам без внешнего адреса.
     * @return ShowcaseBuilder
     */
    public function whereExternalUrlIsNull(): ShowcaseBuilder
    {
        return $this->whereNull('external_url');
    }
}