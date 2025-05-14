import { createRoot } from 'react-dom/client';
import React from 'react';
import MoneyForm from "@/components/MoneyForm.jsx";

// Ref хранилище для компонентов MoneyForm
const moneyFormRefs = new Map();

// Expose a navigation function to the global scope
window.navigateToOffer = (url, params = {}) => {
    // Get existing URL parameters
    const urlSearchParams = new URLSearchParams(window.location.search);
    
    // Add any additional parameters
    for (const [key, value] of Object.entries(params)) {
        urlSearchParams.set(key, value);
    }
    
    // Build the destination URL
    const destinationUrl = url + (urlSearchParams.toString() ? '?' + urlSearchParams.toString() : '');
    
    // Navigate to the URL
    window.location.href = destinationUrl;
};

// Функция для установки URL редиректа для компонента
window.setRedirectUrlForApp = (appElement, url) => {
    if (!appElement) return;
    
    // Получаем уникальный ID для компонента
    const appId = appElement.dataset.appId;
    if (!appId || !moneyFormRefs.has(appId)) return;
    
    // Получаем ref и устанавливаем новый URL
    const componentRef = moneyFormRefs.get(appId);
    if (componentRef.current && componentRef.current.setRedirectUrl) {
        componentRef.current.setRedirectUrl(url);
    }
};

// Найти все элементы с классом 'app'
const containers = document.querySelectorAll('.app');

containers.forEach((container, index) => {
    // Присваиваем уникальный ID, если его нет
    if (!container.dataset.appId) {
        container.dataset.appId = `app-${index}`;
    }
    
    // Читаем настройки из data-атрибутов
    const showFormFields = container.dataset.showForm !== 'false';
    const redirectUrl = container.dataset.redirectUrl || window.parentRedirectUrl || undefined;
    
    // Отладка - вывод значений атрибутов
    console.log('Container data attributes:', {
        'data-app-id': container.dataset.appId,
        'data-show-form': container.dataset.showForm,
        'showFormFields (parsed)': showFormFields,
        'data-redirect-url': container.dataset.redirectUrl,
        'redirectUrl (parsed)': redirectUrl
    });
    
    // Add data attributes for external navigation
    container.dataset.navigate = "true";
    
    // Создаем ref для компонента
    const moneyFormRef = React.createRef();
    moneyFormRefs.set(container.dataset.appId, moneyFormRef);
    
    // Добавляем метод установки URL в DOM объект
    container.setRedirectUrl = (url) => {
        window.setRedirectUrlForApp(container, url);
    };

    const root = createRoot(container);
    root.render(
        <MoneyForm 
            ref={moneyFormRef}
            showFormFields={showFormFields} 
            redirectUrl={redirectUrl} 
        />
    );
});
