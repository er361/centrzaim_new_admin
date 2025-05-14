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
            onChangeListener={(val) => hiddenInput.value = val}
            initialQuery={() => {
                let attribute = hiddenInput.getAttribute('initial-query');
                console.log('init query', attribute);
                return attribute ?? ''
            }}
            hasErrors={(error) => {
                console.log('set error', error)
                hiddenInput.setAttribute('has-error', error)
            } }
        />
    </>
);
