<?php

namespace App\Services\LoanService;

use App\Models\Showcase;
use App\Models\Sms;
use App\Models\Source;
use App\Models\User;
use App\Models\Webmaster;
use Illuminate\Support\Facades\App;

class LoanServiceBuilder
{
    /**
     * @var null|Webmaster
     */
    protected ?Webmaster $webmaster = null;

    /**
     * @var null|Showcase
     */
    protected ?Showcase $showcase = null;

    /**
     * @var null|User
     */
    protected null|User $user = null;

    /**
     * @var null|string
     */
    protected ?string $sourceDomain = null;

    /**
     * @var Sms|null
     */
    protected ?Sms $sms = null;

    /**
     * @var Source|null
     */
    protected ?Source $source = null;

    /**
     * @param Webmaster|null $webmaster
     * @return LoanServiceBuilder
     */
    public function setWebmaster(?Webmaster $webmaster): LoanServiceBuilder
    {
        $this->webmaster = $webmaster;

        if ($this->source === null) {
            $this->setSource($this->webmaster?->source);
        }

        return $this;
    }

    /**
     * @param Showcase|null $showcase
     * @return LoanServiceBuilder
     */
    public function setShowcase(?Showcase $showcase): LoanServiceBuilder
    {
        $this->showcase = $showcase;
        return $this;
    }

    /**
     * @param null|User $user
     * @return LoanServiceBuilder
     */
    public function setUser(null|User $user): LoanServiceBuilder
    {
        $this->user = $user;

        if ($this->webmaster === null) {
            $this->setWebmaster($this->user?->webmaster);
        }

        return $this;
    }

    /**
     * @param string|null $sourceDomain
     * @return LoanServiceBuilder
     */
    public function setSourceDomain(?string $sourceDomain): LoanServiceBuilder
    {
        $this->sourceDomain = $sourceDomain;
        return $this;
    }

    /**
     * @param Sms|null $sms
     * @return LoanServiceBuilder
     */
    public function setSms(?Sms $sms): LoanServiceBuilder
    {
        $this->sms = $sms;
        return $this;
    }

    /**
     * @param Source|null $source
     * @return LoanServiceBuilder
     */
    public function setSource(?Source $source): LoanServiceBuilder
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return LoanService
     */
    public function getLoanService(): LoanService
    {
        return App::make(LoanService::class, [
            'webmaster' => $this->webmaster,
            'showcase' => $this->showcase,
            'user' => $this->user,
            'sourceDomain' => $this->sourceDomain,
            'sms' => $this->sms,
            'source' => $this->source,
        ]);
    }
}