import BaseView from "./base.js";

export default class extends BaseView {
    constructor() {
        super();

        this.initIframeListener();
        this.initTimer();
    }

    initTimer() {
        const $timerElement = $('.timer__wrapper');
        if (!$timerElement.length) {
            return;
        }

        const $afterTimerElement = $('.after-timer');
        $afterTimerElement.hide();

        let value = 0;
        const maxValue = 100;
        const totalTime = 10 * 1000;
        const stepTime = totalTime / maxValue;
        const $timerProgressElement = $('.timer__progress'); // replace with your own element ID

        const $steps = $('.timer__steps-step');
        const percentPerStep = 100 / $steps.length;

        function simulateLoading() {
            if (value < maxValue) {
                // increase value randomly
                const randomValue = Math.random() * 6;
                value += randomValue > 8 ? 10 : randomValue;

                if (value > 100) {
                    value = 100;
                }

                const textValue = Math.round(value) + '%';
                // update element text
                $timerProgressElement.text(textValue);
                // schedule next update

                const stepsToShow = Math.ceil(value / percentPerStep);
                $steps.slice(0, stepsToShow - 1).addClass('success');
                const nextLoadingLaunchIn = (Math.random() * stepTime * 3) + stepTime;

                setTimeout(() => {
                    $steps.slice(0, stepsToShow).attr('style', 'opacity:1');
                }, nextLoadingLaunchIn / 0.8);
                setTimeout(simulateLoading, nextLoadingLaunchIn);

                return;
            }

            $afterTimerElement.show();
            $timerElement.hide();
        }

        // start loading simulation
        setTimeout(simulateLoading, 0);
    }

    initIframeListener() {
        let eventMethod = window.addEventListener
            ? "addEventListener"
            : "attachEvent";

        let eventer = window[eventMethod];
        let messageEvent = eventMethod === "attachEvent"
            ? "onmessage"
            : "message";

        let redirectUrl = '/vitrina';
        console.log('redirectUrl', redirectUrl);

        eventer(messageEvent, function (e) {
            if (e.data === 'paymentFinished' || e.message === 'paymentFinished') {
                ym(99015882, 'reachGoal', 'complete_credit_card_add', null, function() {
                    console.log('Цель отправлена, выполняется перенаправление...');
                    window.location.replace(redirectUrl);
                });
            }
        });
    }
}