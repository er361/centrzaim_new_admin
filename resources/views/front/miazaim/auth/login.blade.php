@extends('layouts.app')

@section('content')
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="w-full max-w-lg p-6 bg-white rounded-lg shadow-md">
            <section class="unsubscribe">
                <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Личный кабинет</h1>

                <form id="regForm" method="POST" action="{{ route('auth.login.store') }}" class="space-y-6">
                    <input type="hidden" name="remember" value="1"/>
                    @if($showPassword)
                        <input type="hidden" name="force_password" value="1"/>
                    @endif
                    {{ csrf_field() }}

                    @if (count($errors) > 0)
                        <div class="p-4 mb-4 text-red-800 bg-red-100 rounded-lg">
                            <p class="font-semibold">Произошла ошибка:</p>
                            <ul class="mt-2 list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <input
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Ваш e-mail"
                                required
                        >

                        @if($showPassword)
                            <input
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Пароль"
                                    required
                            >
                        @endif
                    </div>

                    <div class="flex justify-center">
                        <button
                                type="submit"
                                class="px-6 py-3 bg-blue text-white font-semibold rounded-lg transition"
                        >
                            Войти
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection
