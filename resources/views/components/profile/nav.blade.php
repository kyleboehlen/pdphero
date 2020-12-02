<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />

    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" />

    <ul class="list">
        @if(in_array('back', $show))
            <a href="{{ route('profile') }}"><li>Back To Profile</li></a>
        @endif

        @if(in_array('edit-name', $show))
            <a href="{{ route('profile.edit.name') }}"><li>Edit Name</li></a>
        @endif

        {{-- ToDo: this could be changed to link to profile.edit.profile-picture where a cropping form could be added --}}
        @if(in_array('edit-picture', $show))
            <form id="profile-picture-form" action="{{ route('profile.update.picture') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input id="profile-picture-input" type="file" name="profile-picture" accept=".png,.jpg,.jpeg" required />
            </form>
            <a href="{{ route('profile.update.picture') }}"
                onclick="event.preventDefault(); document.getElementById('profile-picture-input').click();">
                <li>Change Profile Picture</li>
            </a>
        @endif

        @if(in_array('edit-values', $show))
            <a href="{{ route('profile.edit.values') }}"><li>Edit Values</li></a>
        @endif

        @if(in_array('edit-nutshell', $show))
            <a href="{{ route('profile.edit.nutshell') }}"><li>Edit Nutshell</li></a>
        @endif

        @if(in_array('edit-membership', $show))
            <a href="{{ route('profile.edit.memebership') }}"><li>Manage Membership</li></a>
        @endif

        {{-- To-Do: View stats option if user has black label memebership --}}

        @if(in_array('log-out', $show))
            <form id="log-out-form" action="{{ route('logout') }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('logout') }}" class="destructive-option"
                onclick="event.preventDefault(); document.getElementById('log-out-form').submit();">
                <li>Log Out</li>
            </a>
        @endif 
    </ul>
</nav>