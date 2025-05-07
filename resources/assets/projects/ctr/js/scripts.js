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
