@props([
    'text' => 'личный кабинет',
    'route' => route('sms.login'),
    'html' => false,
    'target' => '_self'
])
<a href="{{$route}}" target="{{$target}}">
    @if($html)
        {{$html}}
    @else
        <b>{{$text}}</b>
    @endif

</a>
