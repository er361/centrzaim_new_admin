<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserInfoResource",
 *     title="Информация о пользователе",
 *     description="Ресурс с детальной информацией о пользователе",
 *     @OA\Property(
 *         property="first_name",
 *         type="string",
 *         description="Имя пользователя",
 *         example="Иван"
 *     ),
 *     @OA\Property(
 *         property="birthdate",
 *         type="string",
 *         format="date",
 *         description="Дата рождения",
 *         example="1990-05-15"
 *     ),
 *     @OA\Property(
 *         property="mphone",
 *         type="string",
 *         description="Мобильный телефон",
 *         example="+79001234567"
 *     ),
 *     @OA\Property(
 *         property="credit_sum",
 *         type="number",
 *         format="float",
 *         description="Сумма кредита",
 *         example=50000
 *     )
 * )
 */
class UserInfoResource extends JsonResource
{
    /**
     * Преобразование ресурса в массив.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->first_name,
            'birthdate' => $this->birthdate,
            'mphone' => $this->mphone,
            'credit_sum' => $this->credit_sum,
        ];
    }
}