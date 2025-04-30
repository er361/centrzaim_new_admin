<span class="sm:text-[34px] text-2xl font-bold">Информация об оплате</span>
<div class="sm:max-w-[856px] bg-white p-4 rounded">

    <table class="w-full">
        <thead>
        <tr class="opacity-60">
            <th class="text-center">Дата</th>
            <th class="text-right">Сумма</th>
            <th class="text-right">Статус</th>
        </tr>
        </thead>
        <tbody>
        @foreach($user->payments as $payment)
            <tr>
                <td class="text-center">{{$formatDate($payment->created_at)}}</td>
                <td class="text-right">{{$payment->amount}}</td>
                <td class="text-right">{{$mapStatus($payment->status)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>