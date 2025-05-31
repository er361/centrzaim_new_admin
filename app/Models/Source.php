<?php

namespace App\Models;

use App\Builders\SourceBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Source
 * @package App\Models
 *
 * @property string $name
 * @property float $postback_cost Стоимость конверсии
 *
 * @property-read int $id
 *
 * @method static SourceBuilder query()
 */
class Source extends Model
{
    /**
     * Идентфиикатор источника Leads.
     */
    public const ID_LEADS = 1;

    /**
     * Идентификатор источника GuruLeads.
     */
    public const ID_GURU_LEADS = 2;

    /**
     * Идентификатор источника "Прямой вебмастер"
     */
    public const ID_DIRECT = 3;

    /**
     * Идентификатор источника "Лид Гид".
     */
    public const ID_LEAD_GID = 4;

    /**
     * Идентификатор источника "Лид Крафт".
     */
    public const ID_LEAD_CRAFT = 5;

    /**
     * Идентификатор источника "Лид Бит".
     */
    public const ID_LEAD_BIT = 6;

    /**
     * Идентификатор источника "Click2Money".
     */
    public const ID_CLICK_2_MONEY = 7;

    /**
     * Идентификатор источника "LeadsTech".
     */
    public const ID_LEADS_TECH = 8;

    /**
     * Идентификатор источника "Affise".
     */
    public const ID_AFFISE = 9;

    /**
     * Идентификатор источника "Fin CPA Network".
     */
    public const ID_FIN_CPA_NETWORK = 10;

    /**
     * Идентификатор источника "XPartners".
     */
    public const ID_X_PARTNERS = 11;

    /**
     * Идентификатор источника "LeadTarget".
     */
    public const ID_LEAD_TARGET = 12;

    /**
     * Идентификатор источника "Finkort".
     */
    public const ID_FINKORT = 13;

    /**
     * Идентификатор источника "ЛинкМани".
     */
    public const ID_LINK_MONEY = 14;

    /**
     * Идентификатор источника "Альянс".
     */
    public const ID_ALLIANCE = 15;

    /**
     * Идентификатор источника "Bankiros".
     */
    public const ID_BANKIROS = 16;

    /**
     * Идентификатор источника "Sravni".
     */
    public const ID_SRAVNI = 17;
    
    /**
     * Идентификатор источника "Rafinad".
     */
    public const ID_RAFINAD = 18;

    /**
     * Идентификатор источника "AdsFin".
     */
    public const ID_ADSFIN = 19;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'postback_cost',
    ];

    /**
     * @param $query
     * @return SourceBuilder
     */
    public function newEloquentBuilder($query): SourceBuilder
    {
        return new SourceBuilder($query);
    }

    /**
     * @return HasMany
     */
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    /**
     * @return HasMany
     */
    public function webmasters(): HasMany
    {
        return $this->hasMany(Webmaster::class);
    }

    /**
     * @return HasMany
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * @return HasMany
     */
    public function loanLinks(): HasMany
    {
        return $this->hasMany(Loan::class, 'link_source_id');
    }
}