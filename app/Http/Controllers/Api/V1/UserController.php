<?php


namespace App\Http\Controllers\Api\V1;

use App\Builders\PaymentBuilder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserIndexRequest;
use App\Http\Resources\UserInfoResource;
use App\Http\Resources\UserResource;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Получение списка ссылок.
     *
     * @param UserIndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(UserIndexRequest $request): AnonymousResourceCollection
    {
        $cardNumber = $request->string('payment_card_number')
            ->replace('*', Payment::CARD_MASK_SYMBOL);

        $users = User::query()
            ->whereHas('payments', function (PaymentBuilder $query) use ($cardNumber) {
                $query->where('card_number', $cardNumber);
            })
            ->get();

        return UserResource::collection($users);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user-info/{user}",
     *     summary="Получить информацию о пользователе",
     *     description="Возвращает детальную информацию о запрошенном пользователе",
     *     operationId="getUserInfo",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID пользователя",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(ref="#/components/schemas/UserInfoResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Не авторизован"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не найден"
     *     )
     * )
     */
    public function info(User $user): UserInfoResource
    {
        return new UserInfoResource($user);
    }
}