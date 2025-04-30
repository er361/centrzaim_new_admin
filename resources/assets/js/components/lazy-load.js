export let LazyLoad = new class {
    init() {
        const lazyBackgrounds = [].slice.call(document.querySelectorAll(".lazy-background"));

        if ("IntersectionObserver" in window) {
            let lazyBackgroundObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        lazyBackgroundObserver.unobserve(entry.target);
                    }
                });
            });

            lazyBackgrounds.forEach(function(lazyBackground) {
                lazyBackgroundObserver.observe(lazyBackground);
            });
        } else {
            // Загружаем все изображения с задержкой, чтобы не перегружать браузер
            setTimeout(() => {
                lazyBackgrounds.forEach(function(lazyBackground) {
                    lazyBackground.classList.add("visible");
                });
            }, 5000);
        }
    }
}