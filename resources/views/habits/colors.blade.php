@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Habits" />

    {{-- Side Nav --}}
    <x-habits.nav show="back" />

    <div class="app-container">
        <div class="color-guide">
            <div class="color-key">
                <div class="toggle-history not-required"></div>
                <div class="toggle-history skipped not-required"></div>
                <div class="toggle-history partial not-required"></div>
                <div class="toggle-history completed not-required"></div><br/>
                Gradient colors means you are/were not required to do the habit that day.
            </div>

            <div class="color-key">
                <div class="toggle-history"></div>
                <div class="toggle-history skipped"></div>
                <div class="toggle-history missed"></div>
                <div class="toggle-history partial"></div>
                <div class="toggle-history completed"></div><br/>
                Solid colors mean you are/were required to complete the habit that day.
            </div>

            <div class="color-key">
                <div class="toggle-history"></div>
                <div class="toggle-history not-required"></div><br/>
                Grey means activity has not been recorded for that habit on that day yet. You'll see this show up today and future days.
            </div>

            <div class="color-key">
                <div class="toggle-history skipped not-required"></div>
                <div class="toggle-history skipped"></div><br/>
                Brown means that day was skipped. Either it wasn't required, or it was required that day and was explicitly skipped. Skipped days do not count against habit strength, but if you skip a required day you must explain why you did! For example: your habit is to make your bed everyday but you're hammock camping and you don't have a bed to make. 
            </div>

            <div class="color-key">
                <div class="toggle-history missed"></div><br/>
                Red means that day was required and missed. You can not miss a day that was not required. Missed days will count against your habit strength.
            </div>

            <div class="color-key">
                <div class="toggle-history partial not-required"></div>
                <div class="toggle-history partial"></div><br/>
                Yellow means the habit was partially completed that day. For example: the habit is required to be completed tree times daily and was only completed twice.
            </div>

            <div class="color-key">
                <div class="toggle-history completed not-required"></div>
                <div class="toggle-history completed"></div><br/>
                Green means that the habit was successfully completed as many times as it needed to be that day, good job! You can complete habits on days that are not required but it will not count towards your habit strength.
            </div>
        </div>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="habits" />
@endsection