import React from "react";
import {createRoot} from 'react-dom/client';
import Autocomplete from "./components/Autocomplete.jsx";

// Найти все элементы с классом 'app'
const container = document.getElementById('fio');
const hiddenInput = document.getElementById('fioHiddenInput');

console.log('container className');
const root = createRoot(container);
root.render(
    <>
        <Autocomplete
            inputClass={container.getAttribute('inputClassName')}
            onChangeListener={(val) => {
                hiddenInput.value = val;
                // Trigger the input event to make Alpine.js updateAgreement run
                const inputEvent = new Event('input', { bubbles: true });
                hiddenInput.dispatchEvent(inputEvent);
            }}
            initialQuery={() => {
                let attribute = hiddenInput.getAttribute('initial-query');
                console.log('init query', attribute);
                const initialValue = attribute ?? '';
                // Set initial value to hidden input
                if (initialValue) {
                    hiddenInput.value = initialValue;
                }
                return initialValue;
            }}
            hasErrors={(error) => {
                console.log('set error', error)
                hiddenInput.setAttribute('has-error', error)
            } }
        />
    </>
);
