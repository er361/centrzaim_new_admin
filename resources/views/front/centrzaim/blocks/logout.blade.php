@props(['class' => ''])
<form method="POST" action= "{{route('auth.logout')}}">
    @csrf
    <button type="submit" class="rounded px-6 py-2 bg-black-text text-white {{$class}}">Выйти</button>
</form>