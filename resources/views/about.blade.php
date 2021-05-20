@extends('layouts.app')

@section('template')
    {{-- About Header --}}
    <x-about.header />

    {{-- Side Nav --}}
    <x-about.nav />

    <div class="container">
        {{-- About --}}
        <div class="about-card left top">
            <div class="anchor" id="about"></div>
            <h2>About</h2>
            <img class="left" src="{{ asset('images/about.jpg') }}" />
            <p>When was the last time you lost motivation to complete a goal because it didn't feel like you were making any progress?</p>
            <p>PDPHero, the all-in-one personal development plan app, to the rescue! The best way to track S.M.A.R.T. goals, accomplish to-do items, write journal entries, and create long term habits.</p>
            <p>Not only are all the tools you need in one app, but all the features of PDPHero are tightly coupled and integrated to make personal development planning a breeze</p>
        </div>

        {{-- Features --}}
        <div class="about-card right">
            <div class="anchor" id="features"></div>
            <h2>Features</h2>
            <img class="right" src="{{ asset('images/features.jpg') }}" />
            <ul>
                <li>Flexible habit tracking with accurate strength indication</li>
                <li>Various S.M.A.R.T. goal tracking types to suit any of your goal needs</li>
                <li>Integration with YNAB for financial based goals</li>
                <li>A personal profile so you don't lose sight of your personal values and rules</li>
                <li>An affirmations system</li>
                <li>Compadibliity with all devices</li>
                <li>A To-Do list that automatically updates with your habits and action items</li>
            </ul>
        </div>

        {{-- Why? --}}
        <div class="about-card left">
            <div class="anchor" id="why"></div>
            <h2>Why?</h2>
            <img class="right" src="{{ asset('images/why.jpg') }}" />
            <p>While working on several personal goals and software projects myself I noticed I really needed a better way of tracking everything I was trying to achieve.</p>
            <p>Unfortunately most of the personal development software available was designed for coporations, and the only S.M.A.R.T. goal software available lacked features and integrations I desperatly wanted. A lot of people use paper soltutions, but it makes too hard to adjust goal details or automatically calculate progress.</p>
            <p>For the last couple of years I've had my personal development plan spread out between 3-4 different apps, and I decided it was time to create one piece of software to manage it all.</p>
        </div>

        {{-- Pricing --}}
        <div class="about-card right">
            <div class="anchor" id="pricing"></div>
            <h2>Pricing</h2>
            <div class="pricing-container">
                <h3>All memberships come with a {{ config('membership.trial_length') }} day free trial, no credit card required!</h3>
                
                <div class="pricing-item">
                    <img class="pricing" src="{{ asset('logos/logo-white.png') }}" />

                    <h4>Basic Membership</h4>

                    <p class="pricing-label">${{ config('membership.basic_price') }} monthly</p>

                    <p>Comes with all the features you need for creating smart goals, managing to-do items, building habits, journaling, and personal development planning.</p>
                </div>

                <div class="pricing-item">
                    <img class="pricing" src="{{ asset('logos/logo-black.png') }}" />

                    <h4>Black Label Membership</h4>

                    <p class="pricing-label">${{ config('membership.black_label_price') }} monthly</p>

                    <p>Also includes YNAB integration for financial goals, and access to vote for which features are built next. A great way to support the app :)</p>
                </div>
            </div>
        </div>
    </div>

    @if(config('about.show_social_footer'))
        <x-about.footer />
    @endif
@endsection