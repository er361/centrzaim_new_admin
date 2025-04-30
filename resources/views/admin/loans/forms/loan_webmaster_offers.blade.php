<table class="table table-bordered table-striped">
    <thead class="thead-light">
    <tr>
        <th>Вебмастер Api Id | Источник</th>
        @foreach($showcases as $showcase)
            <th>{{ $showcase->name }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($sources as $source)
        @foreach($webmasters->where('source_id', $source->id) as $webmaster)
            <!-- Фильтрация вебмастеров -->
            <tr>
                <th scope="row">
                    {{ $webmaster->api_id }} | ({{ $source->name }}) <!-- Показываем API ID -->

                    {!! Form::hidden("loan_offers[{$webmaster->id}][webmaster_id]", $webmaster->id) !!}
                </th>

                @foreach($showcases as $showcase)

                    <td>
                        @php
                            /** @var $loan \App\Models\Loan */
                                $loanOffer = $loan->loanOffers
                                    ->where('webmaster_id', $webmaster->id)
                                    ->where('showcase_id', $showcase->id)
                                    ->where('source_id', $source->id)
                                    ->first();

                                $loan_link_id = $loanOffer?->loan_link_id;
                                $fieldKey = "loan_offers[{$webmaster->id}][showcases][{$showcase->id}]";
                        @endphp
{{--                        {{$loan_link_id}}--}}
{{--                        {{$fieldKey}}--}}
                        {!! Form::select(
                            $fieldKey,
                            $loanOffersOptions,
                            old($fieldKey,$loan_link_id),
                            ['class' => 'form-control']
                        ) !!}
                    </td>
                @endforeach
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
