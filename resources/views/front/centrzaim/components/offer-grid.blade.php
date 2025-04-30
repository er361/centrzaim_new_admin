<div class="grid 2xl:grid-cols-4 md:grid-cols-3 grid-cols-2 sm:gap-4 gap-3">
    @php
        $counter = 0;
        $bannerShown = false;
    @endphp

    @foreach($data as $offer)
        @php $counter++; @endphp

        @if(url()->current() === route('vitrina'))
            {{-- Вставляем баннер после 2-й строки (8 элементов при 4 колонках, 6 при 3 колонках, 4 при 2 колонках) --}}
            @if($counter == 9 || ($counter == 7 && request()->is('*/md')) || ($counter == 5 && request()->is('*/sm')))
                <div class="col-span-full banner-container w-full">
                    {!! \App\Services\BannerService\BannerService::get('vitrina') !!}
                </div>
                @php $bannerShown = true; @endphp
            @endif
        @endif

        <x-offer_item
                :logo="$offer['logo']"
                :siteName="$offer['siteName']"
                :rating="$offer['rating']"
                :sum="$offer['sum']"
                :percent="$offer['percent']"
                :duration="$offer['duration']"
                :license="$offer['license']"
                :offerUrl="$offer['offerUrl']"
        />
    @endforeach

    @if(url()->current() === route('vitrina') && !$bannerShown && count($data) > 0)
        <div class="col-span-full banner-container w-full">
            {!! \App\Services\BannerService\BannerService::get('vitrina') !!}
        </div>
    @endif
</div>