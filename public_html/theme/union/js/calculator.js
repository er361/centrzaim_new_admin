jQuery(function() {
    var tarif = 5,
        client = 1, 
        revenue = 0,
        check = 4000,
        date = 1,
        RevenueGrowth = 0,
        rent = 0,
        percentage = 0,
        ClientsGrowth = 0,
    	RentPerMonth = 0,
        TransactionCosts = 0,
        RevenueGrownthTerminal = 0;
    // Функция расчета
    function recount() {
        // Считаем приост клиентов
        ClientsGrowth = client * 0.3;
        // Округляем переменную до целого числа
        ClientsGrowth = +ClientsGrowth.toFixed();
        // Считаем аренду терминала, преобразуем в строку, и переписываем строку так чтобы был пробел каждые три символа
        rent = String(date * RentPerMonth).replace(/(\d)(?=(\d{3})+(\D|$))/g, '$1 ');
        // Считаем расходы по транзакциям
        TransactionCosts = ((client + ClientsGrowth) * check * date) / 100 * tarif;
        // Считаем прирост выручки от терминала
        RevenueGrownthTerminal = check + ( check * 0.01 * date );
        // Преобразуем строку в число и округляем чсило до целого
        TransactionCosts = +TransactionCosts.toFixed();
        // Считаем чистую выручку преобразуем в строку и округляем ее до целого значения
        revenue = (((client + ClientsGrowth) * date * check) - TransactionCosts).toFixed();
        // Переписываем так чтобы был пробел каждые три символа
        revenue = revenue.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
        // Преобразуем в строку и переписываем так чтобы бы пробел каждые три символа
        RevenueGrownthTerminal = String(RevenueGrownthTerminal).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
        // Выводим данные
        jQuery("#revenue span").html(revenue + ' руб');
        jQuery("#ClientsGrowth span").html(ClientsGrowth + ' чел.');
        jQuery("#rent span").html(rent + ' руб.');
        jQuery("#TransactionCosts span").html(TransactionCosts + ' руб.');  
        jQuery("#RevenueGrownthTerminal span").html(RevenueGrownthTerminal + ' руб.');
        jQuery("#tarifpercentage span").html(tarif + ' %');
};
    // Вызываем функцию расчета
	recount();
    // Если в #tarif изменилось значение то выполняется функция:
    jQuery('#tarif').change(function() {
        tarif = jQuery('#tarif option:selected').val();
        RentPerMonth = 0;
        if (tarif == 3.3) {
        RentPerMonth = 300;
        } else if (tarif == 2.2) {
            RentPerMonth = 900;
        }
        // Вызываем функцию расчета, чтобы пересчитать после зменения 
        recount();
    });
    // Вывод данных из слайдера, при условии что он был сдвинут, слайдер выдает переменную в виде строки, преобразуем ее в число добавив унарный +
    $(document).on("change keyup", "#client", function() {
        client = +$(this).val();
        $("#client-slider").slider("value", client);
        // Вызываем функцию расчета, чтобы пересчитать после зменения 
        recount();
    });
    // Вывод данных из слайдера, при условии что он был сдвинут, слайдер выдает переменную в виде строки, преобразуем ее в число добавив унарный +
    $(document).on("change keyup", "#check", function() {
        check = +$(this).val();
        $("#check-slider").slider("value", check);
        // Вызываем функцию расчета, чтобы пересчитать после зменения 
        recount();
    });
    // Вывод данных из слайдера, при условии что он был сдвинут, слайдер выдает переменную в виде строки, преобразуем ее в число добавив унарный +
    $(document).on("change keyup", "#date", function() {
        date = +$(this).val();
        $("#date-slider").slider("value", date);
        // Вызываем функцию расчета, чтобы пересчитать после зменения 
        recount();
    });
});

// слайдер 2
$(function() {
    $("#check-slider").slider({
        range: "min",
        value: 5000,
        min: 1000,
        step: 100,
        max: 60000,
        slide: function(event, ui) {
            $("#check").val(ui.value).trigger("change");
        }
    });
    $("#check").val($("#check-slider").slider("value"));
});
// слайдер 3
$(function() {
    $("#date-slider").slider({
        range: "min",
        value: 3,
        min: 1,
        max: 60,
        slide: function(event, ui) {
            $("#date").val(ui.value).trigger("change");
        }
    });
    $("#date").val($("#date-slider").slider("value"));
});