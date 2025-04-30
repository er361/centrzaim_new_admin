$(document).ready(function () {
    $('[name="reg_city_name"]').kladr({
        type: $.kladr.type.city
    });
    $('[name="fact_city_name"]').kladr({
        type: $.kladr.type.city
    });
    $('[name="fact_region_name"]').kladr({
        type: $.kladr.type.region
    });
    $('[name="reg_region_name"]').kladr({
        type: $.kladr.type.region
    });
    $('[name="birthplace"]').kladr({
        type: $.kladr.type.city
    });
});