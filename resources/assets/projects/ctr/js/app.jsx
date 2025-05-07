import { createRoot } from 'react-dom/client';
import MoneyForm from "@/components/MoneyForm.jsx";

// Найти все элементы с классом 'app'
const containers = document.querySelectorAll('.app');

containers.forEach(container => {
    const root = createRoot(container);
    root.render(
        <>
            <MoneyForm/>
        </>
    );
});
