<?php

namespace App\Services\LinkService;

use App\Models\Sms;
use App\Models\User;
use App\Models\Webmaster;
use App\Services\LinkService\Contracts\LinkCreatorServiceContract;
use App\Services\LinkService\Contracts\LinkParsingServiceContract;
use App\Services\LinkService\Entities\ConversionEntity;

class NullLinkService implements LinkCreatorServiceContract, LinkParsingServiceContract
{
    /**
     * Получить ссылку с метками по исходной ссылке.
     * @param string $initialLink
     * @param null|Webmaster $webmaster Вебмастер, от которого пришел пользователь
     * @param string|null $sourceDomain
     * @return string
     */
    public function getPublicDashboardLink(string $initialLink, ?Webmaster $webmaster, ?string $sourceDomain): string
    {
        return $initialLink;
    }

    /**
     * Получить ссылку с метками по исходной ссылке.
     * @param string $initialLink
     * @param User $user Зарегистрированный пользователь
     * @param string|null $sourceDomain
     * @return string
     */
    public function getUserDashboardLink(string $initialLink, User $user, ?string $sourceDomain): string
    {
       return $initialLink;
    }

    /**
     * Получить ссылку с метками по исходной ссылке.
     * @param string $initialLink
     * @param User $user Пользователь, перешедший по ссылке
     * @param Sms $sms Отправленное сообщение
     * @param string|null $sourceDomain
     * @return string
     */
    public function getSmsLink(string $initialLink, User $user, Sms $sms, ?string $sourceDomain): string
    {
        return $initialLink;
    }

    /**
     * Получить сущность конверсии.
     *
     * @param array $request
     * @return ConversionEntity|null
     */
    public function getConversionEntity(array $request): ?ConversionEntity
    {
        return null;
    }

    public function getAdditionalSubParams(): array
    {
        // TODO: Implement getAdditionalSubParams() method.
    }

    public function getTelegramBotLink(User $user): string
    {
        // TODO: Implement getTelegramBotLink() method.
    }
}