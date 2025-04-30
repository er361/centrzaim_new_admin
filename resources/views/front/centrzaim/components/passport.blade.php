@props(['passport' => []])
<div class="sm:grid grid-cols-3 flex flex-col gap-4">
    <div class="bg-white p-3">
        {{ $passport['title'] ?? '' }}
    </div>
    <div class="bg-white p-3">
        {{ $passport['date'] ?? '' }}
    </div>
    <div class="bg-white p-3">
        {{ $passport['code'] ?? '' }}
    </div>

    <div class="bg-white p-3">
        {{$passport['reg_address']}}
    </div>
    <div class="bg-white p-3">
        {{$passport['fact_address']}}
    </div>
</div>