@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Addictions" />

    {{-- Side Nav --}}
    <x-addictions.nav show="back" :addiction="$addiction" />

    <div class="app-container">
        <form class="create-milestone" method="POST" action="{{ route('addiction.milestone.store', ['addiction' => $addiction->uuid]) }}">
        
            @csrf
        
            {{-- Header --}}
            <h2>Create Milestone</h2>
        
            <input type="text" name="name" placeholder="Milestone Name" maxlength="255" value="{{ old('name') }}" required />
        
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
            <br/><br/>
        
            <input class="milestone-amount" name="milestone-amount" type="number" min="1"
                value="{{ old('moderation-period') ?? 1 }}">
            <select name="milestone-date-format">
                @foreach($date_formats as $value => $format)
                    <option value="{{ $value }}"
                        @if(old('moderation-period') == $value)
                            selected
                        @endif
                    >{{ $format['name'] }}</option>
                @endforeach
            </select>
        
            @error('milestone-amount')
                <p class="error">{{ $message }}</p>
            @enderror
            <br/>
        
            @error('milestone-date-format')
                <p class="error">{{ $message }}</p>
            @enderror
            <br/>
        
            <textarea name="reward" placeholder="What are you going to reward yourself with when you hit this milestone?"
                >{{ old('details') }}</textarea>
        
            @error('reward')
                <p class="error">{{ $message }}</p>
            @enderror
        
            <a href="{{ route('addiction.milestones', ['addiction' => $addiction]) }}">
                <button class="cancel" type="button">Cancel</button>
            </a>
        
            <button class="submit" type="submit">Submit</button>
        </form>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection