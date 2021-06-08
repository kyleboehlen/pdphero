@extends('layouts.app')

@section('template')
    {{-- Push JS --}}
    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>

        <script>
            const stripe = Stripe('stripe-public-key');
        </script>
    @endpush

    {{-- Header --}}
    <x-app.header title="Subscribe" />

    {{-- Side Nav --}}
    <x-stripe.nav />

    <div class="app-container">
        <div class="subscription-selector">
            <h2>Select Subscription</h2>
            <h3 class="trial-days-left">You have <span class="{{ $trial_days_left > 0 ? 'green' : 'red' }}">{{ $trial_days_left }}</span> days of your free trial left</h3>
            <br/>

            <select id="subscription-select">
                <option value="basic">PDPHero Basic</option>
                <option value="black-label">PDPHero Black Label</option>
            </select>

            <p class="sub-desc basic">Comes with all the features you need for creating smart goals, managing to-do items, building habits, journaling, and personal development planning.</p>
            <p class="sub-desc black-label">Allows you to vote for which features are built next. A great way to support the app if you feel so inclined :)</p>

            {{-- Subscribe buttons --}}
            {{ $basic_checkout->button('Subscribe', ['class' => 'checkout-button basic']) }}
            {{ $black_label_checkout->button('Subscribe', ['class' => 'checkout-button black-label']) }}
        </div>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection