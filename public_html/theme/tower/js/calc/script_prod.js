$(document).ready(function() {
	
    $('#hint_online .close').on('click', function(){
        $('#hint_online').hide('fade');
        $.post("/ajax/hint.php",{
            "action" : 'close'
        },function (data) {});
        return false;
    });
    
    //yaCounter2001595.reachGoal('call');
    var currentScroll = $(window).scrollTop();
    function fixedNav() {
        var newScroll = $(window).scrollTop();

        if (!$('#body').hasClass('noscroll')) {

            if (newScroll > 70) {
                $('#header_wrapper').addClass('fixed');
                if (newScroll > 300) {
                    $('#header_wrapper').addClass('hide');
                } else {
                    $('#header_wrapper').removeClass('hide');
                }
                //проверяем направление скрола
                if (newScroll < currentScroll) {
                    //scrolling up
                    $('#header_wrapper').addClass('show');
                } else if (newScroll > currentScroll) {
                    //scrolling down
                    $('#header_wrapper').removeClass('show');
                }

            } else {
                
                $('#header_wrapper').removeClass('fixed show hide');
            
            }
            
        }
        /*if (newScroll > 197) {
            $('#header_wrapper').addClass('fixed show');
            //$('#header_wrapper.fixed .header').addClass('show');
        } else {
            $('#header_wrapper').removeClass('fixed show')
            //$('#header_wrapper.fixed .header').removeClass('show');
        }*/

        $('#header_wrapper.fixed .header').css('left',-$(window).scrollLeft());

        
        //fixed фильтры для офисов
        if ($('#office_filter').length) {
            var topOffset = 226;
            //console.log($('#map').hasClass('hide'));
            if (newScroll >= topOffset && $('#map').hasClass('hide')) {
                var top = newScroll-226;
                $('#office_filter').addClass('fixed').css({
                    'top': top
                });
            } else {
                $('#office_filter').removeClass('fixed').css({
                    'top': 0
                });
            }
            //console.log(topOffset);
        }

        //fixed темы для вопросы и ответы
        if ($('#faq_nav').length) {
            var topOffset = 226;
            var topQuestion = $('#question_list').offset().top*1;
            //console.log($('#map').hasClass('hide'));
            //console.log(newScroll);
            if (newScroll >= topQuestion-200+82) {
                
                var navHeight = $('#faq_nav').height()*1;
                var questionHeight = $('#question_list').height()*1;
                
                var leftQuestion = $('#question_list').offset().left*1;
                var windowHeight = $(window).height()*1;

                var top = newScroll-topQuestion+200;
                
                var maxTop = topQuestion+questionHeight-navHeight-200-140;
                var maxQuestion = questionHeight;
                //console.log(top+" > "+maxTop);
                if (top > maxTop) {
                
                    top = maxTop;

                }
                    
                $('#faq_nav').addClass('fixed').css({
                    "top": top
                });
                
            } else {
                $('#faq_nav').removeClass('fixed').removeAttr("style");
            }
            //console.log(topOffset);
        }
        

        currentScroll = newScroll;
    }

    $(window).on('scroll', function(){

        fixedNav();
    });

    fixedNav();

    if ($('#actions_slider').length) {

        var imgSlides = '/img/index-slider-arrow.png';
        $('#actions_slider .slide').each(function(){
            imgSlides += ","+$(this).css("backgroundImage").replace('url(','').replace(')','').replace('"',"").replace('"','');
        });
        preloadImages('actionsSlider', imgSlides);

        
    }

    var calcArray;

    if ($('#calc_wrapper').length) {

         
            

        var gradualArray = [];
        var bulletsArray = [];
        $.ajax({
            url: '/ajax/calc2.php',
            //url: 'tmp/calc.xml',
            //type: 'post',
            success: function(data) {
                calcArray = data;
                
                calculateCredit('init');

                $('#calc_wrapper').removeClass('load');
            }
        });


        //ползунки
        var $periodSlider;
        var $sumSlider;

        //период в слайдере
        var period;

        //тип ползунка
        var typeCalc;
        var defaulSumMin;
        var defaulSumMax;
        var defaulSumCur; 

        //строка для сравнения - нужна для переинициализации ползунка периода
        var strSlider = '';
        //функция расчета кредита
        function calculateCredit(type) {
            //console.log(calcArray.item[10000].data.length);

            //инициализация
            if (type == 'init') {

                defaulSumMin = calcArray.setting.sum_min*1;
                defaulSumMax = calcArray.setting.sum_max*1;
                defaulSumCur = calcArray.setting.sum_cur*1;

                $('#calc_sum_min').text(number_format(defaulSumMin, 0, '', ' '));
                $('#calc_sum_max').text(number_format((defaulSumMax+1000), 0, '', ' '));
                //console.log(defaulSumMin+' '+defaulSumMax+' '+defaulSumCur);

                $('#calc_sum').val(number_format(defaulSumCur, 0, '', ' '));

                var periodSliderMax = calcArray.item[defaulSumCur].data.length*1-1;
                var periodSliderValue = Math.round(periodSliderMax/2);

                period = periodSliderValue;

                typeCalc = calcArray.item[defaulSumCur].data[periodSliderValue].term_type;

                //console.log(periodSliderValue+' ||| '+periodSliderMax);

                $sumSlider = $('#sum_slider').slider({
                    range: 'min',
                    value: defaulSumCur,
                    min: defaulSumMin,
                    max: defaulSumMax,
                    step: 1000,
                    slide: function(event, ui ){
                        
                        $('#calc_sum').val(number_format(ui.value, 0, '', ' '));
                        calculateCredit();
                    }
                });

                $periodSlider = $('#period_slider').slider({
                    range: 'min',
                    value: periodSliderValue,
                    min: 0,
                    max: periodSliderMax,
                    step: 1,
                    slide: function(event, ui ){
                        period = ui.value;
                        //подпись и значение для текущего периода
                        var term = calcArray.item[$sumSlider.slider('value')].data[ui.value].term;
                        var termLabel = calcArray.item[$sumSlider.slider('value')].data[ui.value].term_type_ru;
                        //console.log(period+' ||| '+term+' ||| '+termLabel);
                        $('#calc_period_label').text(termLabel);
                        $('#calc_period').val(term);

                        typeCalc = calcArray.item[$sumSlider.slider('value')].data[ui.value].term_type;
                        //console.log(ui.value);
                        calculateCredit();
                    }
                });
                


            } 


            var sum = $('#calc_sum').val().replace(/\s+/g,"")*1;

            //console.log(period);
            //в случае если изменяется период для суммы - переинициализация ползунков
            //console.log(sum);
            var strSliderNew = calcArray.item[sum].str;
            //console.log(sum);
            if (strSliderNew != strSlider) {
                

                strSlider = strSliderNew;
                var sliderPeriodMin = 0;
                var sliderPeriodMax = calcArray.item[sum].data.length*1-1;
                var sliderPeriodValue = Math.round(sliderPeriodMax/2);
                period = sliderPeriodValue;

                typeCalc = calcArray.item[sum].data[period].term_type;

                //подписи для min/max значения периода
                var termLabelMin = calcArray.item[sum].data[0].term_type_ru;
                var termMin = calcArray.item[sum].data[0].term;
                $('#calc_period_min').text(termMin);
                $('#calc_period_label_min').text(termLabelMin);

                var termLabelMax = calcArray.item[sum].data[sliderPeriodMax].term_type_ru;
                var termMax = calcArray.item[sum].data[sliderPeriodMax].term;
                $('#calc_period_max').text(termMax);
                $('#calc_period_label_max').text(termLabelMax);

                //подпись и значение для текущего периода
                var term = calcArray.item[sum].data[period].term;
                var termLabel = calcArray.item[sum].data[period].term_type_ru;
                //console.log(period+' ||| '+term+' ||| '+termLabel);
                $('#calc_period_label').text(termLabel);
                $('#calc_period').val(term);
                
                //переинициализация позунка периода
                //обновляем max/value
                $periodSlider.slider("option", { 
                    min: 0,
                    max: sliderPeriodMax,
                    value: sliderPeriodValue
                });

            }

            
            //console.log(typeCalc);
            if (typeCalc == 'day') {
                var labelReturn = 'Возвращаете';
            } else {
                var labelReturn = 'Платеж раз в 2 недели';
            }
             $('#label_sum_return').html(labelReturn); 

            //подставляем данные по платежу
            $('#value1').html($('#calc_sum').val());
            //дата возврата
            var deadline = calcArray.item[sum].data[period].deadline;
            
            $('#value2').html(deadline);

            //сумма возврата/сумма со скидкой
            var sumReturnHTML;
            var payment = calcArray.item[sum].data[period].payment;
            var discount = calcArray.item[sum].data[period].discount;
            if (discount == '' || discount == '0' || discount == 0) {
                $('#sum_return').html('<span>'+number_format(payment, 0, '', ' ')+'</span> руб.');
            } else {
                $('#sum_return').html('<span class="new">'+number_format(discount, 0, '', ' ')+'</span><span class="old">'+number_format(payment, 0, '', ' ')+'</span> руб.');
            }

            //проверка показа сообщение
            //для суммы
            var noticeSum = calcArray.item[sum].data[period].notice_sum*1;
            if (noticeSum == 0) {
                $('#alert_sum').hide().text('');
            } else {
                $('#alert_sum').text(calcArray.notice.sum[noticeSum]).show();
            }

            //для периода
            var noticeTerm = calcArray.item[sum].data[period].notice_term;
            if (noticeTerm == 0) {
                $('#alert_period').hide().text('');
            } else {
                $('#alert_period').text(calcArray.notice.term[noticeTerm]).show();
            }

            
        }

        //инициализация ползунков      

        $('#calc_period').on('change', function(){
            
            var sum = $('#calc_sum').val().replace(/\s+/g,"")*1;

            if (!validateForm('number', 'calc_period') ) {

                $(this).val(calcArray.item[sum].data[period].term);               
                
            } else {

                var termCheck = 0;
                
                var newTerm = jQuery.trim($(this).val())*1;

                //ищем введенный срок для суммы
                for(var i=0;i<calcArray.item[sum].data.length*1;i++){
                    if (newTerm == calcArray.item[sum].data[i].term && typeCalc == calcArray.item[sum].data[i].term_type) {
                        termCheck++;
                        $('#calc_period_label').text(calcArray.item[sum].data[i].term_type_ru);
                        $periodSlider.slider('value', i);
                        period = i;

                        calculateCredit();
                    }
                }
                
                //если указанный срок найден
                if (termCheck == 0) {
                    $(this).val(calcArray.item[sum].data[period].term);
                } 
                
            }
        });


        $('#calc_sum').on('change', function(){
            //alert($(this).val());
            if (!validateForm('number', 'calc_sum')) {
                $(this).val($sumSlider.slider('value'));
            } else {
                var val = jQuery.trim($(this).val().replace(/\s+/g,""))*1;

                var ost = val%1000;
                if ( ost >= 500 ) {
                    val = Math.ceil(val/1000)*1000;
                } else if (ost < 500 && ost > 0) {
                    val = Math.floor(val/1000)*1000;
                }

                if (val > defaulSumMax) {
                    $(this).val(defaulSumMax);
                    val = defaulSumMax;
                } else if (val < defaulSumMin) {
                    $(this).val(defaulSumMin);
                    val = defaulSumMin;
                }

                
                $sumSlider.slider('value', val);
                $('#calc_sum').val(number_format(val, 0, '', ' '));
                calculateCredit();
                
            }
        });



    }


    if ($('#reviews_slider').length) {
        $reviewsSlider = new Swiper('#reviews_slider',{
            slidesPerView: 2,
            slidesPerGroup: 1,
            grabCursor: true,
            loop: 'true'
        });
        $('#reviews_slider_prev').on('click', function(){
            $reviewsSlider.swipePrev();
            return false;
        });
        $('#reviews_slider_next').on('click', function(){
            $reviewsSlider.swipeNext();
            return false;
        });
    }


    
    //ползунок суммы для блока Получить деньги в контенте
    if ($('#order_content').length) {
        var sumMin = 3000;
        var sumMax = 99000;
        var sumValue = 25000;
        $('#order_sum_value').val(number_format(sumValue, 0, '', ' '));
        $('#order_sum_min').text(number_format(sumMin, 0, '', ' '));
        $('#order_sum_max').text(number_format((sumMax+1000), 0, '', ' '));
        var $orderSumSlider = $('#order_sum_slider').slider({
            range: 'min',
            value: sumValue,
            min: sumMin,
            max: sumMax,
            step: 1000,
            slide: function(event, ui ){

                $('#order_sum_value').val(number_format(ui.value, 0, '', ' '));
                
                console.log(ui.value*1);
                if (ui.value*1 >= 3000 && ui.value <=16000) {
                    $('#alert_sum_form, #alert_sum').text('Займы доступны без прихода в офис*').addClass('active');
                } else if (ui.value*1 > 50000) {
                    $('#alert_sum_form, #alert_sum').text('Сумма доступна для повторных клиентов').addClass('active');
                } else {
                    $('#alert_sum_form, #alert_sum').removeClass('active');
                }
                
            }
        });

        $('#order_sum_value').on('change', function(){
            //alert($(this).val());
            if (!validateForm('number', 'order_sum_value')) {
                $(this).val($orderSumSlider.slider('value'));
            } else {
                var val = jQuery.trim($(this).val().replace(/\s+/g,""))*1;

                var ost = val%1000;
                if ( ost >= 500 ) {
                    val = Math.ceil(val/1000)*1000;
                } else if (ost < 500 && ost > 0) {
                    val = Math.floor(val/1000)*1000;
                }

                if (val > sumMax) {
                    $(this).val(sumMax);
                    val = sumMax;
                } else if (val < sumMin) {
                    $(this).val(sumMin);
                    val = sumMin;
                }


                if (ui.value*1 >= 3000 && ui.value <=16000) {
                    $('#alert_sum_form, #alert_sum').text('Займы доступны без прихода в офис*').addClass('active');
                } else if (ui.value*1 > 50000) {
                    $('#alert_sum_form, #alert_sum').text('Сумма доступна для повторных клиентов').addClass('active');
                } else {
                    $('#alert_sum_form, #alert_sum').removeClass('active');
                }

                $orderSumSlider.slider('value', val);
                $('#order_sum_value').val(number_format(val, 0, '', ' '));
                
                
            }
        });
    }

    //запускаем эффекты
    contentEffect(); 


    $('select').styler({
        selectSmartPositioning: false,
        selectSearch: false,
        selectPlaceholder: 'Выберите:',
        onSelectOpened: function() { 
            
            $(this).removeClass('error');
            
            $(this).find('.jq-selectbox__dropdown ul').jScrollPane();
        }
    }); 

    $('#feedback_file').styler({
        filePlaceholder: 'Прикрепить файл'
    }); 

    /*  ==================
    ОФИСЫ
    ================== */
    if ($('#office_list').length) {

        //фильтр по городам/областям
        $('#office_region').on('change', function(){
            var region = $(this).val();
            $('#body').addClass('noscroll');
            $('#header_wrapper').removeClass('fixed show');
            if (region=="") {
                $('#office_list table tr').removeClass('hide');
                officesMapInit();

                $('#office_list tr').removeClass('current');
                $('#office_desc').removeClass('show');

                $('#body').removeClass("noscroll");
                $('#office_city_field').addClass('hide');
            } else {
                var classTR = 'r'+region;
                var cityArray = [];
                var cityList = '';
                
                //$('#office_list table tr').addClass('hide');
                //$('#office_list table tr:first-child').removeClass('hide');
                $('#office_list table tr.'+classTR).removeClass('hide').each(function(){
                    if (!$(this).hasClass('reg')) {
                        //формируем массив городов для региона
                        cityArray[$(this).data('cityname')] = $(this).data('city');
                    }
                    
                });
                

                var cityArrayLength = 0;
                for (var key in cityArray) {
                    cityList += '<option value="'+cityArray[key]+'">'+key+'</option>';
                    cityArrayLength++;
                }

                if (cityArrayLength > 1) {
                    cityList = '<option value="">Все города</option>'+cityList;
                }
                
                $('#office_city').html(cityList).trigger('refresh');

                $('#office_city_field').removeClass('hide');
                //var city = $('#office_city').val();

                //var classTR = '.r'+region+'.c'+city;

                

                $('#office_list tr').removeClass('current');
                $('#office_desc').removeClass('show');

                $('#office_list table tr:not(.'+classTR+',:first-child)').addClass('hide');
                //скролл к первой строке региона/города
                var obj = $('#office_list table tr.'+classTR).first();
                jQuery.scrollTo(obj, 200, {
                    axis:'y',
                    onAfter: function(){
                        //console.log("scrollOk");
                        
                        setTimeout(function(){
                            $('#body').removeClass("noscroll");
                        }, 200);
                    }
                });

                officesMapInit();

            }


            

        });

        $('#office_city').on('change', function(){

            $('#body').addClass("noscroll");
            $('#header_wrapper').removeClass('fixed show');
            var region = $('#office_region').val();
            var classTR = '.r'+region;
            var city = $(this).val();
            if (city != '') {
               classTR = classTR+'.c'+city
            }
            
            $('#office_list table tr').addClass('hide');
            $('#office_list table tr:first-child').removeClass('hide');
            $('#office_list table tr'+classTR).removeClass('hide');
            //console.log($('#office_list table tr'+classTR).length);
            officesMapInit();
            $('#office_list tr').removeClass('current');
            $('#office_desc').removeClass('show');
            var obj = $('#office_list table tr'+classTR).first();
            jQuery.scrollTo(obj, 200, {
                axis:'y',
                onAfter: function(){
                    //console.log("scrollOk");
                    
                    setTimeout(function(){
                        $('#body').removeClass("noscroll");
                    }, 200);
                }
            });
        });

        officesMapInit();

        $('#office_view a').on('click', function(){
            if (!$(this).hasClass('active')) {
                $('#body').addClass('noscroll');

                $(this).addClass('active').siblings('.active').removeClass('active');
                var type = $(this).attr('href');
                if (type == '#map') {
                    $('#office_list').addClass('hide');
                    $('#map').removeClass('hide');
                    officesMapInit();
                    $('#body').removeClass('noscroll');
                    
                } else {

                    //console.log('start!');

                    $('#map').addClass('hide');
                    $('#office_list').removeClass('hide');
                    
                    

                    if (!$('#office_city_field').hasClass('hide')) {
                        var region = $('#office_region').val();
                        var classTR = '.r'+region;
                        var city = $('#office_city').val();
                        if (city != '') {
                            classTR = classTR+'.c'+city;
                        }
                        
                        //console.log(classTR);
                        var obj = $('#office_list table tr'+classTR).first();
                        jQuery.scrollTo(obj, 200, {
                            axis:'y',
                            onAfter: function(){
                                //console.log('scrollOk');
                                
                                setTimeout(function(){
                                    $('#body').removeClass('noscroll');
                                }, 200);
                            }
                        });
                    } else {
                        //console.log('2');
                        /*jQuery.scrollTo(0, 200, {
                            axis:'y',
                            onAfter: function(){
                                //console.log('scrollOk');
                                
                                setTimeout(function(){
                                    $('#body').removeClass('noscroll');
                                }, 200);
                            }
                        });*/
                        $('#body').removeClass('noscroll');
                    }
                    
                }
                $('#office_list tr').removeClass('current');
                $('#office_desc').removeClass('show');

                fixedNav();
            }

            return false;
        });

    }

    /*  ==================
    Наша команда
    ================== */
    $('#team_nav a').on('click', function(){
        if (!$(this).hasClass('active')) {
            $(this).addClass('active').siblings('.active').removeClass('active');
            $('#team_tabs .tab[rel='+$(this).attr('href').replace('#','')+']').addClass('active').siblings('.active').removeClass('active');
        }
        return false;
    });

    /*  ==================
    Вакансии
    ================== */
    $('#vacancies_list .vacancy-title a').on('click', function(){
        var $vacancy = $(this).parent().parent();
        if ($vacancy.hasClass('active')) {
            $vacancy.removeClass('active');
        } else {
            $vacancy.addClass('active').siblings('.active').removeClass('active');
        }
        return false;
    });

    $('#vacancies_city').on('change', function(){
        var city = $(this).val();
        $('#body').addClass('noscroll');
        if (city == 'all') {
            $('#vacancies_list .vacancy').removeClass('hide active');
        } else {
            $('#vacancies_list .vacancy').addClass('hide').removeClass('active');
            $('#vacancies_list .vacancy[data-city='+city+']').removeClass('hide');
        }
        setTimeout(function(){
            $('#body').removeClass('noscroll');
        }, 500);
    });


     /*  ==================
    Вопросы и ответы
    ================== */
    if ($('#question_list').length) {
        $('#question_list .question .title').on('click', function(){
            var $question = $(this).parent();
            if ($question.hasClass('active')) {
                $question.removeClass('active');
            } else {
                $('#question_list .question.active').removeClass('active');
                $question.addClass('active');
            }

            return false;
        });

        var offsetViewport = $(window).height()*1/2;
        $('#question_list .group').viewportChecker({
            classToAdd: 'active',
            offset: 355,
            invertBottomOffset: true,
            repeat: true,
            callbackFunction: function(elem, action){
                //console.log('view');
                if ($('#question_list .group.active').length) {
                    var theme = $('#question_list .group.active').first().data('theme');
                    //console.log(theme);
                    $('#faq_nav a[href=#'+theme+']').addClass('active').siblings('.active').removeClass('active');
                }
                
            },
            scrollHorizontal: false
        });

        //скролл к группе вопросов
        $('#faq_nav a').on('click', function(){
            if (!$(this).hasClass('active')) {
                $('#body').addClass('noscroll');
                var theme = $(this).attr('href').replace('#','');
                var obj = $('#question_list .group[data-theme='+theme+']');
                jQuery.scrollTo(obj, 200, {
                    axis:'y',
                    offset: {left: 0, top:-155 },
                    onAfter: function(){
                        //console.log("scrollOk");
                        
                        setTimeout(function(){
                            $('#body').removeClass('noscroll');
                        }, 200);
                    }
                });

                $('#header_wrapper').removeClass('fixed show');
            }
            

            return false;
        });

            

    }


    /* ==================
    НОВОСТИ
    ================== */

    //подгрузка новостей
    var newsPageLoad = 1;
    var newsLoadHtml = '<a href="#" class="news first"><span class="title">МигКредит профинансировал более 420 тысяч займов на общую сумму более 11 млрд рублей</span><span class="date">30 октября 2015</span></a><a href="#" class="news"><span class="title">МигКредит проводит акцию для клиентов «4 года - 4 автомобиля!»</span><span class="date">30 октября 2015</span></a><a href="#" class="news"><span class="title">МигКредит предупреждает своих клиентов о росте мошенничества среди антиколлекторских компаний</span><span class="date">30 октября 2015</span></a><div class="clr"></div>';
    $('#show_more_news').on('click', function(){
        if (!$(this).hasClass('load')) {
            var $this = $(this);
            var year = $(this).attr('rel');
            $this.addClass('load');
            $.get("/news/ajax.php",{
                "PAGEN_1" : newsPageLoad,
                "year" : year
            },function (data) {
				
                $('#news_list').append(data);
                if (newsPageLoad == $this.data('pages')) {
                    $this.remove();
                } else {
                    $this.removeClass('load');
                    newsPageLoad++;
                }
                

            });
        }

        return false;
    });

    /* ==================
    Отзывы клиентов
    ================== */

    //cлайдер отзывов
    if ($('#reviews_slider_inner').length) {
        $reviewsSliderInner = new Swiper('#reviews_slider_inner',{
            slidesPerView: 1,
            slidesPerGroup: 1,
            grabCursor: true,
            loop: 'true',
            pagination: '.review-pagination',
            paginationClickable: true
        });
        $('#reviews_slider_prev').on('click', function(){
            $reviewsSliderInner.swipePrev();
            return false;
        });
        $('#reviews_slider_next').on('click', function(){
            $reviewsSliderInner.swipeNext();
            return false;
        });
    }

    //подгрузка отзывов
    var reviewsPageLoad = 2;
    var reviewsLoadHtml = '<div class="review"><p class="title"><strong>Игорь Владимиров</strong> <span>|</span> стажер</p><p class="date">09 сентября 2015</p><p class="text">Я хотел новый ноут, но денег особо нет. Увидел сайт МигКредит и удивился. Оказалось, что денег можно попросить в долг прямо из дома через онлайн-анкету. Ребята, вы здорово придумали. Кстати, пишу вам с нового крутого ноута.</p></div><div class="review"><p class="title"><strong>Игорь Владимиров</strong> <span>|</span> стажер</p><p class="date">09 сентября 2015</p><p class="text">У меня лично с Мигкредит все нормально прошло. Нужны были 10 тысяч срочно!!! Срочно - это не через неделю, а завтра, а лучше уже сегодня!!! В Мигкредите выдали на следующий день, специально просил консультанта, чтобы побыстрее рассмотрели. Взял на 3 месяца, гасил маленькими платежами. Поскольку платежи были небольшими, то просрочек не было. Мой вывод: если срочно нужны деньги, а все друзья отказывают, то можно занять у них. Главное - платить вовремя.</p></div><div class="review"><p class="title"><strong>Марина Карасева</strong> <span>|</span> преподаватель</p><p class="date">09 сентября 2015</p><p class="text">Взяла в Мигкредите взаймы 15 000 рублей. Деньги срочно понадобились на лечение у частного врача. Конечно, здоровье не купишь, но ходить по районным поликлиникам не хочу. Все оказалось просто. Справку о зарплате у меня не просили. Пришла с паспортом и минут через 20 вопрос все было оформлено. Теперь главное - вовремя все вернуть, чтобы потом дали сумму больше. </p></div><div class="review"><p class="title"><strong>Дарья Петрова</strong> <span>|</span> агент</p><p class="date">09 сентября 2015</p><p class="text">Срочно понадобилось 20 000 рублей на приобретение подарка. Увидела рекламу от МигКредит, через интернет ознакомилась с информацией по займам, оставила заявку онлайн на сайте. Заем одобрили быстро. Денежные средства получила через сутки после удовлетворения заявки на счёт банковской карты. Я довольна.</p></div>';
    $('#show_more_reviews').on('click', function(){
        if (!$(this).hasClass('load')) {
            var $this = $(this);
            $this.addClass('load');

            $.get("/about/otzyvy-klientov/ajax.php",{
                "PAGEN_1" : reviewsPageLoad
            },function (data) {
                $('#reviews_list').append(data);
                if (reviewsPageLoad == $this.data('pages')) {
                    $this.remove();
                } else {
                    $this.removeClass('load');
                    reviewsPageLoad++;
                }

            });
        }

        return false;
    });


    /*  ==================
    Получение денег
    ================== */
    
    //скролы к блокам лендинга
    $('#get_wrapper .buttons .link').on('click', function(){
        $('#body').addClass("noscroll");
        $('#header_wrapper').removeClass('show');
        var $obj = $($(this).attr('href'));
        jQuery.scrollTo($obj, 200, {
            axis:'y',
            onAfter: function(){
                setTimeout(function(){
                    $('#body').removeClass("noscroll");
                }, 200);
            }
        });
        return false;
    });

    //если на странице Получение денег тогда скролл к блокам из основного меню
    /*if ($('#get_wrapper').length) {
        $('nav .subnav.anchor a').on('click', function(){
            $('#body').addClass("noscroll");
            var url = $(this).attr("href").split("#");
            var $obj = $('#'+url[1]+'_wrapper');
            //alert($obj.html());
            jQuery.scrollTo($obj, 200, {
                axis:'y',
                onAfter: function(){
                    setTimeout(function(){
                        $('#body').removeClass("noscroll");
                    }, 200);
                }
            });

            return false;
        });

    }*/

    //переход к получению денег
    $('.anchor-type-get').on('click', function(){
        $('#body').addClass('noscroll');
        $('#header_wrapper').removeClass('show');
        var $obj = $('#type_get_wrapper');
        //alert($obj.html());
        jQuery.scrollTo($obj, 200, {
            axis:'y',
            onAfter: function(){
                setTimeout(function(){
                    $('#body').removeClass('noscroll');
                }, 200);
            }
        });
        return false;
    });

    //табы способов оформления заема
    $('#type_loan_wrapper .tabs a').on('click', function(){
        if (!$(this).hasClass('active')) {
            $(this).addClass('active').siblings('.active').removeClass('active');
            $('#type_loan_wrapper .tabs-wrapper .tab[rel='+$(this).attr('href').replace('#','')+']').addClass('active').siblings('.active').removeClass('active');


            $('#type_loan_infographic .tab:not(.active)').find('.info,.plus').removeClass('fadeInEffect animated').addClass('noanimate');
            if (!$('#type_loan_infographic .tab.active .plus').hasClass('visible')) {
                $('#type_loan_infographic .tab.active .plus, #type_loan_infographic .active .info').addClass('visible animated fadeInEffect');
            } 
            


        }
        return false;
    });

    //активный по умолчанию таб
    if ($('#type_loan_wrapper').length) {
        var loc = window.location.hash;/*.replace("#","");*/
        //console.log(loc);
        if (loc == '#type_loan_office') {

            $('#type_loan_wrapper .tabs a[href=#2]').addClass('active').siblings('.active').removeClass('active');
            $('#type_loan_wrapper .tabs-wrapper .tab[rel=2]').addClass('active').siblings('.active').removeClass('active');


            $('#type_loan_infographic .tab:not(.active)').find('.info,.plus').removeClass('fadeInEffect animated');
            $('#type_loan_infographic .tab.active .plus, #type_loan_infographic .active .info').addClass('visible animated fadeInEffect'); 
        }
    }

    //табы способов получение/погашения заема
    $('#type_get_wrapper .tabs a').on('click', function(){
        if (!$(this).hasClass('active')) {
            $(this).addClass('active').siblings('.active').removeClass('active');
            $('#type_get_wrapper .tabs-wrapper .tab[rel='+$(this).attr('href').replace('#','')+']').addClass('active').siblings('.active').removeClass('active');
        }
        return false;
    });

    //табы виды займов
    $('#type_credit_wrapper .tabs a').on('click', function(){
        if (!$(this).hasClass('active')) {
            $(this).addClass('active').siblings('.active').removeClass('active');
            var type = $(this).attr('href').replace('#','')*1;
            if (type == 1) {
                $('#type_credit_wrapper .sum').text('50');
                $('#sum_info').removeClass('i4').addClass('i3');
                $('.content .get-wrapper .type-credit-wrapper .type-credit .type.t2 .info .val.i2 .term').hide();
            } else {
                $('#type_credit_wrapper .sum').text('100');
                $('#sum_info').removeClass('i3').addClass('i4');
                $('.content .get-wrapper .type-credit-wrapper .type-credit .type.t2 .info .val.i2 .term').show();
            }
            
        }
        return false;
    });
    


    /*  ==================
    КОНТАКТЫ
    ================== */
    
    var maskPhone = "(999) 999 9999";
    //очистка маски ввода
    function clearMask(id, mask) {
        if (!$("html").hasClass("touch")) {
            var name = jQuery.trim($("#"+id).val());
            
            mask = mask.replace(/[9]/g, "_");
            //если имя совпадает с маской значит ничего не вводили очищаем значение!
            if (name == mask) {
                $("#"+id).val("").removeClass("error");
                $(".error-message."+id).text("").hide();

                if ($("html").hasClass("lt-ie10")) {
                    $.Placeholder.init({ color : "#aaaaaa" });
                }
            }
        }
    }
    $('#feedback_phone').mask(maskPhone).on("change", function(){
        //validateForm("number_phone", "feedback_phone");
        clearMask("feedback_phone", maskPhone);
    });;
    
    
    //карта контакты
    $('#map_control').on('click', function(){
        if ($('#map_contacts_wrapper:visible').length) {
            $('#map_contacts_wrapper').hide();
            $(this).find('span').text('На карте');
        } else {
            $('#map_contacts_wrapper').show();
            $(this).find('span').text('Свернуть');

            contactsMap();
        }
       
        return false;
    });
    
    $('#close_map_contacts').on('click', function(){
        $('#map_contacts_wrapper').hide();
        $('#map_control').find('span').text('На карте');
        return false;
    });

    $('#feedback_type').on('change', function(){
        if ($(this).val() == '5') {
            $('.theme-field').removeClass('hide');
        } else {
            $('.theme-field').addClass('hide');
            $('#question_type option:selected').prop('selected', false).trigger('refresh');
        }
    });

    //форма обратной связи
    $('#feedback_submit').on('click', function(){
        if (!$(this).hasClass('load')) {

            var error = 0;
            if (!validateForm('rusfield','feedback_name')) {
                $('#feedback_name').addClass('error');
                error++;
            }
            if (!validateForm('email','feedback_email')) {
                $('#feedback_email').addClass('error');
                error++;
            }
            if (!validateForm('required','feedback_text')) {
                $('#feedback_text').addClass('error');
                error++;
            }

            if (!validateForm('required','feedback_type')) {
                $('#feedback_type').parent().addClass('error');
                error++;
            } else {
                $('#feedback_type').parent().removeClass('error');
            }

            if (!validateForm('mobile_phone_empty','feedback_phone')) {
                $('#feedback_phone').addClass('error');
                error++;
            }

            if (!validateForm('required','feedback_region')) {
                $('#feedback_region').parent().addClass('error');
                error++;
            }
            
            if (!validateForm('number_dogovor','feedback_contract')) {
                $('#feedback_contract').addClass('error');
                error++;
            }

            if (!$('.theme-field').hasClass('hide')) {
                if (!validateForm('required','question_type')) {
                    $('#question_type').parent().addClass('error');
                    error++;
                }
                
            }

            if (error == 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
        
       
    });

    $('input.text, textarea.textarea').on('focus', function(){
        $(this).removeClass('error');
    });
    


    /* ==================
    Комментарии экспертов
    ================== */

    //подгрузка комментариев
    var expertsPageLoad = 2;
    var expertsLoadHtml = '<div class="comment"><figure class="img"><img src="/img/expert-photo-04.png" alt="img"></figure><div class="text"><p class="name">Надежда Денисова</p><p class="status">Директор по маркетингу МФО «МигКредит»</p><p><a href="#">О преимуществах онлайн-анкеты перед обычным оформлением займа в офисе компании</a></p><p>19 сентября 2015</p></div><div class="clr"></div></div>';
    $('#show_more_experts').on('click', function(){
        if (!$(this).hasClass('load')) {
            var $this = $(this);
            $this.addClass('load');
            $.get("/expert/ajax.php",{
                "PAGEN_1" : expertsPageLoad
            },function (data) {

                $('#experts_wrapper').append(data);
                if (expertsPageLoad == $this.data('pages')) {
                    $this.remove();
                } else {
                    $this.removeClass('load');
                    expertsPageLoad++;
                }

            });
        }

        return false;
    });



    /* ==================
    Статьи
    ================== */

    //подгрузка статей
    var articlesPageLoad = 2;
    var articlesLoadHtml = '<a href="#" class="article">Займ до зарплаты в Москве</a><a href="#" class="article">Плохая кредитная история: как взят кредит?</a><a href="#" class="article">Мгновенный займ</a><a href="#" class="article">Кредит наличными день в день по паспорту</a><a href="#" class="article">Срочный кредит без отказа</a>';

    $('#show_more_articles').on('click', function(){
        if (!$(this).hasClass('load')) {
            var $this = $(this);
            $this.addClass('load');
            $.get("/articles/ajax.php",{
                "PAGEN_1" : articlesPageLoad
            },function (data) {
                $('#articles_wrapper').append(data);

                if (articlesPageLoad == $this.data('pages')) {
                    $this.remove();
                } else {
                    $this.removeClass('load');
                    articlesPageLoad++;
                }
            });
        }

        return false;
    });


    /* ==================
    Статьи
    ================== */
	
	/* ==================
    Статьи
    ================== */

    //подгрузка статей
    var manualPageLoad = 2;
    var manualLoadHtml = '<a href="#" class="article">Займ до зарплаты в Москве</a><a href="#" class="article">Плохая кредитная история: как взят кредит?</a><a href="#" class="article">Мгновенный займ</a><a href="#" class="article">Кредит наличными день в день по паспорту</a><a href="#" class="article">Срочный кредит без отказа</a>';

    $('#show_more_manual').on('click', function(){
        if (!$(this).hasClass('load')) {
            var $this = $(this);
            $this.addClass('load');
            $.get("/manual/ajax.php",{
                "PAGEN_1" : manualPageLoad
            },function (data) {
                $('#articles_wrapper').append(data);

                if (manualPageLoad == $this.data('pages')) {
                    $this.remove();
                } else {
                    $this.removeClass('load');
                    manualPageLoad++;
                }
            });
        }

        return false;
    });


    /* ==================
    Статьи
    ================== */
	
    //табы наши ценности
    $('#about_worth_nav a').on('click', function(){
        if (!$(this).hasClass('active')) {
            $(this).addClass('active').siblings('.active').removeClass('active');

            $('#about_worth_tabs .tab:visible').hide();
            $('#about_worth_tabs .tab[rel='+$(this).attr('href').replace('#','')+']').show('fade');

        }
        return false;
    });
    
    /* ==================
    О компании
    ================== */
    //табы наша история
    $('#history_tabs_nav a').on('click', function(){
        if (!$(this).hasClass('active')) {
            $(this).addClass('active').siblings('.active').removeClass('active');

            var activeYear = $(this).attr('href').replace('#','');
            $('#history_tabs .tab:visible').hide();
            $('#history_tabs .tab[rel='+activeYear+']').show('fade');

            $('#history_points .point[rel='+activeYear+']').addClass('active').siblings('.active').removeClass('active');

        }
        return false;
    });          

    /*  ==================
    placeholders
    ================== */
    $('.field').on('click', '.label-top', function(){
        $(this).next('input, textarea').focus();
        return false;
    });
    $('.placeholder').on('focus', function(){
        $(this).removeClass('error');
        $(this).parent().removeClass('error');
        if (jQuery.trim($(this).val()) == $(this).data('placeholder')) {
            $(this).val('');
            var labelText = $(this).data('placeholder');
            $(this).parent().prepend('<span class="label-top">'+labelText+'</span>');
            $(this).parent().find('.label-top').show('fade', 100);
        }
    });
    $('.placeholder').on('blur', function(){
        if (jQuery.trim($(this).val()) == '') {
            $(this).val($(this).data('placeholder'));
            $(this).parent().find('.label-top').hide('fade', 100, function(){
                $(this).remove();
            });
        }
    });





});//END READY

//слайдер акций
function initActionsSlider() {
    $('#actions_slider').owlCarousel({
        navigation : false,
        navigationText: false,
        pagination: true,
        paginationNumbers: false,
        singleItem : true,
        autoPlay: 5000,
        stopOnHover: false,
        mouseDrag: false,
        transitionStyle: 'fade'
        //slideSpeed: 1000,
        //paginationSpeed: 1000,
        //transitionStyle: false
    });
    var $actionsSlider = $('#actions_slider').data('owlCarousel');

    $('.actions-wrapper').hover(function(){
        $actionsSlider.stop();
    }, function(){
        $actionsSlider.play();
    });

    $('#actions_slider_prev').on('click', function(){
        $actionsSlider.prev();
        return false;
    });
    $('#actions_slider_next').on('click', function(){
        $actionsSlider.next();
        return false;
    });
}

function contentEffect() {
	//$('body').removeClass('load');
	//эффекты при скролле контента
    if ($('.effecttext').length) {
    	$('.effecttext').addClass('hidden').viewportChecker({
            classToAdd: 'visible animated fadeIn',
            offset: 30    
        });
    }
    if ($('.effectfade').length) {
        $('.effectfade').addClass('hidden').viewportChecker({
            classToAdd: 'visible animated fadeInEffect',
            offset: 100    
        });
    }
    if ($('.effectfadeup').length) {
        $('.effectfadeup').addClass('hidden').viewportChecker({
            classToAdd: 'visible animated fadeInUp',
            offset: 100    
        });
    }
    if ($('.effectfadedown').length) {
        $('.effectfadedown').addClass('hidden').viewportChecker({
            classToAdd: 'visible animated fadeInDown',
            offset: 100    
        });
    }
    if ($('.effectzoom').length) {
        $('.effectzoom').addClass('hidden').viewportChecker({
            classToAdd: 'visible animated zoomIn',
            offset: 100    
        });
    }

    if ($('#index_steps').length) {
        $('#index_steps').viewportChecker({
            classToAdd: 'visible',
            offset: 100,
            callbackFunction: function(elem, action){

                $('#index_steps .info.item').addClass('visible animated-06s zoomIn');
                $('#index_steps .info.arrow i').addClass('visible animated-04s stepsArrowEffect');
          
            }
        });
    }
    if ($('#plus_wrapper').length) {
        $('#plus_wrapper').viewportChecker({
            classToAdd: 'visible',
            offset: 200,
            callbackFunction: function(elem, action){

                $('#plus_wrapper .caption-wrapper, #plus_wrapper .plus .ico').addClass('visible animated-06s fadeInEffect');
                
          
            }
        });
    }


    if ($('#get500').length) {
        $('#get500').viewportChecker({
            classToAdd: 'visible',
            offset: 100,
            callbackFunction: function(elem, action){

                $('#get500 .item').addClass('visible animated-06s zoomIn');
                $('#get500 .arrow i').addClass('visible animated-04s stepsArrowEffect');
          
            }
        });
    }

    if ($('.stages').length) {
        $('.stages').viewportChecker({
            classToAdd: 'visible',
            offset: 100,
            callbackFunction: function(elem, action){

                $('.stages .item').addClass('visible animated-06s zoomIn');
                $('.stages .arrow i').addClass('visible animated-04s stepsArrowEffect');
          
            }
        });
    }

    //лояльность график
    if ($('#loyal_graphic').length) {
        $('#loyal_graphic').viewportChecker({
            classToAdd: 'active',
            offset: 100,
            callbackFunction: function(elem, action){

                $('#loyal_graphic .col').addClass('height color');
                $('#loyal_graphic .percent').addClass('visible animated-04s fadeInEffect');
          
            }
        });
    }

    //лояльность график
    if ($('#partners_text').length) {
        $('#partners_text').viewportChecker({
            classToAdd: 'active',
            offset: 100,
            callbackFunction: function(elem, action){

                
                $('#partners_text p').addClass('visible animated-04s fadeInEffect');
          
            }
        });
    }

    //инфографика получение займа
    if ($('#type_loan_infographic').length) {
        $('#type_loan_infographic').viewportChecker({
            classToAdd: 'active',
            offset: 100,
            callbackFunction: function(elem, action){
                $('#type_loan_infographic .active .plus, #type_loan_infographic .active .info').addClass('visible animated fadeInEffect');
            }
        });
    }

    //инфографика типы кредитов
    if ($('#type_credit_infographic').length) {
        $('#type_credit_infographic').viewportChecker({
            classToAdd: 'current',
            offset: 100,
            callbackFunction: function(elem, action){
                //if ($('#type_credit_infographic').hasClass('current')) {
                    $('#type_credit_infographic .name, #type_credit_infographic .half').addClass('visible animated fadeInEffect');
                //}
                
            }
        });
    }
    

    //сео карта
    if ($('#seo_map').length) {
        officesSeoMapInit();
    }
    

};


var timerID;
function preloadImages(type, img) {
    //скрываем все слайды           
    var load = 0;
    var imgArr = img.split(',');
    var countImg = imgArr.length;
    //console.log(countImg);
    timerID = setTimeout(function(){
        if (type == 'actionsSlider') {
            $('#actions_wrapper').removeClass('load');
            initActionsSlider();
            setTimeout(function(){
                $('.actions-wrapper .slider .slide .text p strong').addClass('green');
            }, 500);
        }
    	//contentEffect();   
    }, 7000);  

    for (i in imgArr) {
        var src = imgArr[i];
        $('<img/>', {
            'style': 'display:none;',
            'src': src,
            load: function(){
                load++;
                $(this).remove();
                //console.log(load+' == '+countImg);
                //если все картинки загрузились
                if (load == countImg) {
                    //setTimeout(function(){
                        if (type == 'actionsSlider') {
                            $('#actions_wrapper').removeClass('load');
                            initActionsSlider();
                            setTimeout(function(){
                                $('.actions-wrapper .slider .slide .text p strong').addClass('green');
                            }, 500);
                        }
                        //contentEffect();   
                    //}, 1000);
                    clearTimeout(timerID);
                }
            }
        }).appendTo('body');   
    } 
}



//денежный формат
function number_format(number, decimals, dec_point, thousands_sep){
    var i, j, kw, kd, km;
    if( isNaN(decimals = Math.abs(decimals)) ){
        decimals = 2;
    }
    if( dec_point == undefined ){
        dec_point = ',';
    }
    if( thousands_sep == undefined ){
        thousands_sep = '.';
    }
    i = parseInt(number = (+number || 0).toFixed(decimals)) + '';
    if( (j = i.length) > 3 ){
        j = j % 3;
    } else{
        j = 0;
    }
    km = (j ? i.substr(0, j) + thousands_sep : '');
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousands_sep);
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : '');
    return km + kw + kd;
}

