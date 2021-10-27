<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />

    {{-- Logo --}}
    <x-app.nav-logo />

    <ul class="list settings">
        <a class="close-nav" href="#addictions-settings-header"><li>Addictions</li></a>
        <a class="close-nav" href="#affirmations-settings-header"><li>Affirmations</li></a>
        <a class="close-nav" href="#bucketlist-settings-header"><li>Bucketlist</li></a>
        <a class="close-nav" href="#general-settings-header"><li>General</li></a>
        <a class="close-nav" href="#goals-settings-header"><li>Goals</li></a>
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