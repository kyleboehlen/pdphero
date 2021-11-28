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
            <img class="left" src="{{ asset('icons/smile-white.png') }}" />
            <p>When was the last time you lost motivation to complete a goal because it didn't feel like you were making any progress?</p>
            <p>PDPHero, the all-in-one personal development plan app, to the rescue! The best way to track S.M.A.R.T. goals, accomplish to-do items, write journal entries, create long term habits, and break addictions.</p>
            <p>Not only are all the tools you need in one app, but all the features of PDPHero are tightly coupled and integrated to make personal development planning a breeze.</p>
        </div>

        {{-- Features --}}
        <div class="about-card right">
            <div class="anchor" id="features"></div>
            <h2>Goals</h2>
            <img class="right" src="{{ asset('icons/goals-white.png') }}" />
            <p>Whether you know all the steps to reach your goal or you need to plan it ad-hoc as you go we have a goal type that will track your goal.</p>
            <p>Even if you want to base it off a habit, keep track of a goal you want to accomplish in the future, manually track your progress, or a combonation of everything!</p>
        </div>

        <div class="about-card left">
            <h2>Habits</h2>
            <img class="right" src="{{ asset('icons/habits-white.png') }}" />
            <p>Our habit tracker allows you to track your habit any way you want. Whether it's once a day, or 10 times. Our habit tracker will work for you!</p>
            <p>Doesn't matter if it's certain days of the week, or every so many days.</p>
            <p>Whether it's once a day, or 10 times, our habit tracker will work for you!</p>
        </div>

        <div class="about-card right">
            <h2>Journaling</h2>
            <img class="right" src="{{ asset('icons/journal-white.png') }}" />
            <p>Our journaling tool automatically creates a daily timeline of all the todo items, habits, goals, and bucketlist items you've accomplished that day.</p>
            <p>You can add your own journal entries and use search functionality across all your entries.</p>
        </div>

        <div class="about-card left">
            <h2>Todos</h2>
            <img class="right" src="{{ asset('icons/todo-white.png') }}" />
            <p>While you can create your own todo items, color coded by priority, our todo list will automatically add any habits and goal action items you need to accomplish that day as well.</p>
            <p>It can even remind you to journal and read your affirmations.</p>
            <p>You can set up todo reminders via SMS, email, and push notifications.</p>
        </div>

        <div class="about-card right">
            <h2>Profile</h2>
            <img class="right" src="{{ asset('icons/profile-white.png') }}" />
            <p>You can customize your profile with your values and a nutshell to make sure you never lose track of the big picture.</p>
            <p>You can also see the number of habits, goals, journal entries, and todos you've accomplished.</p>
            <p>You can even create your own daily affirmations!</p>
        </div>

        <div class="about-card left">
            <h2>Bucketlist</h2>
            <img class="right" src="{{ asset('icons/bucketlist-white.png') }}" />
            <p>Goals shouldn't just be about work, you should make sure you get in all those fun things that you've always wanted to do as well.</p>
            <p>Time to stop dreaming and start doing!!</p>
        </div>

        <div class="about-card right">
            <h2>Addictions</h2>
            <img class="right" src="{{ asset('icons/addiction-white.png') }}" />
            <p>We realize that sometimes personal improvement isn't always about creating new habits, sometimes it is about breaking old ones.</p>
            <p>Besides just tracking how long you've broken free from an addiction it also allows you to manage moderated usage if that fits your goals better.</p>
            <p>You can create custom milestones and attach a reward to each milestone, PDPHero will remind you when you reach your milestone and remind you to reward yourself!</p>
            <p>Relapse is unfortunately a part of breaking addictions, with our addiction tracker you can write relapse reports to learn from them and figure out how to improve next time.</p>
        </div>

        {{-- Pricing --}}
        <div class="about-card left">
            <div class="anchor" id="pricing"></div>
            <h2>Pricing</h2>
            <div class="pricing-container">
                <h3>All memberships come with a {{ config('membership.trial_length') }} day free trial, no credit card required!</h3>
                
                <div class="pricing-item">
                    <img class="pricing" src="{{ asset('logos/logo-white.png') }}" />

                    <h4>Basic Membership</h4>

                    <p class="pricing-label">${{ config('membership.basic.price') }} monthly</p>

                    <p>Comes with all the features you need for creating smart goals, managing to-do items, building habits, journaling, and personal development planning. Includes {{ config('sms.basic_limit') }} SMS reminders monthly.</p>
                </div>

                <div class="pricing-item">
                    <img class="pricing" src="{{ asset('logos/logo-black.png') }}" />

                    <h4>Black Label Membership</h4>

                    <p class="pricing-label">${{ config('membership.black_label.price') }} monthly</p>

                    <p>Includes an SMS limit of {{ config('sms.black_label_limit') }} reminders per month. Allows you to vote for which features are built next. A great way to support the app if you feel so inclined :)</p>
                </div>
            </div>
        </div>
    </div>

    @if(config('about.show_social_footer'))
        <x-about.footer />
    @endif
@endsection