import BaseView from "./base.js";
import {Range} from "../components/range.js";
import {zeroPad} from "../utils.js";
import {LazyLoad} from "../components/lazy-load.js";

export default class extends BaseView {
    constructor() {
        super();

        this.initSlickSlider();
        this.initRegisterButtons();
        this.initOfferTiming();
        this.initFaq();

        Range.init();
        LazyLoad.init();
    }

    initSlickSlider() {
        let slidesToShow = [
            frontConfig.initialSlidesToShow,
            frontConfig.initialSlidesToShow - 1,
            frontConfig.initialSlidesToShow - 2 > 0 ? frontConfig.slidesToShow - 2 : frontConfig.slidesToShow - 1
        ];

        let slickConfiguration = {
            dots: true,
            infinite: false,
            speed: 300,
            slidesToShow: slidesToShow[0],
            slidesToScroll: slidesToShow[0],
            arrows: true,
            appendArrows: '.slider_nav',
            appendDots: '.slider_nav',
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: slidesToShow[0],
                        slidesToScroll: slidesToShow[0],
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 999,
                    settings: {
                        slidesToShow: slidesToShow[1],
                        slidesToScroll: slidesToShow[1],
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: slidesToShow[2],
                        slidesToScroll: slidesToShow[2],
                    }
                }
            ]
        };

        if (frontConfig.slidesDots) {
            slickConfiguration.prevArrow = frontConfig.slidesDots.prevArrow;
            slickConfiguration.nextArrow = frontConfig.slidesDots.nextArrow;
        } else {
            slickConfiguration.prevArrow = null;
            slickConfiguration.nextArrow = null;
        }

        $('.reviews_slider').slick(slickConfiguration);
    }

    initRegisterButtons() {
        $('.register_button_1').on('click touchend', function (e) {
            if ($('.payment_confirm_1:not(:checked)').length > 0) {
                e.preventDefault();
                toastr["error"]("Для продолжения оформления анкеты, пожалуйста, согласитесь с условиями.");
                return;
            }

            if (e.type === 'click') {
                e.preventDefault();
                window.open($(this).attr('href')).focus();
                window.location.href = frontConfig.loansUrl;
            }
        });

        $('.register_button_2').on('click touchend', function (e) {
            if ($('.payment_confirm_2:not(:checked)').length > 0) {
                e.preventDefault();
                toastr["error"]("Для продолжения оформления анкеты, пожалуйста, согласитесь с условиями.");
                return;
            }

            if (e.type === 'click') {
                e.preventDefault();
                window.open($(this).attr('href')).focus();
                window.location.href = frontConfig.loansUrl;
            }
        });
    }

    initOfferTiming() {
        const $offerTiming = $('.offer_timing');

        if (!$offerTiming.length) {
            return;
        }

        const offerDate = new Date();
        offerDate.setTime((new Date()).getTime() + (13 * 60 * 1000));

        const hours = zeroPad(offerDate.getHours(), 2);
        const minutes = zeroPad(offerDate.getMinutes(), 2);

        $offerTiming.text(hours + ':' + minutes);
    }

    initFaq() {
        $('.question__spoller-title').on('click', function () {
            $(this).closest('.question__spoller').toggleClass('question__spoller-opened');
        });
    }
}