<footer class="social">
    <div class="links-container">
        @foreach(config('socials') as $social)
            <a href="{{ \buildSocialUrl($social) }}">
                <img src="{{ asset('icons/' . $social['icon_name'] . '-white' . $social['icon_type']) }}" />
            </a>
        @endforeach
    </div>
</footer>