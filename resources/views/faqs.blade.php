@extends('layouts.app')

@section('template')
    {{-- About Header --}}
    <x-about.header />

    {{-- Side Nav --}}
    <x-about.nav />

    <div class="container">
        <div class="about-card faqs">
            @foreach($faqs as $faq)
                <h2 class="question">{{ $faq->question }}</h2>
                <p>{{ $faq->answer }}</p>
                <br/><br/>
            @endforeach
            <br/><br/>
        </div>
    </div>

    @if(config('about.show_social_footer'))
        <x-about.footer />
    @endif
@endsection