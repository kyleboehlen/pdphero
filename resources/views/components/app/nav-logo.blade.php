@if($user->subscribed(config('membership.black_label.slug')))
    <img class="logo" src="{{ asset('logos/logo-black.png') }}" onclick="location.href='{{ route('home') }}'"/>
@else
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" onclick="location.href='{{ route('home') }}'"/>
@endif