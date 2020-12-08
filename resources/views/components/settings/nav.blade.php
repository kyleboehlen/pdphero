<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />

    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" />

    <ul class="list settings">
        <a href="#todo-settings-header"><li>To-Do</li></a>

        <form id="default-settings-form" action="{{ route('profile.destroy.settings') }}" method="POST">
            @csrf
        </form>
        <a href="{{ route('profile.destroy.settings') }}" class="destructive-option"
            onclick="event.preventDefault(); document.getElementById('default-settings-form').submit();">
            <li>Default Settings</li>
        </a>
    </ul>
</nav>