<table class="table table-bordered table-striped">
    <thead class="thead-light">
    <tr>
        <th>
            ПП пользователя / Витрина
        </th>
        @foreach($showcases as $showcase)
            <th>{{ $showcase->name }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($sources as $source)
        <tr>
            <th scope="row">{{ $source->name }}</th>
            @foreach($showcases as $showcase)
                @php($fieldKey = "loan_offers[{$source->id}][{$showcase->id}]")
                <td>
                    {!! Form::select($fieldKey, $loanOffersOptions,
old($fieldKey, $loanOffers
->get($source->id)
?->get($showcase->id)
?->first()
?->loan_link_id), ['class' => 'form-control']) !!}
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>