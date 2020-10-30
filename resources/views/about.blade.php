@extends('layouts.app')

@section('template')
    {{-- Main Header --}}
    <x-main-header />

    {{-- Side Nav --}}
    <x-main-nav page="about" />

    {{-- About --}}
    <div class="about-card left top">
        <div class="anchor" id="about"></div>
        <h2>About</h2>
        <img class="left" src="{{ asset('images/about.jpg') }}" />
        <p>When was the last time you lost motivation to complete a goal because it didn't feel like you were making any progress?</p>
        <p>PDPHero, the all-in-one personal development plan app, to the rescue! The best way to track S.M.A.R.T. goals, action items, and create long term habits.</p>
    </div>

    {{-- Features --}}
    <div class="about-card right">
        <div class="anchor" id="features"></div>
        <h2>Features</h2>
        <img class="right" src="{{ asset('images/features.jpg') }}" />
        <ul>
            <li>S.M.A.R.T. Goal Tracking</li>
            <li>Habit Tracking with Strength Indicator</li>
            <li>Integration with YNAB for Financial Goals</li>
            <li>A Profile to List Personal Values</li>
            <li>Works on All Devices</li>
            <li>A To-Do List that Automatically Pulls In Habits and Action Items</li>
            <li>Distinguish Actionable To-Do Items From Dependent To-Do Items</li>
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

                <p>Comes with all the features you need for creating smart goals, managing action plans, building habits, and personal development planning.</p>
            </div>

            <div class="pricing-item">
                <img class="pricing" src="{{ asset('logos/logo-black.png') }}" />

                <h4>Black Label</h4>

                <p class="pricing-label">${{ config('membership.black_label_price') }} monthly</p>

                <p>Also includes YNAB integration for financial goals, and access to submit and vote on new features. Guaranteed to include any additional features.</p>
            </div>
        </div>
    </div>

    <x-main-footer />
@endsection