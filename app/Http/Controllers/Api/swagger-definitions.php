<?php
/**
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     description="Используйте токен из личного кабинета: Bearer {token}",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     securityScheme="sanctum",
 * )
 */

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="API активации",
 *         description="API для управления кодами активации пользователей",
 *     ),
 *     @OA\Server(
 *         description="API Server",
 *         url="/api"
 *     ),
 *     security={
 *         {"sanctum": {}}
 *     }
 * )
 */