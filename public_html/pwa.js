/*
   Установить PWA (progressive web application)

   Условия установки:
   * PWA еще не установлен;
   * Сайт имеет настроенный файл web manifest;
   * Сайт работает или через HTTPS, или на домене localhost;
   * Установлен service worker с обработчиком события fetch.

   Поддерживаемые браузеры:
   https://developer.mozilla.org/en-US/docs/Web/API/BeforeInstallPromptEvent#browser_compatibility

   Содержание:
   * Настройки;
   * CSS;
   * HTML;
   * JS:
     * установка воркера;
     * поведение поп-апа;
     * установка меток на ссылки
 */

/* Настройки */

// Цвет кнопки
const pwaButtonColor = '#000000';
// Иконка поп-апа
const pwaIcon = 'data:image/png;base64,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';

if (typeof window !== 'undefined') {
    window.onload = function () {

        /*  CSS */
        // language=CSS
        const styles = `
        .pwa-popup {
            text-align: center;
            display: none;
            position: fixed;
            top: 8px;
            left: 48px;
            padding: 36px;
            border-radius: 4px;
            background: white;
            z-index: 10;
        }
        .pwa-popup_title {
            font-family: "Open Sans", sans-serif;
            font-style: normal;
            font-weight: 600;
            font-size: 21px;
            line-height: 29px;
            text-align: center;
            color: #272727;
            margin-bottom: 30px;
        }
        .pwa-popup_title-description {
            font-family: "Open Sans", sans-serif;
            font-style: normal;
            font-weight: normal;
            font-size: 18px;
            line-height: 25px;
            color: #272727;
            margin-bottom: 27px;
        }
        
        .pwa-popup_buttons-container {
            display: flex;
            flex-direction: row;
            justify-content: space-evenly;
            margin: 0 45px;
            clear: both;
        }
        .pwa-popup_button {
            padding: 8px 26px;
            font-family: "Open Sans", sans-serif;
            font-style: normal;
            font-weight: normal;
            font-size: 18px;
            line-height: 25px;
            text-align: center;
            color: #252526;
            text-decoration: none;
        }
        .pwa-popup_button-accept {
            background: ${pwaButtonColor};
            border-radius: 4px;
            color: #FFFFFF;
        }
        .pwa-popup_button:first-child {
            margin-right: 30px;
        }
        @media (max-width: 600px) {
            .pwa-popup {
                width: 100%;
                top: 0;
                left: 0;
                padding: 8px 16px 12px 16px
            }
            .pwa-popup:before {
                content: none;
            }
            .pwa-popup:after {
                border-radius: 0;
            }
            .pwa-popup_image {
                top: -16px;
            }
            .pwa-popup_title, .pwa-popup_title-description {
                font-size: 16px;
            }
            .pwa-popup_buttons-container {
                margin: 0;
            }
        }
        `
        const styleSheet = document.createElement("style")
        styleSheet.innerText = styles
        document.head.appendChild(styleSheet)

        /* HTML */
        const html = `
        <div class="pwa-popup">
            <img class="pwa-popup_image" src="${pwaIcon}" alt="PWA">
            <div class="pwa-popup_title">Установите наше<br>мобильное приложение</div>
            <div class="pwa-popup_buttons-container">
                <a class="pwa-popup_button pwa-popup_button-deny" href="#">Не сейчас</a>
                <a class="pwa-popup_button pwa-popup_button-accept" href="#">Установить</a>
            </div>
        </div>
        `
        const pwaHtml = document.createElement("div")
        pwaHtml.innerHTML = html
        document.body.appendChild(pwaHtml)

        /* JS */

        /* Установка воркера */
        if ('serviceWorker' in navigator) {
            // Положить этот файл в корень или указать путь здесь
            navigator.serviceWorker.register('./pwa.js', {scope: '/'})
                .then((reg) => {
                    // registration worked
                    console.log('Worker registration succeeded. Scope is ' + reg.scope);
                }).catch((error) => {
                // registration failed
                console.log('Worker registration failed with ' + error);
            });
        }

        /* Поведение попапа */
        const pwaButtonAccept = document.querySelector(".pwa-popup_button-accept")
        const pwaButtonDeny = document.querySelector(".pwa-popup_button-deny")
        const pwaPopup = document.querySelector(".pwa-popup")
        let deferredPrompt;
        // При установке добавить в URL source=pwa. В ссылки добавить sys_sub2=pwa если URL содержит source=pwa
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Update UI to notify the user they can add to home screen
            pwaPopup.style.display = "block"
            pwaButtonAccept.addEventListener('click', (e) => {
                e.preventDefault()
                // hide our user interface that shows our A2HS button
                pwaPopup.style.display = "none"
                deferredPrompt.prompt();
                // Wait for the user to respond to the prompt
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the A2HS prompt');
                        // Добавить ?source=pwa в URL
                        const currentUrl = new URL(window.location.href)
                        currentUrl.searchParams.append("source", "pwa")
                        window.history.pushState(null, document.title, currentUrl.toString())
                        markLinks()
                    } else {
                        console.log('User dismissed the A2HS prompt');
                    }
                    deferredPrompt = null;
                });
            });
            pwaButtonDeny.addEventListener('click', (e) => {
                e.preventDefault()
                pwaPopup.style.display = 'none'
            })
        });
        // Если PWA, добавить sys_sub4=pwa в ссылки
        if (window.location.href.indexOf("source=pwa") !== -1 || window.matchMedia('(display-mode: standalone)').matches) {
            console.log("PWA detected")
            markLinks()
        }
        function markLinks() {
            document.querySelectorAll('a').forEach(link => {
                if (link.href) {
                    const linkHref = new URL(link.href)
                    linkHref.searchParams.append("sys_sub4", "pwa")
                    link.href = linkHref.toString()
                }
            })
        }
    }
}

/* Worker */
self.addEventListener('fetch', event => {
    // Ничего не делать
});