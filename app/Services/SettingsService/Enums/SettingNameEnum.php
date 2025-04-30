<?php

namespace App\Services\SettingsService\Enums;


enum SettingNameEnum: string
{
//    case CuttLyApiKey = 'API_KEY_CUTT_LY';

//    case CuttLyDelay = 'CUTT_LY_DELAY';

    case LeadsUrlShortenerToken = 'LEADS_URL_SHORTENER_TOKEN';

    case LeadsToken = 'LEADS_TOKEN';

    case LeadsApiToken = 'LEADS_API_TOKEN';

    case LeadsGoalId = 'LEADS_GOAL_ID';

    case LeadsPlatformId = 'LEADS_PLATFORM_ID';

    case LinkMoneyKey = 'LINK_MONEY_KEY';

    case DadataToken = 'DADATA_TOKEN';

    case DadataSecret = 'DADATA_SECRET';

    case LeadCraftToken = 'LEAD_CRAFT_TOKEN';

    case LeadCraftActionId = 'LEAD_CRAFT_ACTION_ID';

    case LeadCraftConversionPrice = 'LEAD_CRAFT_CONVERSION_PRICE';

    case XPartnersCustomField = 'X_PARTNERS_CUSTOM_FIELD';

    case XPartnersToken = 'X_PARTNERS_TOKEN';

    case LeadGidOfferId = 'LEAD_GID_OFFER_ID';

    case LeadsTechToken = 'LEADS_TECH_TOKEN';

    case LeadsTechGoalId = 'LEADS_TECH_GOAL_ID';

    case AdsfinUser = 'ADSFIN_USER';

    case AdsFinPassword = 'ADSFIN_PASSWORD';

    case BankirosSiteId = 'BANKIROS_SITE_ID';

    case GuruLeadsSecure = 'GURU_LEADS_SECURE';

    case QZaimApiKey = 'Q_ZAIM_API_KEY';

    case QZaimUtmSource = 'Q_ZAIM_UTM_SOURCE';

    case AllianceToken = 'ALLIANCE_TOKEN';

    case AllianceFrom = 'ALLIANCE_FROM';

    case LeadBitAdvertiserId = 'LEAD_BIT_ADVERTISER_ID';

    case LeadBitOfferId = 'LEAD_BIT_OFFER_ID';

    case LeadBitUrlPart = 'LEAD_BEAT_URL_PART';

    case Click2MoneyPartner = 'CLICK2MONEY_PARTNER';

    case DigitalContactApiKey = 'DIGITAL_CONTACT_API_KEY';

    /**
     * Получить название настройки для администратора.
     * @return string
     */
    public function getLabel(): string
    {
        return match($this) {
            self::LeadsUrlShortenerToken => 'Leads - Токен для сокращения ссылок',
//            self::CuttLyApiKey => 'CUTT.LY - API ключ',
//            self::CuttLyDelay => 'CUTT.LY - Задержка между сокращением ссылок в секундах (зависит от тарифа)',
            self::LeadsToken => 'Leads - Токен для отправки постбэков',
            self::LeadsApiToken => 'Leads - API токен для отправки анкет',
            self::LeadsGoalId => 'Leads - Goal ID',
            self::LeadsPlatformId => 'Leads - Platform ID',
            self::LinkMoneyKey => 'LinkMoney - Ключ',
            self::DadataToken => 'Dadata - Токен для определения геолокации пользователя',
            self::DadataSecret => 'Dadata - Секретный ключ для определения геолокации пользователя',
            self::LeadCraftToken => 'LeadCraft - Токен для отправки постбэков',
            self::LeadCraftActionId => 'LeadCraft - ID действия  для отправки постбэков',
            self::LeadCraftConversionPrice => 'LeadCraft - Стоимость конверсии ',
            self::XPartnersCustomField => 'X-Partners - Код сервиса для отправки постбэков',
            self::XPartnersToken => 'X-Partners - Токен для отправки постбэков',
            self::LeadGidOfferId => 'LeadGid - ID оффера для отправки постбэков',
            self::LeadsTechToken => 'LeadsTech - Токен для отправки анкет пользователей',
            self::LeadsTechGoalId => 'LeadsTech - Goal ID',
            self::AdsfinUser => 'AdsFin - Логин пользователя для статистики по баннерам',
            self::AdsFinPassword => 'AdsFin - Пароль пользователя  для статистики по баннерам',
            self::BankirosSiteId => 'Bankiros - ID сайта для отправки постбэков',
            self::GuruLeadsSecure => 'GuruLeads - Secure токен для отправки постбэков',
            self::QZaimApiKey => 'QZaim - API ключ',
            self::QZaimUtmSource => 'QZaim - UTM Source',
            self::AllianceToken => 'Alliance - Токен',
            self::AllianceFrom => 'Alliance - From',
            self::LeadBitAdvertiserId => 'LeadBit - Advertiser ID',
            self::LeadBitOfferId => 'LeadBit - Offer ID',
            self::LeadBitUrlPart => 'LeadBit - Часть URL после post.leadbit.biz',
            self::Click2MoneyPartner => 'Click2Money Partner',
            self::DigitalContactApiKey => 'DigitalContact - API Key',
        };
    }
}