<?php

namespace Tests\Feature\Api\V1;

use App\Models\Conversion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Тестирование новых постбэков.
 * @todo Переписать на dataProvider.
 */
class PostbackControllerTestV2 extends TestCase
{
    use WithFaker;

    /**
     * Проверка сохранения постбэка Leads.
     * @return void
     */
    public function testLeadsPostback(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $params = [
            'source_id' => 1, // Leads
            'postback_secret' => config('app.postback_secret'),
            'aff_sub1' => "v2__u_{$user->id}__si_".config('postbacks.site_id'),
            'aff_sub2' => 'dashboard',
            'conversion_id' => Str::random(),
            'transaction_id' => Str::random(),
            'adv_sub' => Str::random(),
            'created' => Carbon::now()->toDateTimeString(),
            'status' => 'approved', // Конверсия принята
            'payout' => $this->faker->randomNumber(3, true),
            'payout_type' => Str::random(),
            'user_agent' => $this->faker->userAgent,
            'offer_id' => Str::random(),
            'affiliate_id' => Str::random(),
            'source' => Str::random(),
            'ip' => $this->faker->ipv4,
            'is_test' => 0,
        ];
        $url = route('api.postback.store', $params);

        $response = $this->get($url);
        $response->assertStatus(Response::HTTP_OK);

        /** @var null|Conversion $conversion */
        $conversion = Conversion::query()
            ->where('api_transaction_id', $params['transaction_id'])
            ->first();

        self::assertNotNull($conversion);
        self::assertEquals($user->id, $conversion->user_id);
        self::assertEquals(1, $conversion->source_id);
        self::assertEquals($params['conversion_id'], $conversion->api_conversion_id);
        self::assertEquals($params['transaction_id'], $conversion->api_transaction_id);
        self::assertEquals($params['payout'], $conversion->api_payout);
        self::assertEquals(Conversion::STATUS_APPROVED, $conversion->api_status);
    }

    /**
     * Проверка сохранения постбэка GuruLeads.
     * @return void
     */
    public function testGuruLeadsPostback(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $params = [
            'source_id' => 2, // GuruLeads
            'postback_secret' => config('app.postback_secret'),
            'sub1' => "v2__u_{$user->id}__si_".config('postbacks.site_id'),
            'sub2' => 'dashboard',
            'uagent' => $this->faker->userAgent,
            'ip' => $this->faker->ipv4,
            'source' => Str::random(),
            'transactionid' => Str::random(),
            'offerid' => Str::random(),
            'status' => 1, // Конверсия принята
            'sum' => $this->faker->randomNumber(3, true),
            'currency' => 'RUB',
            'date' => Carbon::now()->toDateTimeString(),
        ];
        $url = route('api.postback.store', $params);

        $response = $this->get($url);
        $response->assertStatus(Response::HTTP_OK);

        /** @var null|Conversion $conversion */
        $conversion = Conversion::query()
            ->where('api_transaction_id', $params['transactionid'])
            ->first();

        self::assertNotNull($conversion);
        self::assertEquals(2, $conversion->source_id);
        self::assertEquals($user->id, $conversion->user_id);
        self::assertEquals($params['transactionid'], $conversion->api_conversion_id);
        self::assertEquals($params['transactionid'], $conversion->api_transaction_id);
        self::assertEquals($params['sum'], $conversion->api_payout);
        self::assertEquals(Conversion::STATUS_APPROVED, $conversion->api_status);
    }

    /**
     * Проверка сохранения постбэка LeadGid.
     * @return void
     */
    public function testLeadGidPostback(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $params = [
            'source_id' => 4, // LeadGid
            'postback_secret' => config('app.postback_secret'),
            'lead_id' => Str::random(),
            'transaction_id' => Str::random(),
            'status' => 'approved', // Конверсия принята
            'lead_date' => Carbon::now()->toDateString(),
            'offer_id' => Str::random(),
            'payout' => $this->faker->randomNumber(3, true),
            'currency' => 'RUB',
            'aff_sub1' => "v2__u_{$user->id}__si_".config('postbacks.site_id'),
            'aff_sub2' => 'dashboard',
        ];
        $url = route('api.postback.store', $params);

        $response = $this->get($url);
        $response->assertStatus(Response::HTTP_OK);

        /** @var null|Conversion $conversion */
        $conversion = Conversion::query()
            ->where('api_transaction_id', $params['transaction_id'])
            ->first();

        self::assertNotNull($conversion);
        self::assertEquals(4, $conversion->source_id);
        self::assertEquals($user->id, $conversion->user_id);
        self::assertEquals($params['lead_id'], $conversion->api_conversion_id);
        self::assertEquals($params['transaction_id'], $conversion->api_transaction_id);
        self::assertEquals($params['payout'], $conversion->api_payout);
        self::assertEquals(Conversion::STATUS_APPROVED, $conversion->api_status);
    }

