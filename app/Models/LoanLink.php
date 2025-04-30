<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LoanLink.
 *
 * @property-read int $id Идентификатор записи
 * @property string $link Ссылка
 * @property int $source_id Партнерская программа, на которую ведет ссылка
 * @property int $loan_id Предложение
 *
 * @property-read Source $source Партнерская программа, на которую ведет ссылка
 * @property-read Loan $loan Предложение
 *
 * @package App\Models
 */
class LoanLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'link',
        'source_id',
        'loan_id',
    ];

    /**
     * @return BelongsTo
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * @return BelongsTo
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }
}
