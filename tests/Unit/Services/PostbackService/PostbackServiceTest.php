<?php

namespace Tests\Unit\Services\PostbackService;

use App\Models\Postback;
use App\Models\User;
use App\Services\PostbackService\PostbackCostService;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\PostbackService\PostbackService;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;

class PostbackServiceTest extends TestCase
{
    use WithFaker;

    /**
     * Проверяет корректную отправку постбэка по пользователю.
     * @return void
     */
    public function testSendPostback(): void
    {
        $user = User::factory()->createOne();
        $cost = $this->faker->randomNumber(3, true);

        /** @var PostbackCostService $postbackCostService */
        $postbackCostService = $this->mock(PostbackCostService::class, function ($mock) use ($user, $cost) {
            /** @var MockInterface $mock */
            $mock->shouldReceive('getCost')
                ->once()
                ->with($user)
                ->andReturn($cost);
        });

        /** @var PostbackNotifyServiceInterface $postbackNotifyService */
        $postbackNotifyService = $this->mock(PostbackNotifyServiceInterface::class, function ($mock) use ($user) {
            /** @var MockInterface $mock */
            $mock->shouldReceive('send')
                ->once()
                ->with($user);
        });

        self::assertFalse($user->postbacks()->exists());

        $postbackService = new PostbackService($postbackNotifyService, $postbackCostService);
        $postbackService->send($user);

        /** @var null|Postback $postback */
        $postback = $user->postbacks()->first();
        self::assertNotNull($postback);
        self::assertEquals($user->unique_id, $postback->remote_user_id);
        self::assertEquals($cost, $postback->cost);
        self::assertNotNull($postback->sent_at);
    }

    /**
     * Проверяет корректную отправку постбэка по пользователю.
     * @return void
     */
    public function testDoesNotSendIfUserWithSamePhoneExists(): void
    {
        $user = User::factory()->createOne();
        User::factory()->createOne([
            'mphone' => $user->mphone,
        ]);

        /** @var PostbackCostService $postbackCostService */
        $postbackCostService = $this->mock(PostbackCostService::class, function ($mock) use ($user) {
            /** @var MockInterface $mock */
            $mock->shouldNotHaveBeenCalled();
        });

        /** @var PostbackNotifyServiceInterface $postbackNotifyService */
        $postbackNotifyService = $this->mock(PostbackNotifyServiceInterface::class, function ($mock) use ($user) {
            /** @var MockInterface $mock */
            $mock->shouldNotHaveBeenCalled();
        });

        self::assertFalse($user->postbacks()->exists());

        $postbackService = new PostbackService($postbackNotifyService, $postbackCostService);
        $postbackService->send($user);

        self::assertFalse($user->postbacks()->exists());
    }

    /**
     * Проверяет корректную отправку постбэка по пользователю.
     * @return void
     */
    public function testDoesNotSendIfUserHasPostbacks(): void
    {
        $user = User::factory()->createOne();
        Postback::factory()->for($user)->createOne();

        /** @var PostbackCostService $postbackCostService */
        $postbackCostService = $this->mock(PostbackCostService::class, function ($mock) use ($user) {
            /** @var MockInterface $mock */
            $mock->shouldNotHaveBeenCalled();
        });

        /** @var PostbackNotifyServiceInterface $postbackNotifyService */
        $postbackNotifyService = $this->mock(PostbackNotifyServiceInterface::class, function ($mock) use ($user) {
            /** @var MockInterface $mock */
            $mock->shouldNotHaveBeenCalled();
        });

        self::assertEquals(1, $user->postbacks()->count());

        $postbackService = new PostbackService($postbackNotifyService, $postbackCostService);
        $postbackService->send($user);

        self::assertEquals(1, $user->postbacks()->count());
    }
}