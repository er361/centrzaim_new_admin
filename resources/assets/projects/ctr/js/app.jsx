import { createRoot } from 'react-dom/client';
import MoneyForm from "@/components/MoneyForm.jsx";

// Найти все элементы с классом 'app'
const containers = document.querySelectorAll('.app');

containers.forEach(container => {
    // Check if the container has a data attribute to show only the button
    const showFormFields = container.dataset.showForm !== 'false';

    const root = createRoot(container);
    root.render(
        <>
            <MoneyForm showFormFields={showFormFields} />
        </>
    );
});
