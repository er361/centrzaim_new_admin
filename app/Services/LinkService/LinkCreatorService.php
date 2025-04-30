<?php


namespace App\Services\LinkService;


use App\Models\Conversion;
use App\Models\Sms;
use App\Models\User;
use App\Models\Webmaster;
use App\Services\LinkService\Contracts\LinkCreatorServiceContract;
use Illuminate\Support\Str;

/**
 * Универсальный "создатель" ссылок для конверсий v2.
 *
 * Для парсинга всей информации используем только sub1+sub2.
 * Sub1 - версия + все идентификаторы моделей.
 * Sub2 - тип конверсии.
 * Sub4 - домен, где размещена ссылка (для аналитики в ПП).
 */
class LinkCreatorService implements LinkCreatorServiceContract
{
    /** @var string Префикс в sub1, указывающий на вторую версию парсинга. */
    public const V2_SUB1_PREFIX = 'v2__';

    /** @var string Префикс вебмастера */
    public const PREFIX_WEBMASTER = 'w_';

    /** @var string Префикс пользователя */
    public const PREFIX_USER = 'u_';

    /** @var string Префикс SMS */
    public const PREFIX_SMS = 's_';

    /** @var string */
    public const PREFIX_SITE_ID = 'si_';

    /**
     * @var array Конфигурация для создания (services.sources.*.conversion).
     */
    protected array $configuration;

    /**
     * @var int Идентификатор сайта
     */
    protected int $siteId;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
        $this->siteId = (int)config('postbacks.site_id');
    }

    /**
     * Получить ссылку с метками по исходной ссылке.
     * @param string $initialLink
     * @param null|Webmaster $webmaster Вебмастер, от которого пришел пользователь
     * @param string|null $sourceDomain
     * @return string
     */
    public function getPublicDashboardLink(string $initialLink, ?Webmaster $webmaster, ?string $sourceDomain): string
    {
        $sub1 = $this->generateSub1(
            webmaster: $webmaster,
        );

        return $this->generateLink($initialLink, $sub1, Conversion::TYPE_PUBLIC_DASHBOARD, $sourceDomain);
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
        $sub1 = $this->generateSub1(
            webmaster: $user->webmaster ?? null,
            user: $user
        );

        return $this->generateLink($initialLink, $sub1, Conversion::TYPE_DASHBOARD, $sourceDomain);
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
        $type = Conversion::TYPE_SMS;

        $sub1 = $this->generateSub1(
            webmaster: $user->webmaster ?? null,
            user: $user,
            sms: $sms
        );

        return $this->generateLink($initialLink, $sub1, $type, $sourceDomain);
    }


    protected function generateLink(string $initialLink, string $sub1, string $sub2, ?string $sub4): string
    {
        $query = [];

        $subNames = $this->configuration['subs'];

        $query[$subNames[1]] = $sub1;
        $query[$subNames[2]] = $sub2;

        if ($sub4 !== null) {
            $query[$subNames[4]] = $sub4;
        }

        // Добавляем дополнительные параметры из куки
        $query = array_merge($query, $this->getAdditionalSubParams());

        $queryString = http_build_query($query);

        if (Str::contains($initialLink, '?')) {
            return $initialLink . '&' . $queryString;
        }

        return $initialLink . '?' . $queryString;
    }


    /**
     * Сгенерировать строку для sub1.
     * @param Webmaster|null $webmaster
     * @param User|null $user
     * @param Sms|null $sms
     * @return string
     * @example v2__w_1__u_2__du_3__s_4
     */
    public function generateSub1(?Webmaster $webmaster = null, ?User $user = null, ?Sms $sms = null): string
    {
        $str = self::V2_SUB1_PREFIX;
        $parts = [
            self::PREFIX_SITE_ID . $this->siteId,
        ];

        if ($webmaster !== null) {
            $parts[] = self::PREFIX_WEBMASTER . $webmaster->id;
        }

        if ($user !== null) {
            $parts[] = self::PREFIX_USER . $user->id;
        }

        if ($sms !== null) {
            $parts[] = self::PREFIX_SMS . $sms->id;
        }

        $str .= implode('__', $parts);

        return $str;
    }

    public function getAdditionalSubParams(): array
    {
        $additionalSubs = [];

        // Список всех возможных sub-параметров, которые могут быть в куках
        $availableSubs = [
            'sub5',
//            'sub6',
//            'sub7'
        ];

        foreach ($availableSubs as $subName) {
            $cookieValue = request()->cookie("aff_$subName") ?? $_COOKIE["aff_$subName"] ?? null;

            if (!empty($cookieValue)) {
                $additionalSubs["aff_$subName"] = $cookieValue;
            }
        }

        return $additionalSubs;
    }
    
    /**
     * Получить ссылку для Telegram-бота с нужными параметрами
     * 
     * @param User $user Пользователь, для которого генерируется ссылка
     * @return string
     */
    public function getTelegramBotLink(User $user): string
    {
        $sub1 = $this->generateSub1(
            webmaster: $user->webmaster ?? null,
            user: $user
        );
        
        return "https://t.me/zaimkonsultant24na7_bot?start=" . $sub1;
    }

}