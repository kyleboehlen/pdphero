<footer class="social">
    @foreach(config('socials') as $social)
        <a href="{{ \buildSocialUrl($social) }}">
            <img class="hover-white" src="{{ asset('icons/' . $social['icon_name'] . '-white' . $social['icon_type']) }}" />
        </a>
    @endforeach
</footer>