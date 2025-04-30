var regEmail = /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,4}$/i;
var regName = /^[a-zA-Z\s]+$/i;
var regNameRus = /^[А-Яа-яЁёa\s]+$/i;
var regPass = /^[a-zA-Z0-9]+$/i ;
var regDate = /(\d{2}\.\d{2}\.\d{4})/ ;
var regNum = /^\d+$/;

//функция проверки корректности заполненения полей
function validateForm(type, id) {

	var val = jQuery.trim($("#"+id).val());
	var placeholder = jQuery.trim($("#"+id).attr("data-placeholder"));
	//alert(val+" = "+placeholder);
	switch(type){

		/*ТОЛЬКО ЦИФРЫ*/
		case "number":
			//проверка поля
			val = val.replace(/\s+/g,"");
			if (val == "" || val.search(regNum) == -1 ){
				return false;
			} else{
				return true;
			}	
		break;

		/*просто проверка на НЕ пустоту поля*/
		case "required":
			//проверка поля
			if (val == "" || val == placeholder){
				return false;
			} else{
				return true;
			}	
		break;	
		/*селект проверка на НЕ пустоту поля*/
		case "required_select":
			//проверка поля
			if (val == 0){
				return false;
			} else{
				return true;
			}	
		break;

		/*просто пароля на длину*/
		case "pass":
			//проверка поля
			if (val == "" || val.length < 5){
				return false;
			} else{
				return true;
			}	
		break;	

		/*проверка на корректный email адрес*/	
		case "email":
			//проверка поля на пустоту и корректность email
			if (val == "" || val.search(regEmail) == -1 || val.length > 40 || val == placeholder){
				return false;
			} else{
				return true;
			}	
		break;	

		/*русские символы*/
		case "rusfield":
			var name = jQuery.trim($("#"+id).val());
			//проверка поля
			if (name == "" || name.search(regNameRus) == -1 ||  name.length > 40 || name.length < 2 || val == placeholder){
				return false;
			} else{
				return true;
			}	
		break;

		/*проверка чекбоксов*/
		case "checkbox_accept":
			var count = $("#"+id+" input:checkbox:checked").length;
			if (count == "0"){
				return false;
			} else{
				return true;
			}	
		break;

		/*мобильный телефон*/
		case "number_phone":
			var name = jQuery.trim($("#"+id).val());
			//var name_n = parseInt(name);
			name = name.replace("8 (", "");
			name = name.replace(")","");
			name = name.replace("-","");
			name = name.replace("-","");
			name = name.replace(" ", "");

			if (name == "" || name.search(regNum) == -1 || name.length < 10){
				return false;
			} else {
				return true;
			}
		break;

		/*мобильный телефон + код города*/
		case "mobile_phone":
			var name = jQuery.trim($("#"+id).val());
			var name_1 = name.substr(0,1);
			if (name == "" || name.search(regNum) == -1 || name.length < 10 || name_1 === '+' || name_1 === '-' || name_1.length < 1 || name_1 == 0 || name_1 == 1 || name_1 == 2 || name_1 == 7){
				return false;
			} else {
				return true;
			}
		break;

		/*мобильный телефон + код города + м.б. пустое*/
		case "mobile_phone_empty":
			var name = jQuery.trim($("#"+id).val());
			name = name.replace("(","");
			name = name.replace(")","");
			name = name.replace(" ","");
			name = name.replace(" ","");
			name = name.replace(" ","");
			name = name.replace(" ","");
			name = name.replace(" ","");
			name = name.replace(" ","");
			var name_1 = name.substr(0,1);
			if (name == "") {
				return true;
			} else {
				if (name.search(regNum) == -1 || name.length < 10 || name_1 == 0 || name_1 == 1 || name_1 == 2 || name_1 == 7){
					return false;
				} else {
					return true;
				}
			}
			
		break;

		/*№ договора*/
		case "number_dogovor":
			//проверка поля
			val = val.replace(/\s+/g,"");
			if (val == "") {
				return true;
			} else {
				if (val.search(regNum) == -1 || val.length != 10 || val == placeholder ){
					return false;
				} else{
					return true;
				}	
			}
			
		break;
		
	} //end switch
}//end validateForm
