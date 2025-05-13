import React, {useEffect, useRef, useState, forwardRef, useImperativeHandle} from 'react';
import Autocomplete from "@/components/Autocomplete.jsx";
import axios from 'axios';
import PhoneInput from "@/components/PhoneInput.jsx";

const parentRedirect = 'https://maxzaim.com/668278707e019'

// Export redirect URL for external access
window.parentRedirectUrl = parentRedirect;


const MoneyForm = forwardRef(({ showFormFields = true, redirectUrl = parentRedirect }, ref) => {
    const [phone, setPhone] = useState('');
    const [privacy, setPrivacy] = useState(true);
    const [phoneError, setPhoneError] = useState(false);
    const [fullName, setFullName] = useState('');
    const [runFullNameValidation, setRunFullNameValidation] = useState(false);
    const [customRedirectUrl, setCustomRedirectUrl] = useState(redirectUrl);
    const phoneInputRef = useRef(null);
    const fullNameRef = useRef(null);


    useEffect(() => {
        console.log('phone error', phoneError)
    }, [phoneError]);
    
    // Обновляем URL редиректа, если изменился props
    useEffect(() => {
        setCustomRedirectUrl(redirectUrl);
    }, [redirectUrl]);
    
    // Метод для установки URL редиректа извне
    useImperativeHandle(ref, () => ({
        setRedirectUrl: (url) => {
            setCustomRedirectUrl(url);
        }
    }));

    const handleSubmit = async (event) => {
        event.preventDefault();

        // If we're only showing the button (not the form fields)
        // just open the redirect URL directly
        if (!showFormFields) {
            const urlSearchParams = new URLSearchParams(window.location.search);
            window.location.href = customRedirectUrl + '?' + urlSearchParams.toString();
            return;
        }

        if(!privacy)
            return;

        if (phoneInputRef.current && fullNameRef.current) {

            const isPhoneValid = await phoneInputRef.current.validate();
            const isFullNameValid = await fullNameRef.current.validate();

            if(!isFullNameValid || !isPhoneValid ) {
                console.log('isPhoneValid', isPhoneValid)
                console.log('isFullNameValid', isFullNameValid)
                return;
            }
        }

        const storedUtmSource = localStorage.getItem('utm_source') || '';
        const storedUtmMedium = localStorage.getItem('utm_medium') || '';
        const storedUtmCampaign = localStorage.getItem('utm_campaign') || '';
        const storedUtmContent = localStorage.getItem('utm_content') || '';
        const storedUtmTerm = localStorage.getItem('utm_term') || '';
        const storedSum = localStorage.getItem('sum') || '';
        const storedDays = localStorage.getItem('days') || '';

        const urlSearchParams = new URLSearchParams(window.location.search);

        const aff_id = urlSearchParams.get('aff_id') || '';
        const ym_id = urlSearchParams.get('ym_id') || '';
        const affiliate_id = urlSearchParams.get('affiliate_id') || '';

        // логика отправки формы
        const data = {
            phone: phone,
            utm_source: storedUtmSource,
            utm_medium: storedUtmMedium,
            utm_campaign: storedUtmCampaign,
            utm_content: storedUtmContent,
            utm_term: storedUtmTerm,
            sum: storedSum,
            days: storedDays,
            last_name: fullName.split(' ')[0],
            first_name: fullName.split(' ')[1],
            father_name: fullName.split(' ')[2],
            custom_fields: {
                aff_id: aff_id,
                affiliate_id: affiliate_id,
            }
        };


        try {
            const res = await axios.post('/api/saveData', data);

            ym(96714912, 'reachGoal', 'send_form')
            ym(ym_id,'reachGoal','approve')

            _tmr.push({ type: 'reachGoal', id: 3493619, goal: 'send_form'});
            console.log(res.data)

            window.open(`/offer/${res.data.lead.id}` + window.location.search, "_blank");

            const currentSearchParams = urlSearchParams;
            if (currentSearchParams.has('affiliate_id')) {
                const affiliateIdValue = currentSearchParams.get('affiliate_id');
                currentSearchParams.delete('affiliate_id');
                currentSearchParams.set('source_id', affiliateIdValue);
            }

            window.location.href = customRedirectUrl + '?' + currentSearchParams.toString();
        } catch (e) {
            console.log(e);
        }


        console.log('send form', data)
    };

    return (
        <form onSubmit={handleSubmit}>
            <div className="flex flex-col xl:flex-row gap-8">
                {showFormFields && (
                    <div className="flex flex-col xl:flex-row gap-8 xl:h-[60px] w-full">
                        <Autocomplete ref={fullNameRef}
                                    onChangeListener={(val) => {
                                        setFullName(val)
                                    }}
                                    wrapperClass='w-full max-h-[60px] xl:w-1/2'/>
                        <div className='w-full xl:w-1/2 max-h-[60px]'>
                            <PhoneInput ref={phoneInputRef}
                                        onPhoneChange={(val) => {
                                            setPhone(val)
                                        }}
                            />
                        </div>
                    </div>
                )}
                <div className={`get-money-wrapper flex flex-col gap-4 justify-center ${!showFormFields ? 'w-full' : ''}`}>
                    <div className="flex flex-row justify-center">
                        <button onClick={() => {
                        }} type="submit"
                                className="money-btn bg-blue text-white text-center
                                xl:w-auto w-full min-w-[280px] h-[60px]
                                px-14 sm:py-3 py-3 rounded-2xl cursor-pointer text-lg">
                            Получить деньги
                        </button>
                    </div>
                    <div className="flex flex-row justify-center gap-2">
                        <img src="/assets/ctr/img/new-site/checkbox-blue.svg" className="size-[20px]" alt="checkbox"/>
                        <span className="text-sm text-center opacity-55">Быстро и надежно</span>
                    </div>
                </div>
            </div>

            {showFormFields && (
                <div className="checkbox-square form__checkbox">
                    <input
                        className="visibility-hidden"
                        id="privacy"
                        type="checkbox"
                        checked={privacy}
                        onChange={() => setPrivacy(!privacy)}
                        name="privacy"
                    />
                    <label htmlFor="privacy" className='inline-block'>
                        <span>Я даю согласие на сбор и обработку моих данных на условиях &nbsp;
                            <a className='inline underline' href="/docs/data.docx">обработки персональных данных</a>,
                            на <a className='inline underline' href="/docs/ads.docx">рассылку рекламных материалов</a></span>
                    </label>
                    {privacy === false && <span className="text-red text-xs">Заполните это поле</span>}
                </div>
            )}
        </form>
    );
});

export default MoneyForm;
