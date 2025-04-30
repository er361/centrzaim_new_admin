<?php

namespace Feature\Api\V1;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use WithFaker;

    /**
     * @param string|null $configToken
     * @param string|null $requestToken
     * @param int $responseCode
     * @return void
     * @dataProvider unauthorizedDataProvider
     */
    public function testUnauthorized(?string $configToken, ?string $requestToken, int $responseCode): void
    {
        Config::set('app.api_token', $configToken);

        $response = $this->get(route('api.users.index'), [
            'X-TOKEN' => $requestToken,
        ]);

        $response->assertStatus($responseCode);
    }

    /**
     * Проверка ошибок получения списка пользователей.
     * @return array[]
     */
    public static function unauthorizedDataProvider(): array
    {
        return [
            'не передан токен' => ['abc', null, Response::HTTP_BAD_REQUEST],
            'не установлен токен' => [null, 'abc', Response::HTTP_INTERNAL_SERVER_ERROR],
            'не установлен и не передан токен' => [null, null, Response::HTTP_BAD_REQUEST],
            'неверный токен' => ['abc', 'def', Response::HTTP_UNAUTHORIZED],
        ];
    }

    /**
     * Проверка успешного получения списка пользователей.
     * @return void
     */
    public function testOk(): void {
        $token = 'abc';

        Config::set('app.api_token', $token);

        $url = route('api.users.index', [
            'payment_card_number' => '1234567890123456',
        ]);

        $response = $this->get($url, [
            'X-TOKEN' => $token,
        ]);

        $response->assertOk();
    }

}