<?php

namespace Tests\Feature\Front;

use App\Models\Source;
use App\Models\Webmaster;
use App\Services\SettingsService\SettingsService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class LandingPageControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Тестирует корректиное открытие страницы после перехода из ПП.
     * @dataProvider dataProvider
     * @param callable $dataProvider
     * @return void
     */
    public function testWithRedirectToMainPage(callable $dataProvider): void
    {
        $this->mock(SettingsService::class)
            ->expects('shouldRedirectToRegisterPageFromSources')
            ->andReturn(false);

        [$query, $sourceId, $apiId, $transactionId] = $dataProvider();
        $response = $this->get(route('front.lp.index', $query));

        $expectedParams = array_merge(Arr::except($query, 'source'), [
           'utm_source' => $query['source'],
           'utm_content' => $apiId,
        ]);

        $response->assertRedirect(route('front.index', $expectedParams));

        /** @var Webmaster $webmaster */
        $webmaster = Webmaster::query()
            ->where('source_id', $sourceId)
            ->where('api_id', $apiId)
            ->first();

        $response->assertCookie('webmaster_id', $webmaster->id);

        if ($transactionId !== null) {
            $response->assertCookie('transaction_id', $transactionId);
        }
    }

    /**
     * Тестирует корректиное открытие страницы после перехода из ПП.
     * @dataProvider dataProvider
     * @param callable $dataProvider
     * @return void
     */
    public function testWithRedirectToRegisterPage(callable $dataProvider): void
    {
        $this->mock(SettingsService::class)
            ->expects('shouldRedirectToRegisterPageFromSources')
            ->andReturn(true);

        [$query, $sourceId, $apiId, $transactionId] = $dataProvider();
        $response = $this->get(route('front.lp.index', $query));

        $expectedParams = array_merge(Arr::except($query, 'source'), [
            'utm_source' => $query['source'],
            'utm_content' => $apiId,
        ]);

        $response->assertRedirect(route('auth.register', $expectedParams));

        /** @var Webmaster $webmaster */
        $webmaster = Webmaster::query()
            ->where('source_id', $sourceId)
            ->where('api_id', $apiId)
            ->first();

        $response->assertCookie('webmaster_id', $webmaster->id);

        if ($transactionId !== null) {
            $response->assertCookie('transaction_id', $transactionId);
        }
    }

    public function dataProvider(): array
    {
        return [
            'leads' => [
                function () {
                    $query = [
                        'source' => 'leads',
                        'wmid' => random_int(1000, 9999),
                        'subid' => random_int(1000, 9999),
                    ];

                    $sourceId = Source::ID_LEADS;
                    $apiId = $query['wmid'];
                    $transactionId = $query['subid'];

                    return [
                        $query,
                        $sourceId,
                        $apiId,
                        $transactionId
                    ];
                }
            ],
            'guru-leads' => [
                function () {
                    $query = [
                        'source' => 'guru-leads',
                        'pid' => random_int(1000, 9999),
                        'click_id' => random_int(1000, 9999),
                    ];

                    $sourceId = Source::ID_GURU_LEADS;
                    $apiId = $query['pid'];
                    $transactionId = $query['click_id'];

                    return [
                        $query,
                        $sourceId,
                        $apiId,
                        $transactionId
                    ];
                }
            ],
            'direct' => [
                function () {
                    $query = [
                        'source' => 'direct',
                        'webmaster_id' => random_int(1000, 9999),
                    ];

                    $sourceId = Source::ID_DIRECT;
                    $apiId = $query['webmaster_id'];

                    return [
                        $query,
                        $sourceId,
                        $apiId,
                        null
                    ];
                }
            ],
            'leadgid' => [
                function () {
                    $query = [
                        'source' => 'leadgid',
                        'affiliate_id' => random_int(1000, 9999),
                        'transaction_id' => random_int(1000, 9999),
                    ];

                    $sourceId = Source::ID_LEAD_GID;
                    $apiId = $query['affiliate_id'];
                    $transactionId = $query['transaction_id'];

                    return [
                        $query,
                        $sourceId,
                        $apiId,
                        $transactionId,
                    ];
                }
            ],
            'leadcraft' => [
                function () {
                    $query = [
                        'source' => 'leadcraft',
                        'campaignID' => random_int(1000, 9999),
                        'clickID' => random_int(1000, 9999),
                    ];

                    $sourceId = Source::ID_LEAD_CRAFT;
                    $apiId = $query['campaignID'];
                    $transactionId = $query['clickID'];

                    return [
                        $query,
                        $sourceId,
                        $apiId,
                        $transactionId,
                    ];
                }
            ],
            'leadstech' => [
                function () {
                    $query = [
                        'source' => 'leadstech',
                        'web_id' => random_int(1000, 9999),
                        'click_id' => random_int(1000, 9999),
                    ];

                    $sourceId = Source::ID_LEADS_TECH;
                    $apiId = $query['web_id'];
                    $transactionId = $query['click_id'];

                    return [
                        $query,
                        $sourceId,
                        $apiId,
                        $transactionId,
                    ];
                }
            ],
            'affise' => [
                function () {
                    $query = [
                        'source' => 'affise',
                        'pid' => random_int(1000, 9999),
                        'click_id' => random_int(1000, 9999),
                    ];

                    $sourceId = Source::ID_AFFISE;
                    $apiId = $query['pid'];
                    $transactionId = $query['click_id'];

                    return [
                        $query,
                        $sourceId,
                        $apiId,
                        $transactionId,
                    ];
                }
            ],
            'Fin CPA Network' => [
                function () {
                    $query = [
                        'source' => 'fincpanetwork',
                        'webmaster_id' => random_int(1000, 9999),
                        'click_id' => random_int(1000, 9999),
                    ];

                    $sourceId = Source::ID_FIN_CPA_NETWORK;
                    $apiId = $query['webmaster_id'];
                    $transactionId = $query['click_id'];

                    return [
                        $query,
                        $sourceId,
                        $apiId,
                        $transactionId,
                    ];
                }
            ],
            'XPartners' => [
                function () {
                    $query = [
                        'source' => 'xpartners',
                        'webmaster_id' => random_int(1000, 9999),
                        'click_id' => random_int(1000, 9999),
                    ];

                    $sourceId = Source::ID_X_PARTNERS;
                    $apiId = $query['webmaster_id'];
                    $transactionId = $query['click_id'];

                    return [
                        $query,
                        $sourceId,
                        $apiId,
                        $transactionId,
                    ];
                }
            ],
        ];
    }
}