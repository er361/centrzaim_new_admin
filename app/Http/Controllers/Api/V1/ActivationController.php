<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     title="API активации",
 *     version="1.0.0",
 *     description="API для управления кодами активации пользователей"
 * )
 */
class ActivationController extends Controller
{
    /**
     * Генерирует 6-значный код активации для пользователя.
     *
     * @OA\Get(
     *     path="/api/v1/activation/code",
     *     operationId="generateActivationCode",
     *     tags={"Activation"},
     *     summary="Генерация 6-значного кода активации",
     *     description="Генерирует и сохраняет 6-значный код активации для указанного пользователя",
     *     security={
     *         {"sanctum": {}}
     *     },
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         description="ID пользователя для генерации кода активации",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Код активации успешно создан",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Код активации успешно создан"),
     *             @OA\Property(property="activation_code", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The user id field is required."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не найден"
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateCode(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
            ]);
        } catch (ValidationException $e) {
            // Проверка на ошибку существования (exists)
            if (isset($e->errors()['user_id'])) {
                return response()->json(['message' => 'Пользователь не найден'], 404);
            }
            throw $e;
        }

        $user = User::find($request->user_id);

        // Генерируем 6-значный числовой код
        $activationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Сохраняем код в профиле пользователя
        $user->activation_code = $activationCode;
        $user->save();

        return response()->json([
            'message' => 'Код активации успешно создан',
            'activation_code' => $activationCode
        ]);
    }

    /**
     * Подтверждает код активации.
     *
     * @OA\Post(
     *     path="/api/v1/activation/confirm",
     *     operationId="confirmActivationCode",
     *     tags={"Activation"},
     *     summary="Подтверждение кода активации",
     *     description="Проверяет код активации и активирует аккаунт пользователя",
     *     security={
     *         {"sanctum": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "user_id"},
     *             @OA\Property(property="code", type="string", example="123456"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Активация успешно завершена",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Активация успешно завершена")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Неверный код активации или ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Неверный код активации")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не найден"
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmCode(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        $request->validate([
            'code' => 'required|string|size:6',
            'user_id' => 'required|integer',
        ]);


        $submittedCode = $request->input('code');

        if ($user->activation_code !== $submittedCode) {
            return response()->json([
                'message' => 'Неверный код активации'
            ], 422);
        }

        // Код верный, обновляем статус пользователя
        $user->activation_code = null; // Очищаем код после успешной активации
        $user->is_active = true;    // Предполагаем, что у пользователя есть поле is_activated
        $user->save();

        return response()->json([
            'message' => 'Активация успешно завершена'
        ]);
    }
}