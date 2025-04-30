@extends('layouts.app')
@section('head_scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
@endsection
@section('header')
    <div class="hidden lg:flex flex-row gap-6 text-sm">
        <a href="#why-us">
            <span class="cursor-pointer">Почему мы?</span>
        </a>
        <a href="#how-to">
            <span class="cursor-pointer">Как оформить заём</span>
        </a>
        <a href="#money-to">
            <span class="cursor-pointer">Способы получения</span>
        </a>
    </div>
@endsection
@section('content')
    <h1>ctr</h1>
@endsection
@section('scripts')
    <script>
        frontConfig.loansUrl = @json(route('front.loans'));

        document.addEventListener('click', function (event) {
            if (event.target.closest('.money-btn')) {
                const button = event.target.closest('.money-btn');
                const wrapper = button.closest('.get-money-wrapper');
                @if(auth()->check())
                // console.log("Пользователь авторизован");
                const dashboardUrl = @json(route('vitrina'));
                // console.log("dashboardUrl", dashboardUrl);
                window.location.href = dashboardUrl;
                return;
                @endif

                ym(99015882, 'reachGoal', 'go_to_step_register');

                if (wrapper) {
                    const block = wrapper.previousElementSibling;

                    if (block && block.classList.contains('money-slider-container')) {
                        const amountLabel = block.querySelector('.amountLabel');
                        const amountText = amountLabel.textContent.replace(/\D/g, ''); // Оставляем только цифры

                        const daysLabel = block.querySelector('.daysLabel');
                        const daysText = daysLabel.textContent.replace(/\D/g, ''); // Оставляем только цифры

                        @if($redirectMainPage)
                        window.open(`/register?amount=${amountText}&days=${daysText}`);
                        redirect(event, '{{route('public.vitrina')}}');
                        @else
                            window.location.href = `/register?amount=${amountText}&days=${daysText}`;
                        @endif

                        console.log("Найденное значение amountLabel:", amountText);
                        console.log("Найденное значение daysLabel:", daysText);
                    } else {
                        console.warn("Не удалось найти блок money-slider-container перед оберткой");
                    }
                } else {
                    @if($redirectMainPage)
                    window.open('/register');
                    redirect(event, '{{route('public.vitrina')}}');
                    @else
                        window.location.href = '/register';
                    @endif

                    console.warn("Не удалось найти обертку кнопки get-money-wrapper");
                }
            }
        });
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
