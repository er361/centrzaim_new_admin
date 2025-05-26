export function updateTime(selector) {
    // Получаем все элементы с классом 'time-plus-15'
    const timeElements = document.querySelectorAll(selector);

    // Текущее время + 15 минут
    const now = new Date();
    now.setMinutes(now.getMinutes() + 15);

    // Форматируем время, например, "14:25"
    const timeString = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();

    // Устанавливаем время во все найденные элементы
    timeElements.forEach(element => {
        element.textContent = timeString;
    });
}

// window.validateAndSubmitForm = function (formId, validateUrl, goalName = null, goalId = 96714912 ) {
//     const form = document.getElementById(formId);
//     if (!form) {
//         console.error(`Форма с id "${formId}" не найдена.`);
//         return;
//     }
//
//     form.addEventListener('submit', function (event) {
//         event.preventDefault(); // Останавливаем стандартную отправку формы
//
//         let isFormSubmitted = false; // Флаг для предотвращения повторной отправки
//
//         fetch(validateUrl, {
//             method: 'POST',
//             body: new FormData(this),
//             headers: {
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//             }
//         })
//             .then(response => {
//                 if (response.ok) {
//                     if (!isFormSubmitted) {
//                         isFormSubmitted = true;
//                         if (goalName && typeof ym === 'function') {
//                             ym(goalId, 'reachGoal', goalName, null, function () {
//                                 console.log('Цель отправлена, отправляем форму...');
//                                 form.submit();
//                             });
//                         } else {
//                             form.submit();
//                         }
//
//                         setTimeout(() => {
//                             if (!isFormSubmitted) {
//                                 isFormSubmitted = true;
//                                 console.log('Отправка формы без цели, потому что заблокировано в адблоке');
//                                 form.submit();
//                             }
//                         }, 500);
//                     }
//                 } else {
//                     if (!isFormSubmitted) {
//                         isFormSubmitted = true;
//                         form.submit();
//                     }
//                 }
//             })
//             .catch(error => {
//                 console.error('Ошибка отправки формы:', error);
//                 if (!isFormSubmitted) {
//                     isFormSubmitted = true;
//                     form.submit();
//                 }
//             });
//     });
// }



console.log('lib js file loaded');
