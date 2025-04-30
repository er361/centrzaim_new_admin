<?php

namespace App\Http\Controllers\Front\Miazaim;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseRequest;
use App\Http\Requests\FirstStepRequest;
use App\Http\Requests\PassportRequest;
use App\Interfaces\PaymentServiceApi;
use App\Models\Passport;
use App\Models\UserData;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class SiteController extends Controller
{
    //
    public function index()
    {
        return view('index');
    }

    public function fio(Request $request)
    {
        $amount = $request->get('amount');
        $days = $request->get('days');

        return view('fio', ['amount' => $amount, 'days' => $days]);
    }

    public function saveFio(FirstStepRequest $request)
    {
        $phoneNumbers = preg_replace('/\D/', '', $request->phone);
        $data = array_merge($request->all(),
            [
                'birth_date' => date('Y-m-d', strtotime($request->birth_date)),
                'phone' => $numbersOnly = $phoneNumbers
            ]);

        if (UserData::query()->where('phone', $phoneNumbers)->exists()) {
            return redirect()->route('passport');
        }

        $userData = UserData::create($data);
        return redirect()->route('passport');
    }

    public function savePassport(PassportRequest $request)
    {
        $phoneNumbers = preg_replace('/\D/', '', $request->phone);
        $data = array_merge($request->all(),
            [
                'given_date' => date('Y-m-d', strtotime($request->given_date)),
                'phone' => $phoneNumbers
            ]);

        if (Passport::query()->where('phone', $phoneNumbers)->exists()) {
            return redirect()->route('card');
        }

        $passport = Passport::create($data);
        return redirect()->route('card');
    }

    public function passport()
    {
        return view('passport');
    }

    public function card(PaymentServiceApi $paymentService)
    {
        $randomOrderId = fake()->numerify('########');
        $sessionId = null;
        try {
            $sessionId = $paymentService->initiateSession($randomOrderId, 1000);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return view('card.index', [
            'sessionId' => $sessionId,
            'orderId' => $randomOrderId
        ]);
    }

    public function handleCard(BaseRequest $request)
    {
        $userData = UserData::query()->where('phone', $request->phone)->first();
        if (!$userData) {
            return redirect()->route('index');
        }
//        $fakeHandle = rand(0, 1);
//        if(!$fakeHandle) {
//            return view('card-error');
//        }
        return view('vitrina', ['user' => $userData]);
    }

    public function vitrina()
    {
        // 8 dummy data items
        $dummyData = [
            [
                'rating' => 4.5,
                'percent' => 10,
                'max-days' => 30,
                'max-amount' => 10000,
                'license' => 'Лицензия № 123456',
            ],
            [
                'rating' => 4.7,
                'percent' => 15,
                'max-days' => 30,
                'max-amount' => 15000,
                'license' => 'Лицензия № 123457',
            ],
            [
                'rating' => 4.9,
                'percent' => 20,
                'max-days' => 30,
                'max-amount' => 20000,
                'license' => 'Лицензия № 123458',
            ],
            [
                'rating' => 5,
                'percent' => 25,
                'max-days' => 30,
                'max-amount' => 25000,
                'license' => 'Лицензия № 123459',
            ],
            [
                'rating' => 4.5,
                'percent' => 10,
                'max-days' => 30,
                'max-amount' => 10000,
                'license' => 'Лицензия № 123460',
            ],
            [
                'rating' => 4.7,
                'percent' => 15,
                'max-days' => 30,
                'max-amount' => 15000,
                'license' => 'Лицензия № 123461',
            ],
            [
                'rating' => 4.9,
                'percent' => 20,
                'max-days' => 30,
                'max-amount' => 20000,
                'license' => 'Лицензия № 123462',
            ],
            [
                'rating' => 5,
                'percent' => 25,
                'max-days' => 30,
                'max-amount' => 25000,
                'license' => 'Лицензия № 123463',
            ],

        ];
        return view('vitrina', ['user' => UserData::query()->first(), 'data' => $dummyData]);
    }
}
