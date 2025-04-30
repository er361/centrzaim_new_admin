@extends('admin.layouts.app')

@section('content')
    @can('report_access')
        <div class="row">
            @foreach($labels as $label => $createdAtFrom)
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">{{ $label }}</div>

                        <div class="panel-body">
                            @foreach($payments[$label] as $planId => $paymentInformation)
                                <p><strong>Платежи по {{ $planId === 0 ? 'старой схеме' : 'новой схеме' }}</strong></p>
                                <ul>
                                    <li>Платежей для привязки: {{ $paymentInformation['default_payments'] }}</li>
                                    <li>Успешных привязок: {{ $paymentInformation['default_success_payments'] }}</li>
                                    <li>
                                        Рекуррентные платежи: {{ $paymentInformation['recurrent_payments'] }}
                                    </li>
                                    <li>
                                        Успешных рекуррентных
                                        платежей: {{ $paymentInformation['recurrent_success_payments'] }}
                                        на {{ $paymentInformation['recurrent_success_payments_sum'] }} ₽
                                        <ul>
                                            @foreach($paymentInformation['recurrent_payments_distribution'] as $iteration => $iterationData)
                                                @foreach($iterationData as $paymentNumber => $paymentData)
                                                    <li>#{{ $iteration + 1 }}.{{ $paymentNumber + 1 }}
                                                        : {{ $paymentData['total'] }} на {{ $paymentData['amount'] }} ₽
                                                    </li>
                                                @endforeach
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>
                            @endforeach
                            <p><strong>SMS</strong></p>
                            <ul>
                                <li>Отправлено SMS: {{ $sms[$label] }}</li>
                            </ul>
                            <p><strong>Постбэки</strong></p>
                            <ul>
                                <li>Создано: {{ $postbacks[$label]['created'] }}</li>
                                <li>Отправлено: {{ $postbacks[$label]['sent'] }}</li>
                                <li>Не отправлено: {{ $postbacks[$label]['unsuccessful'] }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">Статистика по сайту</div>
                        <div class="panel-body">
                            <x-site-statistics/>
                        </div>
                    </div>
                </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Рекуррентные платежи</div>

                    <div class="panel-body">
                        <canvas id="recurrentPaymentsChart" width="400" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('quickadmin.qa_dashboard')</div>

                    <div class="panel-body">
                        @lang('quickadmin.qa_dashboard_text')
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('scripts')
    @can('report_access')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"
                integrity="sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxU9JEHakS7UJZNeml5ALk+8IKlU6jDgMabC3vkumRokgJA=="
                crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                const dates = @json($paymentsPerDayDates);
                const data = {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Создано',
                            data:  @json($paymentsPerDay[0] ?? []),
                            backgroundColor: '#ffc107',
                        },
                        {
                            label: 'Оплачено',
                            data:  @json($paymentsPerDay[10] ?? []),
                            backgroundColor: '#28a745',
                        },
                        {
                            label: 'Ошибки',
                            data: @json($paymentsPerDay[11] ?? []),
                            backgroundColor: '#dc3545',
                        },
                    ]
                };

                const config = {
                    type: 'bar',
                    data: data,
                    options: {
                        plugins: {
                            title: {
                                display: true,
                                text: 'Рекурретные платежи'
                            },
                        },
                        responsive: true,
                        scales: {
                            x: {
                                stacked: true,
                            },
                            y: {
                                stacked: true
                            }
                        }
                    }
                };

                const ctx = document.getElementById('recurrentPaymentsChart');
                const myChart = new Chart(ctx, config);
            });
        </script>
    @endcan
@endsection