//фукнция склонения
function declOfNum(number, titles) {  
    cases = [2, 0, 1, 1, 1, 2];  
    return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];  
}  



var styledMap = new google.maps.StyledMapType([{"featureType":"landscape","stylers":[{"hue":"#FFBB00"},{"saturation":43.400000000000006},{"lightness":37.599999999999994},{"gamma":1}]},{"featureType":"road.highway","stylers":[{"hue":"#FFC200"},{"saturation":-61.8},{"lightness":45.599999999999994},{"gamma":1}]},{"featureType":"road.arterial","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":51.19999999999999},{"gamma":1}]},{"featureType":"road.local","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":52},{"gamma":1}]},{"featureType":"water","stylers":[{"hue":"#0078FF"},{"saturation":-13.200000000000003},{"lightness":2.4000000000000057},{"gamma":1}]},{"featureType":"poi","stylers":[{"hue":"#00FF6A"},{"saturation":-1.0989010989011234},{"lightness":11.200000000000017},{"gamma":1}]}]);


var officesMap;
var markersArray = [];
var markersInfo = [];
var markersObject = [];
var objectIcon;
var objectIconActive;
var objectMarker;
function officesMapInit(type) {
    $('#offices_map').html('');
    markersArray = [];
    markersObject = [];
    if (!$('#map').hasClass('hide') ) {
        var mapOptions = {
            zoom: 15,
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.RIGHT_BOTTOM
            },
            scaleControl: false,
            mapTypeControl: false,
            panControl: false,
            disableDefaultUI: true,
            scrollwheel: false,
            disableDoubleClickZoom: true,
            draggable: true,
            mapTypeControlOptions: {
                mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
            }
        };
        officesMap = new google.maps.Map(document.getElementById('offices_map'), mapOptions);

        officesMap.mapTypes.set('map_style', styledMap);
        officesMap.setMapTypeId('map_style');
        
        objectIcon = new google.maps.MarkerImage(
            '/img/balloon-office.png',
            new google.maps.Size(42,39),
            new google.maps.Point(0,0),
            new google.maps.Point(15,39)
        );
        objectIconActive = new google.maps.MarkerImage(
            '/img/balloon-office_active.png',
            new google.maps.Size(42,39),
            new google.maps.Point(0,0),
            new google.maps.Point(15,39)
        );

        //добавление маркеров
        var latlngbounds = new google.maps.LatLngBounds();

        var $objects = $('#office_list table tr:not(:first-child,.hide,.reg)');
        $objects.each(function(){
            //получение параметров балуна для добавления на карту
            //if ($(this).data())

            var id = $(this).data('id');
            var coords = $(this).data('coords').split(',');
            //var content = $(this).find('.col1 p').html()+$(this).find('.col2 p').text();
            var title = $(this).find('.col1 p').text();

            markersObject[id] = new google.maps.Marker({
                position: new google.maps.LatLng(coords[0], coords[1]),
                //position: myLatLng,
                map: officesMap,
                icon: objectIcon,
                title: title,
                id: id
            });
            
            latlngbounds.extend(new google.maps.LatLng(coords[0], coords[1]));


            
            google.maps.event.addListener(markersObject[id], 'click', function(){
                var thismark = this;
                if (!$('#office_list tr[data-id='+thismark.id+']').hasClass('current') ) {

                    var $activeOffice = $('#office_list tr.current');
                    if ($activeOffice.length) {
                        markersObject[$activeOffice.data('id')].setIcon(objectIcon);
                    }

                    thismark.setIcon(objectIconActive);
                    //console.log(thismark.id);
                    //console.log('clickballoon');
                    $('#office_list tr').removeClass('current');
                    $('#office_list tr[data-id='+thismark.id+']').addClass('current');

                    $('#office_desc table tr').html($('#office_list tr.current').html());
                    $('#office_desc').addClass('show');
                    //показываем блок с описанием
                    //$('#objects').removeClass('hide');

                }

                    
                return false;
            });
            markersArray.push(markersObject[id]);
        });

        officesMap.setCenter( latlngbounds.getCenter(), officesMap.fitBounds(latlngbounds));  

        var zoomLevel = officesMap.getZoom();
        if(zoomLevel > 17){
            officesMap.setZoom(17);
        };
        
        var styles = [{ 
            url: "/img/claster.png",
            height: 31,
            width: 31,
            anchor: [0, 0],
            textColor: "#455054",
            textSize: 11,
            textAlign: "center"
        }];
        var mcOptions = {gridSize: 50, maxZoom: 16, styles: styles};
        //var markerCluster = new MarkerClusterer(mapBuildObjects, markersArray, mcOptions);
        var markerCluster = new MarkerClusterer(officesMap, markersArray, mcOptions);

        
        
    }
    
        
};

