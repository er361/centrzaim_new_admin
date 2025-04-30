/**
 * Sticky Header
 */
$(".container-wrapper").waypoint(function() {
    $(".navbar").toggleClass("navbar-sticky-function");
    $(".navbar").toggleClass("navbar-sticky");
    return false;
}, { offset: "-20px" });



/**
 * Main Menu Slide Down Effect
 */

// Mouse-enter dropdown
$('#navbar li').on("mouseenter", function() {
    $(this).find('ul').first().stop(true, true).delay(350).slideDown(500, 'easeInOutQuad');
});

// Mouse-leave dropdown
$('#navbar li').on("mouseleave", function() {
    $(this).find('ul').first().stop(true, true).delay(100).slideUp(150, 'easeInOutQuad');
});



/**
 * Effect to Bootstrap Dropdown
 */
$('.bt-dropdown-click').on('show.bs.dropdown', function(e) {
    $(this).find('.dropdown-menu').first().stop(true, true).slideDown(500, 'easeInOutQuad');
});
$('.bt-dropdown-click').on('hide.bs.dropdown', function(e) {
    $(this).find('.dropdown-menu').first().stop(true, true).slideUp(250, 'easeInOutQuad');
});



/**
 * Icon Change on Collapse
 */
$('.collapse.in').prev('.panel-heading').addClass('active');
$('.bootstarp-accordion, .bootstarp-toggle').on('show.bs.collapse', function(a) {
    $(a.target).prev('.panel-heading').addClass('active');
})
    .on('hide.bs.collapse', function(a) {
        $(a.target).prev('.panel-heading').removeClass('active');
    });



/**
 * Slicknav - a Mobile Menu
 */
$('#responsive-menu').slicknav({
    duration: 300,
    easingOpen: 'easeInExpo',
    easingClose: 'easeOutExpo',
    closedSymbol: '<i class="fa fa-plus"></i>',
    openedSymbol: '<i class="fa fa-minus"></i>',
    prependTo: '#slicknav-mobile',
    allowParentLinks: true,
    label:""
});



/**
 * Smooth scroll to anchor
 */
$('a.anchor[href*=#]:not([href=#])').on("click",function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
        if (target.length) {
            $('html,body').animate({
                scrollTop: (target.offset().top - 120) // 70px offset for navbar menu
            }, 1000);
            return false;
        }
    }
});



/**
 * Sign-in Modal
 */
var $formLogin = $('#login-form');
var $formLost = $('#lost-form');
var $formRegister = $('#register-form');
var $divForms = $('#modal-login-form-wrapper');
var $modalAnimateTime = 300;

$('#login_register_btn').on("click", function () { modalAnimate($formLogin, $formRegister) });
$('#register_login_btn').on("click", function () { modalAnimate($formRegister, $formLogin); });
$('#login_lost_btn').on("click", function () { modalAnimate($formLogin, $formLost); });
$('#lost_login_btn').on("click", function () { modalAnimate($formLost, $formLogin); });
$('#lost_register_btn').on("click", function () { modalAnimate($formLost, $formRegister); });

function modalAnimate ($oldForm, $newForm) {
    var $oldH = $oldForm.height();
    var $newH = $newForm.height();
    $divForms.css("height",$oldH);
    $oldForm.fadeToggle($modalAnimateTime, function(){
        $divForms.animate({height: $newH}, $modalAnimateTime, function(){
            $newForm.fadeToggle($modalAnimateTime);
        });
    });
}



/**
 * select2 - custom select
 */
$(".select2-single").select2({allowClear: true});
$(".select2-no-search").select2({dropdownCssClass : 'select2-no-search',allowClear: true});
$(".select2-multi").select2({});



/**
 * Show more-less button
 */
$('.btn-more-less').on("click",function(){
    $(this).text(function(i,old){
        return old=='Show more' ?  'Show less' : 'Show more';
    });
});



/**
 *  Arrow for Menu has sub-menu
 */
$(".navbar-arrow > ul > li").has("ul").children("a").append("<i class='arrow-indicator fa fa-angle-down'></i>");
$(".navbar-arrow ul ul > li").has("ul").children("a").append("<i class='arrow-indicator fa fa-angle-right'></i>");



/**
 *  Placeholder
 */
