import './lib/init.js'

console.log('Hello from app.js');

// faq
const faqItems = document.querySelectorAll(".faq__item");

if (faqItems) {
    faqItems.forEach((faq) => {
        faq.addEventListener("click", () => {
            faq.classList.toggle("active");
        });
    });
}

// mobile menu
const btnMenu = document.querySelector(".mobile-action");
const mobileMenu = document.querySelector(".mobile-menu");
const mobileMenuLinks = mobileMenu.querySelectorAll(".mobile-menu__link");
btnMenu.addEventListener("click", () => {
    mobileMenu.classList.toggle("active");
    btnMenu.classList.toggle("active");
    document.body.classList.toggle("overflow");
});

mobileMenuLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
        mobileMenu.classList.toggle("active");
        btnMenu.classList.toggle("active");
        document.body.classList.toggle("overflow");
    });
});

window.validateAndSubmitForm = function (formId, validateUrl, goalName = null, goalId = 99015882 ) {
    const form = document.getElementById(formId);

    if (!form) {
        console.error(`Форма с id "${formId}" не найдена.`);
        return;
    }

form.addEventListener('submit', function (event) {
        console.log('validate and submit form', formId, validateUrl, goalName, goalId);
        event.preventDefault(); // Останавливаем стандартную отправку формы

        let isFormSubmitted = false;

        // Функция для безопасной отправки формы (только один раз)
        const submitForm = () => {
            if (isFormSubmitted) return;
            isFormSubmitted = true;
            console.log('Отправляем форму...');
            form.submit();
        };

        fetch(validateUrl, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                console.log('Response is ok', response);

                if (goalName && typeof ym === 'function') {
                    try {
                        // Отправляем цель метрики
                        ym(goalId, 'reachGoal', goalName);
                        console.log('Цель отправлена в Яндекс.Метрику:', goalName);
                        
                        // Небольшая задержка для учета метрики, затем отправляем форму
                        setTimeout(() => {
                            submitForm();
                        }, 100);
                    } catch (error) {
                        console.log('Ошибка отправки цели в Яндекс.Метрику:', error);
                        submitForm();
                    }
                } else {
                    // Если нет цели или функции ym, отправляем форму сразу
                    console.log('Яндекс.Метрика недоступна или goalName не указан');
                    submitForm();
                }
            } else {
                console.log('Response is not ok', response);
                submitForm();
            }
        })
        .catch(error => {
            console.error('Ошибка отправки формы:', error);
            submitForm();
        });
    });;
}