    /**
     * Проверка сохранения постбэка LeadCraft.
     * @return void
     */
    public function testLeadCraftPostback(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $params = [
            'source_id' => 5, // LeadCraft
            'postback_secret' => config('app.postback_secret'),
            'clickID' => Str::random(),
            'campaignID' => Str::random(),
            'offerID' => $this->faker->randomNumber(3, true),
            'offerTitle' => Str::random(),
            'status' => 'approved', // Конверсия принята
            'price' => $this->faker->randomNumber(3, true),
            'sub' => "v2__u_{$user->id}__si_".config('postbacks.site_id'),
            'sub2' => 'dashboard',
            'advertiserID' => Str::random(),
        ];
        $url = route('api.postback.store', $params);

        $response = $this->get($url);
        $response->assertStatus(Response::HTTP_OK);

        /** @var null|Conversion $conversion */
        $conversion = Conversion::query()
            ->where('api_transaction_id', $params['clickID'])
            ->first();

        self::assertNotNull($conversion);
        self::assertEquals(5, $conversion->source_id);
        self::assertEquals($user->id, $conversion->user_id, 'user_id');
        self::assertEquals($params['clickID'], $conversion->api_conversion_id, 'api_conversion_id');
        self::assertEquals($params['clickID'], $conversion->api_transaction_id, 'api_transaction_id');
        self::assertEquals($params['price'], $conversion->api_payout, 'api_payout');
        self::assertEquals(Conversion::STATUS_APPROVED, $conversion->api_status, 'api_status');
    }

    /**
     * Проверка сохранения постбэка Click2Money.
     * @return void
     */
    public function testClick2MoneyPostback(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $params = [
            'source_id' => 7, // Click2Money
            'postback_secret' => config('app.postback_secret'),
            'stream_id' => Str::random(),
            'subid1' => "v2__u_{$user->id}__si_".config('postbacks.site_id'),
            'subid2' => 'dashboard',
            'cid' => Str::random(),
            'date' => Carbon::now()->toDateTimeString(),
            'action_type' => 'approved', // Конверсия принята
            'payout' => $this->faker->randomNumber(3, true),
            'lead_id' => Str::random(),
            'offer_id' => Str::random(),
            'offer_name' => Str::random(),
            'currency' => 'RUB',
        ];
        $url = route('api.postback.store', $params);

        $response = $this->get($url);
        $response->assertStatus(Response::HTTP_OK);

        /** @var null|Conversion $conversion */
        $conversion = Conversion::query()
            ->where('api_transaction_id', $params['cid'])
            ->first();

        self::assertNotNull($conversion);
        self::assertEquals(7, $conversion->source_id);
        self::assertEquals($user->id, $conversion->user_id);
        self::assertEquals($params['cid'], $conversion->api_conversion_id);
        self::assertEquals($params['cid'], $conversion->api_transaction_id);
        self::assertEquals($params['payout'], $conversion->api_payout);
        self::assertEquals(Conversion::STATUS_APPROVED, $conversion->api_status);
    }

    /**
     * Проверка сохранения постбэка LeadsTech.
     * @return void
     */
    public function testLeadsTechPostback(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $params = [
            'source_id' => 8, // LeadsTech
            'postback_secret' => config('app.postback_secret'),
            'goalId' => Str::random(),
            'sub1' => "v2__u_{$user->id}__si_".config('postbacks.site_id'),
            'sub2' => 'dashboard',
            'sub3' => '',
            'clickId' => Str::random(),
            'webmasterId' => Str::random(),
            'dateClick' => Carbon::now()->toDateTimeString(),
            'date' => Carbon::now()->toDateTimeString(),
            'offerConversionId' => Str::random(),
            'status' => '1', // Конверсия принята
            'sum' => $this->faker->randomNumber(3, true),
        ];
        $url = route('api.postback.store', $params);

        $response = $this->get($url);
        $response->assertStatus(Response::HTTP_OK);

        /** @var null|Conversion $conversion */
        $conversion = Conversion::query()
            ->where('api_transaction_id', $params['clickId'])
            ->first();

        self::assertNotNull($conversion);
        self::assertEquals(8, $conversion->source_id);
        self::assertEquals($user->id, $conversion->user_id);
        self::assertEquals($params['offerConversionId'], $conversion->api_conversion_id);
        self::assertEquals($params['clickId'], $conversion->api_transaction_id);
        self::assertEquals($params['sum'], $conversion->api_payout);
        self::assertEquals(Conversion::STATUS_APPROVED, $conversion->api_status);
    }

