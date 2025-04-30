<?php

namespace Tests\Feature\Front;

use App\Models\User;
use App\Services\ActivationService\ActivationServiceInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class ActivationControllerTest extends TestCase
{
    /**
     * Проверка работы активации, когда она выключена в настройках.
     *
     * @return void
     */
    public function testIndexWhenDisabled()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        setting(['is_phone_verification_enabled' => '0'])->save();

        $response = $this->get(route('account.activation.index'));
        $response->assertStatus(Response::HTTP_FOUND);
    }

    /**
     * Проверка открытия страницы и отправки кода при включенной активации.
     * @return void
     */
    public function testIndex()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        setting(['is_phone_verification_enabled' => '1'])->save();

        $this->mock(ActivationServiceInterface::class, function ($mock) use ($user) {
            /** @var MockInterface $mock */
            $mock->expects('sendCode')
                ->once()
                ->with($user);
        });

        $response = $this->get(route('account.activation.index'));
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Проверка переотправки кода.
     * @return void
     */
    public function testResend()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->mock(ActivationServiceInterface::class, function ($mock) use ($user) {
            /** @var MockInterface $mock */
            $mock->expects('resendCode')
                ->once()
                ->with($user);
        });

        $response = $this->get(route('account.resend'));
        $response->assertStatus(Response::HTTP_FOUND);
    }

    /**
     * Проверка ввода валидного кода.
     * @return void
     */
    public function testStoreValid()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $code = Str::random();

        $this->mock(ActivationServiceInterface::class, function ($mock) use ($user, $code) {
            /** @var MockInterface $mock */
            $mock->expects('validateCode')
                ->once()
                ->with($user, $code)
                ->andReturn(true);
        });

        $response = $this->post(route('account.activation.store'), [
            'activation_code' => $code
        ]);
        $response->assertStatus(Response::HTTP_FOUND);
    }

    /**
     * Проверка ввода невалидного кода.
     * @return void
     */
    public function testStoreInvalid()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $code = Str::random();

        $this->mock(ActivationServiceInterface::class, function ($mock) use ($user, $code) {
            /** @var MockInterface $mock */
            $mock->expects('validateCode')
                ->once()
                ->with($user, $code)
                ->andReturn(false);
        });

        $response = $this->post(route('account.activation.store'), [
            'activation_code' => $code
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }
}