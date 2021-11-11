@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Addictions" />

    {{-- Side Nav --}}
    <x-addictions.nav show="back" :addiction="$addiction" />

    <div class="app-container">
        <form class="relapse" method="POST" action="{{ route('addiction.relapse.store', ['addiction' => $addiction->uuid]) }}">
            @csrf
        
            {{-- Header --}}
            <h2>Mark Relapse</h2>

            <p>Remember that relapses do not mean you've failed, they are part of the recovery process. You got this!</p>
        
            <textarea name="notes" rows="10"
                {{-- Trust me, I know, I don't wanna talk about it. That being said, don't fuck w/ the spacing here --}}
                placeholder="Was there an event that triggered the relapse?
                
How did you feel before and after your relapse?

Are there any steps you can take to prevent a similar relapse in the future?"></textarea>
        
            @error('notes')
                <p class="error">{{ $message }}</p>
            @enderror
        
            <a href="{{ route('addiction.details', ['addiction' => $addiction->uuid]) }}">
                <button class="cancel" type="button">Cancel</button>
            </a>
        
            <button class="submit" type="submit">Submit</button>
        </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection