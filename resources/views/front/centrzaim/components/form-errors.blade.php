{{--                    @php($errors = collect(['some','eror']))--}}
@props(['errors' => []])
@if(count($errors) > 0)
    <div {{ $attributes->class(['border border-red text-red px-4 py-3 rounded relative']) }}
         role="alert">
        <strong class="font-bold">Произошла ошибка:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif