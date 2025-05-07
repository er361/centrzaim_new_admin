import {initSliders, updateTime} from "@/lib/lib.js";


document.addEventListener('DOMContentLoaded', function () {

    initSliders();

    updateTime('.timeGetMoney');

    setInterval(updateTime, 60000);

    console.log('DOM is ready')

    function toggleMenu() {
        document.getElementById('showMenu').classList.toggle('rotate-[135deg]');
        document.getElementById('showMenuContent').classList.toggle('!w-full');
    }

    // Обработчик клика для кнопки открытия/закрытия меню
    // document.getElementById('showMenu').addEventListener('click', toggleMenu);

    // Обработчики кликов для ссылок мобильного меню
    document.querySelectorAll('.mobile-menu-link').forEach((link) => {
        link.addEventListener('click', function () {
            toggleMenu(); // Вызываем функцию toggleMenu при клике на ссылку
        });
    });

    // next step
    const btnNextStep = document.querySelectorAll("[data-next]");
    if (btnNextStep) {
        btnNextStep.forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    }
})

console.log('init js file loaded')
