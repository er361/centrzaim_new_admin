<?php

namespace App\Listeners;

use App\Events\UserActivated;
use App\Events\UserPaymentSuccessful;
use App\Events\UserRegistrationFinished;
use App\Services\PostbackService\PostbackServiceFactory;
use App\Services\PostbackService\PostbackServiceStepDecider;
use Throwable;

class SendUserPostback
{
    /**
     * Handle the event.
     *
     * @param UserPaymentSuccessful|UserActivated|UserRegistrationFinished $event
     * @return void
     * @throws \RuntimeException
     */
    public function handle(UserPaymentSuccessful|UserActivated|UserRegistrationFinished $event): void
    {
        if ($event->user->webmaster === null) {
            return;
        }

        $stepDecider = new PostbackServiceStepDecider();
        $postbackStep = $stepDecider->getPostbackStep($event->user);

        if ($event instanceof UserActivated) {
            $currentStep = PostbackServiceStepDecider::STEP_ACTIVATION;
        } elseif ($event instanceof UserRegistrationFinished) {
            $currentStep = PostbackServiceStepDecider::STEP_FILL;
        } elseif ($event instanceof UserPaymentSuccessful) {
            $currentStep = PostbackServiceStepDecider::STEP_PAYMENT;
        } else {
            return;
        }

        if ($postbackStep !== $currentStep) {
            return;
        }

        try {
            $postbackService = (new PostbackServiceFactory())
                ->createPostbackService(
                    $event->user->webmaster->source_id
                );

            $postbackService?->send($event->user);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
