//$('document').ready(function(){
//    // Selectinng Country in Nav Menu
//    $('#drop_coutry li a').on('click', function (e) {
//        e.preventDefault();
//        var link_text = $(this).html();
//        $('#country_block').html(link_text);
//        // Catching Location
//        //window.location.href= "/c/" + $(this).closest('li').data('item-value');
//    });
//});
$('document').ready(function(){






    function genCount() {
        var def = 19433;
        var ts = 1379591528;
        var ti = new Date().getTime();
        var tsi = parseInt(ti.toString().substring(0, 10));

        var rand = 5 + Math.floor(Math.random() * 10);

        var ta = def + parseInt((tsi - ts) / 10000);
        jQuery('#credit_count').text(ta);
    }
    genCount();
    setInterval(genCount, 6478);





});

$('document').ready(function(){
    // progress Circle
    $('.dial').each(function() {
        var $this = $(this);
        var myVal = $this.attr("rel");
        // alert(myVal);
        $this.knob({
            "fgColor": "#f9c100",
            "bgColor": "#eaedf4"
        });
        $({
            value: 0
        }).animate({
            value: myVal
        }, {
            duration: 15000,
            easing: 'swing',
            step: function() {
                $this.val(Math.ceil(this.value)).trigger('change');
            },
            complete: function () {
                $('.step_1').animate({
                    opacity: "toggle"
                }, 500, "swing", function () {
                    $('.step_2').animate({
                        opacity: "toggle"
                    }, 500, "swing");
                });
            }
        })
    });
});

//$('document').ready(function () {
//
//    var urlRegStr = window.location.pathname;
//
//    function callMainModal() {
//        // Не показываем модальку на странице подтверждения регистрации /registration/* и отписки /unsubscribe/*
//        if ( (urlRegStr.indexOf("registration") < 0) && (urlRegStr.indexOf("unsubscribe") < 0)) {
//            $('#modal_window_main').addClass('active');
//            $('body').css({
//                'overflow': 'hidden'
//            });
//        }
//    }
//
//    //calling of Modal in 1 minute
//
//    setTimeout(callMainModal, 60000)
//
//    var mouse = {x: 0, y: 0};
//
//    function cursorPosition(e) {
//        mouse.x = e.clientX || e.pageX;
//        mouse.y = e.clientY || e.pageY
//        //console.log(mouse.x);
//        //console.log(mouse.y);
//
//        if (mouse.y <= 15) {
//            callMainModal();
//        }
//    }
//
//    document.addEventListener('mousemove', cursorPosition, false);
//
//
//    $('.toggleModal').on('click', function () {
//        $(this).parents('.modal_fader').removeClass('active');
//        $('body').css({
//            'overflow': 'scroll'
//        })
//        document.removeEventListener('mousemove', cursorPosition);
//    });
//
//
//    $('#main_modal_popup_btn').click(function(e){
//        console.log("Main popup click");
//        e.preventDefault();
//        yaCounter30184049.reachGoal("MAIN_POPUP_BTN_CLICK");
//        window.location.href = $(this).attr('href');
//    });
//
//    $('#seo_modal_popup_btn').click(function (e) {
//        console.log("Seo popup click");
//        e.preventDefault();
//        yaCounter30184049.reachGoal("KEYWORD_POPUP_BTN_CLICK");
//        window.location.href = $(this).attr('href');
//    });
//});
$('document').ready(function(){
    $('#place_equal, #place_inequal ').on('change', function(){
        //alert()//
        if( $("#place_equal").is(":checked") ){
            $('#js_addition_place').find('input').attr('disabled', true);
            //console.log('place_equal')
        }else if ( $("#place_inequal").is(":checked") ){
            $('#js_addition_place').find('input').removeAttr('disabled');
            //console.log('place_inequal')
        }
    })
});

