<?php


namespace App\Services\PostbackService;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PDOException;
use Throwable;

class PostbackService implements PostbackNotifyServiceInterface
{
    /**
     * @var PostbackCostService
     */
    protected PostbackCostService $postbackCostService;

    /**
     * @var PostbackNotifyServiceInterface
     */
    protected PostbackNotifyServiceInterface $postbackNotifyService;

    /**
     * PostbackService constructor.
     * @param PostbackNotifyServiceInterface $postbackNotifyService
     * @param PostbackCostService $postbackCostService
     */
    public function __construct(PostbackNotifyServiceInterface $postbackNotifyService, PostbackCostService $postbackCostService)
    {
        $this->postbackNotifyService = $postbackNotifyService;
        $this->postbackCostService = $postbackCostService;
    }

    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $isUsersWithSamePhoneExists = User::query()
            ->where('id', '!=', $user->id)
            ->where('mphone', $user->mphone)
            ->exists();

        if ($isUsersWithSamePhoneExists) {
            return;
        }

        if ($user->postbacks()->exists()) {
            return;
        }

        // В случае повторной отправки постбэка, на стороне ПП это может считаться как дубль
        // Создаем конверсию в базе заранее, чтобы если что словить дубль по unique

        try {
            $cost = $this->postbackCostService->getCost($user);
        } catch (Throwable $e) {
            report($e);
            $cost = null;
        }

        try {
            $postback = $user->postbacks()->create([
                'cost' => $cost,
                'remote_user_id' => $user->unique_id,
            ]);
        } catch (PDOException $e) {
            // Дубль существующей конверсии
            if (Str::contains($e->getMessage(), 'Duplicate entry')) {
                return;
            }

            throw $e;
        }

        Log::debug('Планируем отправить постбэк по пользователю.', [
            'user_id' => $user->id,
        ]);

        try {
            $this->postbackNotifyService->send($user);
        } catch (Throwable $e) {
            report($e);
        }

        $postback->update([
            'sent_at' => Carbon::now(),
        ]);
    }

    /**
     * @return PostbackCostService
     */
    public function getPostbackCostService(): PostbackCostService
    {
        return $this->postbackCostService;
    }

    /**
     * @return PostbackNotifyServiceInterface
     */
    public function getPostbackNotifyService(): PostbackNotifyServiceInterface
    {
        return $this->postbackNotifyService;
    }
}