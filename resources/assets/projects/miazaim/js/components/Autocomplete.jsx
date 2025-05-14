import React, {useState, useEffect, forwardRef, useImperativeHandle, useRef, useLayoutEffect} from 'react';
import axios from 'axios';
import RenderDataInOneLine from "@/components/RenderDataInOneLine.jsx";
import {renderName, validateFullName} from "@/components/lib/utils.js";

const Autocomplete =
    forwardRef(({
                    wrapperClass,
                    inputClass,
                    onChangeListener,
                    hasErrors,
                    initialQuery = ''
                }, ref) => {
        const inputRef = useRef(null);
        const [query, setQuery] = useState(initialQuery);

        const [firstName, setFirstName] = useState('');
        const [lastName, setLastName] = useState('');
        const [fatherName, setFatherName] = useState('');
        const [loading, setLoading] = useState(false);

        const [type, setType] = useState('');

        const [results, setResults] = useState([]);
        const [match, setMatch] = useState({});
        const [highlightIndex, setHighlightIndex] = useState(0);

        const [fullNameError, setFullNameError] = useState('');


        useImperativeHandle(ref, () => ({
            validate: async () => {
                const errors = validateFullName(query);
                if (errors.length > 0) {
                    setFullNameError(`Введите ${errors.join(', ')}`);
                    return false;
                } else {
                    setFullNameError('');
                    return true;
                }
            }
        }))

        useEffect(() => {
            fullNameError ? hasErrors(1): hasErrors(0)
        }, [fullNameError]);

        useEffect(() => {
            return () => {
                setResults([]);
                setFirstName('');
                setLastName('');
                setFatherName('');
                setType('');
                setQuery('');
                setHighlightIndex(0);
            };
        }, []);

        const handleKeyDown = (event) => {
            if (event.key === 'ArrowDown') {
                setHighlightIndex((prevIndex) => {
                    prevIndex = prevIndex ?? 0;
                    return (prevIndex + 1) % results.length;
                });
            } else if (event.key === 'ArrowUp') {
                setHighlightIndex((prevIndex) => (prevIndex - 1 + results.length) % results.length);
            } else if (event.key === 'Enter') {
                selectResult(highlightIndex);
            }
        };

        useEffect(() => {
            const strings = query.split(' ');
            let length = strings.length;
            switch (length) {
                case 1:
                    setType('last_name');
                    setLastName(strings[0]);
                    break;
                case 2:
                    setType('first_name');
                    setLastName(strings[1]);
                    break;
                case 3:
                    setType('father_name');
                    setLastName(strings[2]);
                    break;
                default:
                    setType('last_name');
            }
        }, [query]);

        async function fetchNames(query = '', type = '') {
            setLoading(true)
            const encodedQuery = encodeURIComponent(query);
            try {
                const response = await axios.get('api/autocomplete/search',
                    {params: {query: encodedQuery, type}});
                setResults(response.data.names);
                setMatch(response.data.match)
            } catch (error) {
                console.error('There was an error fetching the results!', error);
            }
        }

        useEffect(() => {
            setLoading(false);
        }, [results]);


        useEffect(() => {
            const search = query;
            if (search.trim().length < 3) return;

            const delayDebounceFn = setTimeout(() => {
                if (search.trim() !== '') {
                    fetchNames(search, type);
                } else {
                    setResults([]);
                }
            }, 300);

            return () => clearTimeout(delayDebounceFn);
        }, [query]);

        useEffect(() => {
            handleInputChange(query, true)
        }, [query]);

        const handleInputChange = (event, manual = false) => {
            let value;
            if (!manual)
                value = event.target.value;
            else
                value = event;


            // Проверяем, что все символы в строке являются кириллическими
            if (/^[а-яА-ЯёЁ\s]*$/.test(value)) {
                setQuery(value);
                onChangeListener(value)
            }

            // Очищаем ошибки при изменении ввода
            setFullNameError('');
        };


        const selectResult = async (highlightIndex) => {
            const fullName = renderName(results[highlightIndex], match);
            if (type === 'father_name')
                setQuery(fullName);
            else
                setQuery(fullName + ' ');

            setHighlightIndex(0);
            console.log('select res - before focus')
        };

        useLayoutEffect(() => {
            if (inputRef.current && query.length > 0) {
                inputRef.current.focus();
                console.log('focus')
            }
        }, [query]);

        // useEffect(() => {
        //     document.addEventListener('click', (event) => {
        //         if (event.target.closest('.autocomplete-item')) return;
        //         setResults([]);
        //     })
        // }, []);

        return (
            <div onBlur={(event) => {
                console.log('blur')
                // Используем trim для удаления пробелов с краев
                const errors = validateFullName(query);
                if (errors.length > 0) {
                    setFullNameError(`Введите ${errors.join(', ')}`);
                } else {
                    setFullNameError('');
                }

                setResults([])
            }}
                 className={`${wrapperClass}`}>
                {/*<h2>type - {type}</h2>*/}
                {/*<h2>last_name - {lastName}</h2>*/}
                {/*<h2>first_name - {firstName}</h2>*/}
                {/*<h2>father_name - {fatherName}</h2>*/}
                {/*<h2>query {query}; type - {type}</h2>*/}
                {/*<h2>highlightIndex - {highlightIndex}</h2>*/}

                <input
                    className={`w-full h-full focus:border focus:border-blue ${fullNameError ? 'border !border-red' : ''} ${inputClass}`}
                    type="text"
                    value={query}
                    onChange={handleInputChange}
                    onKeyDown={handleKeyDown}
                    placeholder="Введите ФИО"
                    ref={inputRef}
                />
                <span className='text-red text-xs'>{fullNameError}</span>

                <RenderDataInOneLine
                    type={type}
                    query={query}
                    loading={loading}
                    names={results}
                    match={match}
                    highlightIndex={highlightIndex}
                    setHighlightIndex={setHighlightIndex}
                    selectResult={selectResult}
                />
            </div>
        );
    });

export default Autocomplete;