    /**
     * Проверка сохранения постбэка Affise.
     * @return void
     */
    public function testAffisePostback(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $params = [
            'source_id' => 9, // Affise
            'postback_secret' => config('app.postback_secret'),
            'sub1' => "v2__u_{$user->id}__si_".config('postbacks.site_id'),
            'sub2' => 'dashboard',
            'sub3' => '',
            'transactionid' => Str::random(),
            'date' => Carbon::now()->toDateTimeString(),
            'status' => '1', // Конверсия принята
            'sum' => $this->faker->randomNumber(3, true),
            'uagent' => $this->faker->userAgent,
            'offerid' => Str::random(),
            'ip' => $this->faker->ipv4,
            'currency' => 'RUB',
        ];
        $url = route('api.postback.store', $params);

        $response = $this->get($url);
        $response->assertStatus(Response::HTTP_OK);

        /** @var null|Conversion $conversion */
        $conversion = Conversion::query()
            ->where('api_transaction_id', $params['transactionid'])
            ->first();

        self::assertNotNull($conversion);
        self::assertEquals(9, $conversion->source_id);
        self::assertEquals($user->id, $conversion->user_id);
        self::assertEquals($params['transactionid'], $conversion->api_conversion_id);
        self::assertEquals($params['transactionid'], $conversion->api_transaction_id);
        self::assertEquals($params['sum'], $conversion->api_payout);
        self::assertEquals(Conversion::STATUS_APPROVED, $conversion->api_status);
    }

    /**
     * Проверка сохранения постбэка XPartners.
     * @return void
     */
    public function testXPartnersPostback(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $params = [
            'source_id' => 11, // XPartners
            'postback_secret' => config('app.postback_secret'),
            'sub1' => "v2__u_{$user->id}__si_".config('postbacks.site_id'),
            'sub2' => 'dashboard',
            'sub3' => '',
            'transactionid' => Str::random(),
            'date' => Carbon::now()->toDateTimeString(),
            'status' => 'confirmed', // Конверсия принята
            'sum' => $this->faker->randomNumber(3, true),
            'uagent' => $this->faker->userAgent,
            'offerid' => Str::random(),
            'ip' => $this->faker->ipv4,
        ];
        $url = route('api.postback.store', $params);

        $response = $this->get($url);
        $response->assertStatus(Response::HTTP_OK);

        /** @var null|Conversion $conversion */
        $conversion = Conversion::query()
            ->where('api_transaction_id', $params['transactionid'])
            ->first();

        self::assertNotNull($conversion);
        self::assertEquals(11, $conversion->source_id);
        self::assertEquals($user->id, $conversion->user_id);
        self::assertEquals($params['transactionid'], $conversion->api_conversion_id);
        self::assertEquals($params['transactionid'], $conversion->api_transaction_id);
        self::assertEquals($params['sum'], $conversion->api_payout);
        self::assertEquals(Conversion::STATUS_APPROVED, $conversion->api_status);
    }

    /**
     * Проверка, что не сохраняем конверсию, если принадлежит другому сайту.
     * @return void
     */
    public function testDoNotSaveWithWrongSiteId(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $siteId = (int)config('postbacks.site_id') + 1;

        $params = [
            'source_id' => 11, // XPartners
            'postback_secret' => config('app.postback_secret'),
            'sub1' => "v2__u_{$user->id}__si_".$siteId,
            'sub2' => 'dashboard',
            'sub3' => '',
            'transactionid' => Str::random(),
            'date' => Carbon::now()->toDateTimeString(),
            'status' => 'confirmed', // Конверсия принята
            'sum' => $this->faker->randomNumber(3, true),
            'uagent' => $this->faker->userAgent,
            'offerid' => Str::random(),
            'ip' => $this->faker->ipv4,
        ];
        $url = route('api.postback.store', $params);

        $response = $this->get($url);
        $response->assertStatus(Response::HTTP_OK);

        /** @var null|Conversion $conversion */
        $conversion = Conversion::query()
            ->where('api_transaction_id', $params['transactionid'])
            ->first();

        self::assertNull($conversion);
    }
}