var contactsMaps;
var contactsMarker;
var contactsIcon;

function contactsMap() {
    
    var mapOptions = {
        zoom: 15,
        zoomControl: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.LARGE,
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        },
        scaleControl: false,
        mapTypeControl: false,
        panControl: false,
        disableDefaultUI: true,
        scrollwheel: false,
        disableDoubleClickZoom: true,
        draggable: true,
        center: new google.maps.LatLng(55.761745, 37.458299),
        
        mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, "map_style"]
        }
    };
    contactsMaps = new google.maps.Map(document.getElementById("contacts_maps"), mapOptions);

    contactsMaps.mapTypes.set("map_style", styledMap);
    contactsMaps.setMapTypeId("map_style");
    
    
    contactsIcon = new google.maps.MarkerImage(
        "/img/balloon-mig.png",
        new google.maps.Size(61, 63),
        new google.maps.Point(0,0),
        new google.maps.Point(25,63)
    );

    //var myLatLng = new google.maps.LatLng(53.241886, 50.236759);
    //var marker = new google.maps.Marker({
    contactsMarker = new google.maps.Marker({
        position: new google.maps.LatLng(55.761745, 37.458299),
        map: contactsMaps,
        icon: contactsIcon
    });
        
};


var officesMapSeo;
var markersArraySeo = [];
var markersInfoSeo = [];
var markersObjectSeo = [];
var objectIconSeo;
var objectIconActiveSeo;
var objectMarkeSeor;
function officesSeoMapInit() {
    $('#seo_map').html('');
    markersArraySeo = [];
    markersObjectSeo = [];

    var mapOptions = {
        zoom: 13,
        zoomControl: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.LARGE,
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        },
        scaleControl: false,
        mapTypeControl: false,
        panControl: false,
        disableDefaultUI: true,
        scrollwheel: false,
        disableDoubleClickZoom: true,
        draggable: true,
        mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
        }
    };
    officesMapSeo = new google.maps.Map(document.getElementById('seo_map'), mapOptions);

    officesMapSeo.mapTypes.set('map_style', styledMap);
    officesMapSeo.setMapTypeId('map_style');
    
    objectIconSeo = new google.maps.MarkerImage(
        '/img/balloon-office.png',
        new google.maps.Size(42,39),
        new google.maps.Point(0,0),
        new google.maps.Point(15,39)
    );
    objectIconActiveSeo = new google.maps.MarkerImage(
        '/img/balloon-office_active.png',
        new google.maps.Size(42,39),
        new google.maps.Point(0,0),
        new google.maps.Point(15,39)
    );

    //добавление маркеров
    var latlngbounds = new google.maps.LatLngBounds();

    var $objects = $('#seo_offices_list .office');
    $objects.each(function(){
        //получение параметров балуна для добавления на карту
        //if ($(this).data())

        var id = $(this).data('id');
        var coords = $(this).data('coords').split(',');
        //var content = $(this).find('.col1 p').html()+$(this).find('.col2 p').text();
        var title = $(this).find('.name').html();
        var address = $(this).find('.address').html();
        var graphic = $(this).find('.graphic').html();

        markersObjectSeo[id] = new google.maps.Marker({
            position: new google.maps.LatLng(coords[0], coords[1]),
            //position: myLatLng,
            map: officesMapSeo,
            icon: objectIconSeo,
            title: title,
            id: id,
            address: address,
            graphic: graphic
        });
        
        latlngbounds.extend(new google.maps.LatLng(coords[0], coords[1]));


        
        google.maps.event.addListener(markersObjectSeo[id], 'click', function(){
            var thismark = this;
            if (!$('#seo_offices_list .office[data-id='+thismark.id+']').hasClass('current') ) {

                var $activeOffice = $('#seo_offices_list .current');
                if ($activeOffice.length) {
                    markersObjectSeo[$activeOffice.data('id')].setIcon(objectIconSeo);
                }

                thismark.setIcon(objectIconActiveSeo);
                //console.log(thismark.id);
                //console.log('clickballoon');
                $('#seo_offices_list .office').removeClass('current');
                $('#seo_offices_list .office[data-id='+thismark.id+']').addClass('current');

                $('#seo_office_desc table tr').html('<td class="col1"><p>'+thismark.title+'</p></td><td class="col2">'+thismark.address+'</td><td class="col3">'+thismark.graphic+'</td>');
                $('#seo_office_desc').addClass('show');
                //показываем блок с описанием
                //$('#objects').removeClass('hide');

            }

                
            return false;
        });
        markersArraySeo.push(markersObjectSeo[id]);
    });

    officesMapSeo.setCenter( latlngbounds.getCenter(), officesMapSeo.fitBounds(latlngbounds));  

    


    var styles = [{ 
        url: "/img/claster.png",
        height: 31,
        width: 31,
        anchor: [0, 0],
        textColor: "#455054",
        textSize: 11,
        textAlign: "center"
    }];
    var mcOptions = {gridSize: 50, maxZoom: 15, styles: styles};
    //var markerCluster = new MarkerClusterer(mapBuildObjects, markersArray, mcOptions);
    var markerCluster = new MarkerClusterer(officesMapSeo, markersArraySeo, mcOptions);

    var checkZoomInterval;
    checkZoomInterval = setInterval(function(){
        var zoomLevel = officesMapSeo.getZoom();
        if(zoomLevel > 17){
            officesMapSeo.setZoom(17);
            clearInterval(checkZoomInterval);
        };
    }, 100);
    
 
};