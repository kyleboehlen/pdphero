<nav>
    {{-- Logo --}}
    <img class="close" src="{{ asset('icons/close.png') }}" />
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" />

    {{-- Nav ul --}}
    <ul>
        {{-- Switch Nav li elements based on page --}}
        @switch($page) 
            @case('about')
                <a href="#about"><li>About</li></a>
                <a href="#features"><li>Features</li></a>
                <a href="#why"><li>Why?</li></a>
                <a href="#pricing"> <li>Pricing</li></a>
                {{-- Change to tutorials --}}
                <a href="#tutorials"><li>Tutorials</li></a>
                {{-- Change to FAQs page --}}
                <a href="#faqs"><li>FAQs</li></a>
                @break
        @endswitch
    </ul>
</nav>