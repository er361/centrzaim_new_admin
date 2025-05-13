@extends('layouts.app')
@section('content')
    <div class="banner_suke">
        {!! App\Services\BannerService\BannerService::get('unsub_3') !!}
    </div>
@endsection


