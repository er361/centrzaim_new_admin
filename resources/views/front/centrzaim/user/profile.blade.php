@extends('layouts.app')
@section('content')
    <div class="bg-gray-bg">
        <div class="container lg:px-8 py-10">
            <x-profile :profile="$data['profile']" />
            <x-passport :passport="$data['passport']"/>
        </div>
    </div>
    <x-report/>
    @include('user.blocks.info', ['data' => $data])
    @include('user.blocks.vitrina', ['showcaseLoansEntity' => $sourceShowcaseLoansEntity])
@endsection
