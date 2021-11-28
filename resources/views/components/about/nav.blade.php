<nav class="about">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" onclick="location.href='{{ route('about') }}'"/>

    {{-- Nav ul --}}
    <ul>
        <a class="close-nav" href="{{ route('about') }}#features"><li>Features</li></a>
        <a class="close-nav" href="{{ route('about') }}#pricing"><li>Pricing</li></a>
        <a href="#tutorials"><li>Tutorials</li></a>
        <a href="{{ route('faqs') }}"><li>FAQs</li></a>
        <a href="{{ route('privacy') }}"><li>Privacy</li></a>
    </ul>
</nav>