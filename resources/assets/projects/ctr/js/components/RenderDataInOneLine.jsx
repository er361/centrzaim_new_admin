import React, {useEffect, useState} from 'react';
import {renderName, validateFullName} from "@/components/lib/utils.js";

const RenderDataInOneLine = ({names, match, query, highlightIndex, setHighlightIndex, selectResult, type}) => {
    const [highlightedNames, setHighlightedNames] = useState([]);
    const [completedFullName, setCompletedFullName] = useState(false);

    useEffect(() => {
        if (match.first_name && match.last_name && match.father_name)
            setCompletedFullName(true)
        else
            setCompletedFullName(false)
    }, [match,query]);


    const getHighlightedText = (text, highlight) => {
        if (!highlight.trim()) {
            return text;
        }

        const index = text.toLowerCase().indexOf(highlight.toLowerCase());
        if (index === -1) {
            return text;
        }

        return (
            <>
                {text.substring(0, index)}
                <span className='text-blue'>
                    {text.substring(index, index + highlight.length)}
                </span>
                {text.substring(index + highlight.length)}
            </>
        );
    };

    useEffect(() => {
        const newHighlightedNames = names.map(name => {
            const renderedName = renderName(name, match);
            return getHighlightedText(renderedName, query);
        });
        setHighlightedNames(newHighlightedNames);
    }, [names]);

    return (
        <div>
            {!completedFullName && query && highlightedNames.length > 0 && (
                <ul className='bg-white relative z-10'>
                    <p className='text-sm opacity-55'>Выберите вариант или продолжите ввод</p>
                    {highlightedNames.map((result, index) => (
                        <li
                            key={index}
                            onMouseDown={(e) => {
                                e.preventDefault()
                                console.log('click', index)
                                selectResult(index)
                            }}
                            onMouseEnter={() => setHighlightIndex(index)}
                            className={`autocomplete-item cursor-pointer ${highlightIndex === index ? 'bg-gray-200' : ''}`}
                        >
                            {result}
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
};

export default RenderDataInOneLine;
