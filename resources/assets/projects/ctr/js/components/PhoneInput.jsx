import React, { useState, useImperativeHandle, forwardRef } from 'react';
import { IMaskInput } from 'react-imask';
import axios from 'axios';

const PhoneInput =
    forwardRef(({ onPhoneChange }, ref) => {
    const [phone, setPhone] = useState('');
    const [phoneError, setPhoneError] = useState('');

    useImperativeHandle(ref, () => ({
        validate: async () => {
            if (phone.length === 0) {
                setPhoneError('Введите телефон');
                return false;
            } else if (phone.length < 11) {
                setPhoneError('Введите корректный телефон (11 цифр)');
                return false;
            } else {
                return await checkPhoneUniqueness(phone);
            }
        }
    }));

    const checkPhoneUniqueness = async (phoneNumber) => {
        try {
            const response = await axios.post(`/api/check-phone/${phoneNumber}`);
            setPhoneError('Этот телефон уже используется');
            return false;
        } catch (error) {
            if (error.response && error.response.status === 404) {
                setPhoneError('');
                return true;
            } else {
                console.error('Ошибка при проверке телефона:', error);
                setPhoneError('Ошибка проверки телефона');
                return false;
            }
        }
    };

    return (
        <>
            <IMaskInput
                mask="+{7} (000) 000-00-00"
                className={`rounded-xl focus:border focus:border-blue w-full h-full ${phoneError ? 'border !border-red' : ''}`}
                radix="."
                type='text'
                unmask={true}
                onBlur={() => {
                    if (phone.length === 0) {
                        setPhoneError('Введите телефон');
                    } else if (phone.length < 11) {
                        setPhoneError('Введите корректный телефон (11 цифр)');
                    } else {
                        checkPhoneUniqueness(phone);
                    }
                }}
                onAccept={(value) => {
                    setPhone(value);
                    onPhoneChange(value);
                    if (value.length > 10) {
                        setPhoneError('');
                    }
                }}
                placeholder='Телефон'
            />
            <span className='text-red text-xs'>{phoneError}</span>
        </>
    );
});

export default PhoneInput;
