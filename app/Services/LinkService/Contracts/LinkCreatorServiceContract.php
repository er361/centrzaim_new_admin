<?php

namespace App\Services\LinkService\Contracts;

use App\Models\Sms;
use App\Models\User;
use App\Models\Webmaster;

interface LinkCreatorServiceContract
{
    /**
     * Получить ссылку с метками по исходной ссылке.
     * @param string $initialLink
     * @param null|Webmaster $webmaster Вебмастер, от которого пришел пользователь
     * @param string|null $sourceDomain Домен, на котором будет размещена ссылка
     * @return string
     */
    public function getPublicDashboardLink(string $initialLink, ?Webmaster $webmaster, ?string $sourceDomain): string;

    /**
     * Получить ссылку с метками по исходной ссылке.
     * @param string $initialLink
     * @param User $user Зарегистрированный пользователь
     * @param string|null $sourceDomain Домен, на котором будет размещена ссылка
     * @return string
     */
    public function getUserDashboardLink(string $initialLink, User $user, ?string $sourceDomain): string;

    /**
     * Получить ссылку с метками по исходной ссылке.
     * @param string $initialLink
     * @param User $user Пользователь, перешедший по ссылке
     * @param Sms $sms Отправленное сообщение
     * @param string|null $sourceDomain Домен, на котором будет размещена ссылка
     * @return string
     */
    public function getSmsLink(string $initialLink, User $user, Sms $sms, ?string $sourceDomain): string;

    public function getAdditionalSubParams(): array;
    
    /**
     * Получить ссылку для Telegram-бота с нужными параметрами
     * 
     * @param User $user Пользователь, для которого генерируется ссылка
     * @return string
     */
    public function getTelegramBotLink(User $user): string;
}