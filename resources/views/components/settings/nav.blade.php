<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />

    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" />

    <ul class="list settings">
        <a class="close-nav" href="#affirmations-settings-header"><li>Affirmations</li></a>
        <a class="close-nav" href="#habits-settings-header"><li>Habits</li></a>
        <a class="close-nav" href="#profile-settings-header"><li>Profile</li></a>
        <a class="close-nav" href="#todo-settings-header"><li>To-Do</li></a>

        <form id="default-settings-form" action="{{ route('profile.destroy.settings') }}" method="POST">
            @csrf
        </form>
        <a href="{{ route('profile.destroy.settings') }}" class="destructive-option"
            onclick="event.preventDefault(); document.getElementById('default-settings-form').submit();">
            <li>Default Settings</li>
        </a>
    </ul>
</nav>