$("input, textarea").placeholder();



/**
 * Bootstrap tooltips
 */
$('[data-toggle="tooltip"]').tooltip();



/**
 * responsivegrid - layout grid
 */
$('.grid').responsivegrid({
    gutter : '0',
    itemSelector : '.grid-item',
    'breakpoints': {
        'desktop' : {
            'range' : '1200-',
            'options' : {
                'column' : 20,
            }
        },
        'tablet-landscape' : {
            'range' : '1000-1200',
            'options' : {
                'column' : 20,
            }
        },
        'tablet-portrate' : {
            'range' : '767-1000',
            'options' : {
                'column' : 20,
            }
        },
        'mobile-landscape' : {
            'range' : '-767',
            'options' : {
                'column' : 10,
            }
        },
        'mobile-portrate' : {
            'range' : '-479',
            'options' : {
                'column' : 10,
            }
        },
    }
});



/**
 * Payment Option
 */
$("div.payment-option-form").hide();
$("input[name$='payments']").on("click",function() {
    var test = $(this).val();
    $("div.payment-option-form").hide();
    $("#" + test).show();
});



/**
 * ionRangeSlider - range slider
 */

// Price Range Slider
$("#price_range").ionRangeSlider({
    type: "double",
    grid: true,
    min: 0,
    max: 1000,
    from: 200,
    to: 800,
    prefix: "$"
});

var $range = $("#range_33");
$range.ionRangeSlider({
    min: 0,
    max: 100000,
    from: 10000,
    from_min: 1000,
    from_max: 70000,
    step: 1000,
});

var $range = $("#range_34");
$range.ionRangeSlider({
    type: "single",
    min: 1,
    max: 300,
    from: 20,
    from_max: 300,
    step: 1,
    prefix: "&nbsp;&nbsp;&nbsp;",
    postfix: "&nbsp;&nbsp;"
});

$range.on("change", function () {
    var $this = $(this),
        value = $this.prop("value");

    console.log("Value: " + value);
});

$(document).ready(function () {
    var $range = $("#range_46"),
        $result = $("#result_46");

    var track = function (data) {
        $result.html(" " + data.from);
    };

    $range.ionRangeSlider({
        type: "single",
        min: 1000,
        max: 100000,
        from: 30000,
        step: 1000,
        onStart: track,
        onChange: track,
        onFinish: track,
        onUpdate: track
    });
});




// Star Range Slider
$("#star_range").ionRangeSlider({
    type: "double",
    grid: false,
    from: 1,
    to: 2,
    values: [
        "<i class='fa fa-star'></i>",
        "<i class='fa fa-star'></i> <i class='fa fa-star'></i>",
        "<i class='fa fa-star'></i> <i class='fa fa-star'></i> <i class='fa fa-star'></i>",
        "<i class='fa fa-star'></i> <i class='fa fa-star'></i> <i class='fa fa-star'></i> <i class='fa fa-star'></i>",
        "<i class='fa fa-star'></i> <i class='fa fa-star'></i> <i class='fa fa-star'></i> <i class='fa fa-star'></i> <i class='fa fa-star'></i>"
    ]
});



/**
 * slick
 */
$('.gallery-slideshow').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    speed: 500,
    arrows: true,
    fade: true,
    asNavFor: '.gallery-nav'
});
$('.gallery-nav').slick({
    slidesToShow: 7,
    slidesToScroll: 1,
    speed: 500,
    asNavFor: '.gallery-slideshow',
    dots: false,
    centerMode: true,
    focusOnSelect: true,
    infinite: true,
    responsive: [
        {
            breakpoint: 1199,
            settings: {
                slidesToShow: 7,
            }
        },
        {
            breakpoint: 991,
            settings: {
                slidesToShow: 5,
            }
        },
        {
            breakpoint: 767,
            settings: {
                slidesToShow: 5,
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 3,
            }
        }
    ]
});



/**
 * Back To Top
 */
$(window).scroll(function(){
    if($(window).scrollTop() > 500){
        $("#back-to-top").fadeIn(200);
    } else{
        $("#back-to-top").fadeOut(200);
    }
});
$('#back-to-top').on("click",function() {
    $('html, body').animate({ scrollTop:0 }, '800');
    return false;
});
