@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Витрины займов</h3>

    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Фильтр по витринам
                </div>

                <div class="panel-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="showcase_id">Витрина</label>
                                {!! Form::select('showcase_id', $showcases, old('showcase_id', request('showcase_id')), ['class' => 'form-control']) !!}
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="source_id">Источник</label>
                                {!! Form::select('source_id', $sources, old('source_id', request('source_id')), ['class' => 'form-control']) !!}
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="webmaster_id">Вебмастер</label>
                                {!! Form::select('webmaster_id', $webmasters, old('webmaster_id', request('webmaster_id')), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <input type="submit" value="Показать" class="btn btn-primary"/>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


    @if($loanOffers !== null)
        <p>
            <a class="btn btn-warning"
               href="{{ route('preview', [
                        'showcase_id' => request('showcase_id'),
                        'source_id' => request('source_id'),
                        'webmaster_id' => request('webmaster_id')
                        ]),
                    }}"
               target="_blank">
                <i class="fa fa-external-link" aria-hidden="true"></i> Предпросмотр
            </a>
        </p>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Настройки витрины
                    </div>

                    <div class="panel-body">
                        <form method="POST" action="{{ route('admin.source-showcases.store') }}">
                            @csrf
                            <input type="hidden" name="showcase_id" value="{{ request('showcase_id') }}">
                            <input type="hidden" name="source_id" value="{{ request('source_id') }}">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="loan_offer_id">Всплывающий оффер</label>
                                    </div>
                                    <div class="col-md-8">
                                        {!! Form::select('loan_offer_id', $featuredLoanOffers, old('loan_offer_id', $sourceShowcase?->loan_offer_id), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Обновить" class="btn btn-danger"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading flex flex-row gap-8">
                        <span>
                            Офферы на витрине
                        </span>
                        <div id="webmaster-template">
                            @if($isTemplate)
                                <span class="rounded px-4 py-1 border bg-green-500 text-white">Шаблон</span>
                            @else
                                <form method="POST" action="{{ route('admin.webmaster-templates.store') }}">
                                    @csrf
                                    <input type="hidden" name="source_id" value="{{ request('source_id') }}">
                                    <input type="hidden" name="showcase_id" value="{{ request('showcase_id') }}">
                                    <input type="hidden" name="webmaster_id" value="{{ request('webmaster_id') }}">

                                    <button class="rounded px-2 border border-green-500 text-green-500">
                                        Сделать шаблоном
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="row loan-offers">
                            @php
                                /** @var $loanOffer \App\Models\LoanOffer  */
                            @endphp
                            @foreach($loanOffers as $loanOffer)

                                <div class="col-md-4 loan-offer" id="loan_offers_{{ $loanOffer->id }}">
                                    <div class="well">
                                        <div class="flex flex-row justify-between">
                                            <div>
                                                <img src="{{ $loanOffer->loan->image_path }}" alt="{{ $loanOffer->loan->name }}"
                                                     class="max-h-[16px]"/>
                                                <p>{{ $loanOffer->loan->name }}</p>
                                            </div>

                                            <span class="rounded px-2 border border-blue text-blue">Api ID - {{$loanOffer->loan->api_id}}</span>
                                            @if(in_array($loanOffer->loan->api_id, \App\Services\OffersChecker\Settings::$OFFER_IDS))
                                                <span class="rounded px-2 border border-green-500 text-green-500">основной</span>
                                            @else
                                                <div>
                                                    <label for="backup_{{ $loanOffer->id }}">Запасной?</label>
                                                    <input id="backup_{{ $loanOffer->id }}" type="checkbox"
                                                           {{ $loanOffer->is_backup ? 'checked' : '' }}
                                                           onchange="updateBackup({{ $loanOffer->id }}, this.checked)">
                                                </div>
                                            @endif
                                            <span class="rounded px-2 border border-red text-red"
                                                  onclick="deleteLoanOffer({{ $loanOffer->id }})"
                                            >Удалить</span>

                                        </div>

                                        <p class="help-block">
                                            {{ \Illuminate\Support\Str::limit($loanOffer->loan->description, 50) }}
                                        </p>

                                        <a href="{{ $loanOffer->loanLink->link }}" target="_blank"
                                           class="btn btn-primary btn-xs">Перейти</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="help-block">
                            Порядок офферов можно изменять перетаскиванием.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('scripts')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>

        window.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);

            const source_id = urlParams.get('source_id');
            const showcase_id = urlParams.get('showcase_id');
            console.log({source_id, showcase_id});

            if (!source_id || !showcase_id) {
                const template = document.getElementById('webmaster-template');
                template.style.display = 'none';
            }
        })

        function deleteLoanOffer(id) {
            console.log('delete', id);
            $.ajax({
                url: `{{ route('admin.loan-offers.destroy',['loan_offer' => ':id']) }}`.replace(':id', id),
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                },
            })
                .done(function () {
                    toastr["success"]("Оффер удален из витрины.");
                    $('#loan_offers_' + id).remove();
                })
                .fail(function () {
                    toastr["error"]("Ошибка при удалении оффера");
                });
        }

        function updateBackup(id, isBackup) {
            console.log('update', id);
            $.ajax({
                url: `{{ route('admin.loan-offers.update',['loan_offer' => ':id']) }}`.replace(':id', id),
                type: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                    is_backup: isBackup ? 1 : 0,
                },
            })
                .done(function () {
                    toastr["success"]("Статус оффера обновлен.");
                })
                .fail(function () {
                    toastr["error"]("Ошибка при обновлении статуса оффера");
                });
        }
    </script>
    @if($loanOffers !== null)
        <script type="text/javascript">


            $(function () {
                $('.loan-offers').sortable({
                    update: function (event, ui) {
                        const url = '{{ route('admin.loan-offers.storeOrder') }}';
                        let data = $(this).sortable('serialize');
                        data += '&_token=' + $('meta[name="csrf-token"]').attr('content');
                        data += '&showcase_id={{ request('showcase_id') }}';
                        data += '&source_id={{ request('source_id') }}';

                        $('.loan-offers').addClass('loan-offers-disable');
                        $.ajax({
                            data: data,
                            type: 'POST',
                            url: url,
                        })
                            .done(function () {
                                toastr["success"]("Порядок офферов сохранен.");
                                $('.loan-offers').removeClass('loan-offers-disable');
                            })
                            .fail(function () {
                                toastr["error"]("Ошибка при сохранении порядка офферов");
                            });
                    }
                });
            });
        </script>
    @endif
@stop