$('document').ready(function () {



    var moneySlider = $('#money_slider');
    var moneyAmount = $('#money_amount');
    var termSlider = $('#term_slider');
    var termAmount = $('#term_amount');
    var moneyMap =  ['1.000 рублей', '2.000 рублей', '3.000 рублей', '4.000 рублей', '5.000 рублей', ' 6.000 рублей', ' 7.000 рублей', ' 8.000 рублей', ' 9.000 рублей', ' 10.000 рублей', ' 11.000 рублей', ' 12.000 рублей', ' 13.000 рублей', ' 14.000 рублей', ' 15.000 рублей', ' 16.000 рублей', ' 17.000 рублей', ' 18.000 рублей', ' 19.000 рублей', ' 20.000 рублей', ' 21.000 рублей', ' 22.000 рублей', ' 23.000 рублей', ' 24.000 рублей', ' 25.000 рублей', ' 26.000 рублей', ' 27.000 рублей', ' 28.000 рублей', ' 29.000 рублей', ' 30.000 рублей', ' 35.000 рублей', ' 40.000 рублей', ' 45.000 рублей', ' 50.000 рублей', ' 55.000 рублей', ' 60.000 рублей', ' 65.000 рублей', ' 70.000 рублей', ' 75.000 рублей', ' 80.000 рублей', ' 85.000 рублей', ' 90.000 рублей', ' 95.000 рублей', ' 100.000 рублей', ' 110.000 рублей', ' 120.000 рублей', ' 130.000 рублей', ' 140.000 рублей', ' 150.000 рублей', ' 160.000 рублей', ' 170.000 рублей', ' 180.000 рублей', ' 190.000 рублей', ' 200.000 рублей', ' 210.000 рублей', ' 220.000 рублей', ' 230.000 рублей', ' 240.000 рублей', ' 250.000 рублей', ' 260.000 рублей', ' 270.000 рублей', ' 280.000 рублей', ' 290.000 рублей', ' 300.000 рублей', '  325.000 рублей', ' 350.000 рублей', ' 375.000 рублей', ' 400.000 рублей', ' 425.000 рублей', ' 450.000 рублей', ' 475.000 рублей', ' 500.000 рублей', ' 525.000 рублей', ' 550.000 рублей', ' 575.000 рублей', ' 600.000 рублей', ' 625.000 рублей', ' 650.000 рублей', ' 675.000 рублей', ' 700.000 рублей', ' 725.000 рублей', ' 750.000 рублей', ' 775.000 рублей', ' 800.000 рублей', ' 1.000.000 рублей', ' 1.200.000 рублей', ' 1.400.000 рублей', ' 1.600.000 рублей', ' 1.800.000 рублей', ' 2.000.000 рублей', ' 2.200.000 рублей', ' 2.400.000 рублей', ' 2.600.000 рублей', ' 2.800.000 рублей', ' 3.000.000 рублей'];
    var termMap = [ '1 неделя', '2 недели', '3 недели', '4 недели', '1 месяц', '2 месяца', '3 месяца', '4 месяца', '5 месяцев', '6 месяцев', '7 месяцев', '8 месяцев', '9 месяцев', '10 месяцев', '11 месяцев', '1 год', '2 года', '3 года', '4 года', '5 лет', '6 лет', '7 лет', '8 лет', '9 лет', '10 лет'];

    var replaceValue = function(obj, arr, index){
        //console.log( arr[index] );
        obj.val( arr[index] );
    };

    var fillTheBox = function(input, box){
        var boxVal = $(input).val();
        $(box).html(boxVal);
    };

    var nextVal = function(){
        var curVal = moneySlider.slider( "value" );
        if (curVal >= moneyMap.length - 1) {
            return false
        } else {
            moneySlider.slider("value", ++curVal);
            console.log(curVal);
            moneyAmount.val(moneyMap[curVal]);
            fillTheBox('#money_amount', '#money_box');
        }
    };
    var prevVal = function(){
        var curVal = moneySlider.slider( "value" );
        if (curVal <= 0) {
            return false
        } else {
            moneySlider.slider("value", --curVal);
            console.log(curVal);
            moneyAmount.val(moneyMap[curVal]);
            fillTheBox('#money_amount', '#money_box');
        }
    };


    var nextTerm = function(){
        var curVal = termSlider.slider( "value" );
        if (curVal >= termMap.length - 1) {
            return false
        } else {
            termSlider.slider("value", ++curVal);
            console.log(curVal);
            termAmount.val(termMap[curVal]);
            fillTheBox('#term_amount', '#term_box');
        }
    };
    var prevTerm = function(){
        var curVal = termSlider.slider( "value" );
        if (curVal <= 0) {
            return false
        } else {
            termSlider.slider("value", --curVal);
            console.log(curVal);
            termAmount.val(termMap[curVal]);
            fillTheBox('#term_amount', '#term_box');
        }
    };


    var hintSlider = function(val){
        var sliderHead = $('.slider_head');
        var moneyBox = $('#money_box');
        var percentRate = $('#percent_rate');
        var creditHint = $('#credit_hint');
        var creditTitleWrap = $('#credit_title_wrap');
        var creditHintWrap = $('#credit_hint_wrap');

        creditTitleWrap.hide();
        creditHintWrap.show();
        sliderHead.css({padding: '0', 'border-bottom': 'transparent'})

        // sliderHead Colors
        if ( val >=0 && val <=14 ){
            sliderHead.addClass('green');
            sliderHead.removeClass('yellow');
            sliderHead.removeClass('red');
            moneyBox.css({color:'#81bc42'});
        }
        if ( val >=15 && val <= 33 ){
            sliderHead.addClass('yellow');
            sliderHead.removeClass('green');
            sliderHead.removeClass('red');
            moneyBox.css({color:'#ffa72e'});
        }
        if ( val >33 ){
            sliderHead.addClass('red');
            sliderHead.removeClass('yellow');
            sliderHead.removeClass('green');
            moneyBox.css({color:'#ce4947'});
        }


        //sliderHead Texts
        if ( val >=0 && val <=6 /*от 1 до 7000*/){
            percentRate.html('97%');
            creditHint.html(' Автоматическое <br> одобрение');
        }
        if ( val >=7 && val <=14 /*от 8000 до 15000*/ ){
            percentRate.html('94%');
            creditHint.html(' Может понадобиться <br> паспорт');
        }
        if ( val >=15 && val <=29 /*от 16000 до 30000*/ ){
            percentRate.html('84%');
            creditHint.html(' Нужен только <br>паспорт ');

        }
        if ( val >=30 && val <=33 /*от 31000 до 50000*/ ){
            percentRate.html('72%');
            creditHint.html('  Может понадобиться подтверждение места работы  ');
        }
        if ( val >=34 && val <=43 /*от 51000 до 100000*/ ){
            percentRate.html('64%');
            creditHint.html('   Может понадобиться справка о доходах (или под залог)  ');
        }
        if ( val >=44 && val <=63 /*от 101000 до 300000*/ ){
            percentRate.html('51%');
            creditHint.html('   Необходима справка о доходах (или под залог) ');
        }
        if ( val >=64 && val <=84 /*от 301000 до 1000000*/ ){
            percentRate.html('37%');
            creditHint.html('   Нужна справка 2-ндфл  (или под залог) ');
        }
        if ( val >=85 /*от 1001000 до 3000000*/ ){
            percentRate.html('99%');
            creditHint.html('   Требуется обеспечение кредита (залог)');
        }


    };




////////////////////////////////////////////////////////
    moneySlider.slider({
        range: "min",
        value: moneyMap.length - 1,
        min: 0,
        animate: true,
        max: moneyMap.length - 1,
        slide: function( event, ui ) {
            //moneyAmount.val( ui.value );
            replaceValue(moneyAmount, moneyMap, ui.value );
            fillTheBox('#money_amount', '#money_box');
            hintSlider(ui.value);
        }
    });

    moneyAmount.val(moneyMap[moneyMap.length - 1]);
    fillTheBox('#money_amount', '#money_box');

    $('[data-change="money"]').on('click', function(){
        if( $(this).data('action') === 'plus' ){
            nextVal();
        }else{
            prevVal();
        }
    });
////////////////////////////////////////////////////////
    termSlider.slider({
        range: "min",
        value: termMap.length-1,
        min: 0,
        animate: true,
        max: termMap.length - 1,
        slide: function( event, ui ) {
            //moneyAmount.val( ui.value );
            replaceValue( termAmount, termMap, ui.value );
            fillTheBox('#term_amount', '#term_box');
        }
    });

    termAmount.val(termMap[termMap.length-1]);
    fillTheBox('#term_amount', '#term_box');

    $('[data-change="term"]').on('click', function(){
        if( $(this).data('action') === 'plus' ){
            nextTerm();
        }else{
            prevTerm();
        }
    });

////////////////////////////////////////////////////////
    setTimeout(function(){
        var i = moneyMap.length-1;
        var myLoop = function () {
            $('.slider_range').addClass('bounceIn');
            setTimeout(function () {
              //  console.log ( moneyMap[i] );
                i -= 1;
                if ( i >= 4 ) {
                    myLoop();
                    moneyAmount.val( moneyMap[i] );
                    fillTheBox('#money_amount', '#money_box');
                    moneySlider.slider( "option", "value", i );
                }
            }, 20)
        };
        myLoop();

    }, 1000);


    setTimeout(function(){
        var j = termMap.length-1;
        var myHoop = function () {
            setTimeout(function () {
               // console.log ( termMap[j] );
                j -= 1;
                if ( j >= 1 ) {
                    myHoop();
                    termAmount.val( termMap[j] );
                    fillTheBox('#term_amount', '#term_box');
                    termSlider.slider( "option", "value", j );
                }
            }, 30)
        };
        myHoop();

    }, 1800);


/*
    var reverseLoop = function (val) {
        setTimeout(function () {
            console.log (val);
            $("#money_amount").val( val );
            $("#money_slider").slider('value', val);
            val -= 1;
            if (val >= 0) {
                reverseLoop(val);
            }
        }, 1)
    };

    reverseLoop(moneyMap.length - 1)

 */
////////////////////////////////////////////////////////

//    /*
//     * Initital Animation of Slider values
//     =====================================
//     */
//    var ifVal = function (ifValue) {
//        if (ifValue <= 31000) {
//            $('.term_button[data-term="7"]').removeClass('disabled');
//            $('.term_button[data-term="14"]').addClass('disabled');
//            $('.term_button[data-term="30"]').addClass('disabled');
//            $('.term_button[data-term="180"]').addClass('disabled');
//            $('.term_button[data-term="360"]').addClass('disabled');
//            $('.term_button').removeClass('active');
//            //$('[name="default_term"]').val(30);
//        }
//        if (ifValue >= 31000 && ifValue <= 50000) {
//            $('.term_button[data-term="7"]').removeClass('disabled');
//            $('.term_button[data-term="14"]').addClass('disabled');
//            $('.term_button[data-term="30"]').addClass('disabled');
//            $('.term_button[data-term="180"]').addClass('disabled');
//            $('.term_button[data-term="360"]').addClass('disabled');
//            $('.term_button').removeClass('active');
//            //$('[name="default_term"]').val(30);
//        }
//        if (ifValue >= 50000 && ifValue <= 100000) {
//            $('.term_button[data-term="7"]').removeClass('disabled');
//            $('.term_button[data-term="14"]').removeClass('disabled');
//            $('.term_button[data-term="30"]').removeClass('disabled');
//            $('.term_button[data-term="180"]').addClass('disabled');
//            $('.term_button[data-term="360"]').addClass('disabled');
//            $('.term_button').removeClass('active');
//            //$('[name="default_term"]').val(30);
//        }
//        if (ifValue >= 101000 && ifValue <= 500000) {
//            $('.term_button[data-term="7"]').addClass('disabled');
//            $('.term_button[data-term="14"]').addClass('disabled');
//            $('.term_button[data-term="30"]').removeClass('disabled');
//            $('.term_button[data-term="180"]').removeClass('disabled');
//            $('.term_button[data-term="360"]').addClass('disabled');
//            $('.term_button').removeClass('active');
//            $('[name="default_term"]').val(180);
//        }
//        if (ifValue >= 500000 && ifValue <= 1000000) {
//            $('.term_button[data-term="7"]').addClass('disabled');
//            $('.term_button[data-term="14"]').addClass('disabled');
//            $('.term_button[data-term="30"]').removeClass('disabled');
//            $('.term_button[data-term="180"]').removeClass('disabled');
//            $('.term_button[data-term="360"]').addClass('disabled');
//            $('.term_button').removeClass('active');
//            //$('[name="default_term"]').val(360);
//        }
//        if (ifValue >= 1001000 && ifValue <= 3000000) {
//            $('.term_button[data-term="7"]').addClass('disabled');
//            $('.term_button[data-term="14"]').addClass('disabled');
//            $('.term_button[data-term="30"]').addClass('disabled');
//            $('.term_button[data-term="180"]').removeClass('disabled');
//            $('.term_button[data-term="360"]').removeClass('disabled');
//            $('.term_button').removeClass('active');
//            //$('[name="default_term"]').val(360);
//        }
//    };
//
//
//    var ifAmountMarker = function (mark) {
//
//        if (mark >= 0 && mark <= 100000) {
//            $('[data-mark="30"]').addClass('active');
//            $('[data-mark="180"]').removeClass('active');
//            $('[data-mark="360"]').removeClass('active');
//            $('[data-mark="1500"]').removeClass('active');
//            $('[data-mark="3000"]').removeClass('active');
//        }
//
//        if (mark >= 100001 && mark <= 300000) {
//            $('[data-mark="30"]').addClass('active');
//            $('[data-mark="180"]').addClass('active');
//            $('[data-mark="360"]').removeClass('active');
//            $('[data-mark="1500"]').removeClass('active');
//            $('[data-mark="3000"]').removeClass('active');
//        }
//
//        if (mark >= 300001 && mark <= 800000) {
//            $('[data-mark="30"]').addClass('active');
//            $('[data-mark="180"]').addClass('active');
//            $('[data-mark="360"]').addClass('active');
//            $('[data-mark="1500"]').removeClass('active');
//            $('[data-mark="3000"]').removeClass('active');
//        }
//
//        if (mark >= 800001 && mark <= 3000000) {
//            $('[data-mark="30"]').addClass('active');
//            $('[data-mark="180"]').addClass('active');
//            $('[data-mark="360"]').addClass('active');
//            $('[data-mark="1500"]').addClass('active');
//            $('[data-mark="3000"]').removeClass('active');
//        }
//
//        if (mark == 3000000) {
//            $('[data-mark="30"]').addClass('active');
//            $('[data-mark="180"]').addClass('active');
//            $('[data-mark="360"]').addClass('active');
//            $('[data-mark="1500"]').addClass('active');
//            $('[data-mark="3000"]').removeClass('active');
//        }
//
//    };
//
//
////// Initializing start point of iterator
////    var i = 0;
////// Creating a function with iteration
////    var myLoop = function () {
////        setTimeout(function () {
////            console.log (i);
////            $("#money_amount").val( i );
////            $("#money_slider").slider( 'value', i );
////            i += 50000;
////// Initializing end point of iterator
////            if (i <= 3000000) {
////                myLoop();
////                ifVal(i);
////                ifAmountMarker(i);
////            }
////            if (i == 3000000) {
////                var r = i;
////                //console.log(r + 'авыавыа');
////                //reverseLoop(r);
////                ifAmountMarker(r)
////            }
////// Setting the delay between iterations
////        }, 0)
////    };
////    myLoop();
//////
////
////    var reverseLoop = function (val) {
////        setTimeout(function () {
////            console.log (val);
////            $("#money_amount").val( val );
////            $("#money_slider").slider('value', val);
////            val -= 50000;
////            if (val >= 5000) {
////                reverseLoop(val);
////                ifVal(val);
////                ifAmountMarker(val)
////            }
////        }, 10)
////    };
//
//
//    /*
//     * Creation of Slider
//     =====================================
//     */
//
//    var valMap =  [1000, 2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000, 21000, 22000, 23000, 24000, 25000, 26000, 27000, 28000, 29000, 30000, 35000, 40000, 45000, 50000, 55000, 60000, 65000, 70000, 75000, 80000, 85000, 90000, 95000, 100000, 110000, 120000, 130000, 140000, 150000, 160000, 170000, 180000, 190000, 200000, 210000, 220000, 230000, 240000, 250000, 260000, 270000, 280000, 290000, 300000, 300000, 325000, 350000, 375000, 400000, 425000, 450000, 475000, 500000, 525000, 550000, 575000, 600000, 625000, 650000, 675000, 700000, 725000, 750000, 775000, 800000, 1000000, 1200000, 1400000, 1600000, 1800000, 2000000, 2200000, 2400000, 2600000, 2800000, 3000000]
//
//    var fakeMap = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95];
//
//    var money = $("#money_amount");
//    var slider = $("#money_slider");
//
//    slider.slider({
//        range: "min",
//        value: valMap.length-1,
//        min: 0,
//        max: valMap.length - 1,
//        animate: true,
//        create: function (event, ui) {
//            //console.log('slider created');
//        },
//        change: function (event, ui) {
//            //console.log('slider changed');
//        },
//        slide: function (event, ui) {
//            money.val(valMap[ui.value]);
//            ifVal(valMap[ui.value]);
//            ifAmountMarker(valMap[ui.value]);
//        }
//    });
//
//    money.val(valMap[95]);
//    $('.meter_item').addClass('active');
//
//    setTimeout(function(){
//        var i = valMap.length-1;
//        var j = fakeMap.length-1;
//        var myLoop = function () {
//            setTimeout(function () {
//                console.log ( valMap[i] );
//                i -= 1;
//                j -= 1;
//                if ( i >= 14 ) {
//                    myLoop();
//                    money.val( valMap[i] );
//                    slider.slider( "option", "value", fakeMap[j] );
//                    ifVal( valMap[i] );
//                    ifAmountMarker( valMap[i] );
//                }
//            }, 30)
//        };
//        myLoop();
//
//    }, 2000)
//
//
//
//
//
//    // Binding data between slider and #amount field
//
//    //$("#money_amount").on('keyup', (function () {
//    //    var newVal = $(this).val();
//    //    $("#money_slider").slider("value", newVal);
//    //}));
//
//    //$('.term_button').on('mouseover', function () {
//    //    $('.term_button').removeClass('has-error');
//    //});
//
//    $('.term_button').on('click', function () {
//        $('.term_button').removeClass('active');
//        $('[name="term"]').val($(this).data('term'));
//        $(this).addClass('active')
//    });


    /*
     ========================================================
     */


    $('#paydayru_form').submit(
        function (e) {

            e.preventDefault();

            /* var isOk = true;
             */
            /* проверяем если вбран срок */
            /*
             if($('[name="term"]').val() == ""){
             $(".term_button:not(.disabled)").addClass('has-error');
             isOk = false;
             }*/

            /*if (! $('[name="agree_with_terms"]').is(':checked'))
             {
             $('[name="agree_with_terms"]').parent('label').css('color', 'red');
             //$('[name="agree_with_terms"]').parent('label').css('border', '1px dotted rgb(255, 0, 0)');
             $('[name="agree_with_terms"]').parent('label').css('background', 'rgba(255, 0, 0, 0.1)');
             isOk = false;
             }*/

            //console.log($term_amount);

            $amount_value = $('#money_amount').val().match(/([.\d]+)/)[0].replace('.', '').replace('.', '');

            //console.log($amount_value[0].replace('.', '').replace('.', ''));

            var term = 30;

            var $term_amount = $('#term_amount').val().match(/(\d+)(\s?)([А-Яа-яЁё]+)/);
            console.log($term_amount[1], $term_amount[3]);
            switch ($term_amount[3]){
                case 'неделя':
                case 'недели':
                    term = 7 * $term_amount[1];
                    break;
                case 'месяц':
                case 'месяца':
                case 'месяцев':
                    term = 30 * $term_amount[1];
                    break;
                case 'год':
                case 'года':
                case 'лет':
                    term = 365 * $term_amount[1];
                    break;
                default:
                    term = 365;
                    break;
            }

            //if(isOk) {
            yaCounter30184049.reachGoal("GET_MONEY_BTN_CLICK");
             window.location.href = '/form?amount=' + parseInt($amount_value) + '&term=' + term;
            //}
        });

    var $slider = $('#feature_money_slider');

    $slider.slider({
        range: 'min',
        value: 10000,
        min: 1000,
        max: 100000,
        step: 1000,
        slide: function (event, ui) {
            $('#feature_money_amount').val(ui.value);
            $slider.find('.indicator_value').text(ui.value);
            if (ui.value <= 15000) {
                $('.term_btn[data-term="7"]').removeClass('disabled');
                $('.term_btn[data-term="14"]').removeClass('disabled');
                $('.term_btn[data-term="30"]').removeClass('disabled');
                $('.term_btn[data-term="180"]').addClass('disabled');
                $('.term_btn[data-term="360"]').addClass('disabled');
                $('.term_btn').removeClass('active');
                $('[name="default_term"]').val(30);
            }
            if (ui.value >= 16000 && ui.value <= 30000) {
                $('.term_btn[data-term="7"]').addClass('disabled');
                $('.term_btn[data-term="14"]').addClass('disabled');
                $('.term_btn[data-term="30"]').removeClass('disabled');
                $('.term_btn[data-term="180"]').addClass('disabled');
                $('.term_btn[data-term="360"]').addClass('disabled');
                $('.term_btn').removeClass('active');
                $('[name="default_term"]').val(30);
            }
            if (ui.value >= 31000 && ui.value <= 50000) {
                $('.term_btn[data-term="7"]').addClass('disabled');
                $('.term_btn[data-term="14"]').addClass('disabled');
                $('.term_btn[data-term="30"]').addClass('disabled');
                $('.term_btn[data-term="180"]').removeClass('disabled');
                $('.term_btn[data-term="360"]').removeClass('disabled');
                $('.term_btn').removeClass('active');
                $('[name="default_term"]').val(180);
            }
            if (ui.value >= 51000 && ui.value <= 100000) {
                $('.term_btn[data-term="7"]').addClass('disabled');
                $('.term_btn[data-term="14"]').addClass('disabled');
                $('.term_btn[data-term="30"]').addClass('disabled');
                $('.term_btn[data-term="180"]').addClass('disabled');
                $('.term_btn[data-term="360"]').removeClass('disabled');
                $('.term_btn').removeClass('active');
                $('[name="default_term"]').val(360);
            }
            $('[name="term"]').val("");
            //$('.term_button').removeClass('has-error');
        }
    });

    $('#feature_money_amount').val($slider.slider('value'));

    $('.term_btn').on('click', function () {
        $('.term_btn').removeClass('active');
        $('[name="term"]').val($(this).data('term'));
        $(this).addClass('active')
    });

    $slider.find('.ui-slider-handle').append('<span class="slider_amount_indicator"><span class="indicator_value">' + $slider.slider('value') + '</span><i class="fa fa-rouble slider_rouble_icon"></i></span>');

    $('#paydayru_form_feature').on('submit', function (e) {

        e.preventDefault();
        var term = parseInt($('[name="term"]').val());

        if (isNaN(term)) term = parseInt($('[name="default_term"]').val());
        window.location.href = '/form?amount=' + parseInt($('[name="amount"]').val()) + '&term=' + term;
    });

    var $locationSearchForm = $('.location_search_form');

    $locationSearchForm.on('submit', function (e) {
        e.preventDefault();
    });

    $locationSearchForm.find('.location_search_input').on('keyup', function () {
        var query = $(this).val().toLowerCase();
        searchCity(query);
    });

    function searchCity(city) {
        if (city == '') {
            //$('.search_result_item').css({'display': 'none'});
            $('.cities_list_block').removeClass('hidden');
            return;
        }

        //$('.cities_list_block').addClass('hidden');
        $('.block_header').css({'display': 'none'});
        $('.block_content').css({'display': 'none'});
        $('.search_result_item').css({'display': 'none'});
        //$('.search_result_item[data-name *= "' + city + '"]').css({'display': 'block'});

        var cityElement = $('.search_result_item[data-name *= "'+city+'"]');
        var elemBlock_content = $('.search_result_item[data-name *= "'+city+'"]').parents('.block_content');
        var elemBlock_header = elemBlock_content.prev('.block_header');

        elemBlock_content.css({'display': 'block'});
        elemBlock_header.css({'display': 'block'});
        cityElement.css({'display': 'block'});
    }
});

$('document').ready(function(){
    //Touch Punch (draggable Slider)
    $('#money_slider').draggable();
});

//# sourceMappingURL=app.js.map
